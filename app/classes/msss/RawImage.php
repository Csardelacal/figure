<?php namespace app\classes\msss;

use GdImage;

/**
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * This is a PHP port of Technopagan's MSS Saliency
 * @see https://github.com/commishes/mss-saliency
 */
/**
 *
 */
class RawImage
{
	
	/**
	 *
	 * @var int[]
	 */
	public array $img;
	
	private int $width;
	private int $height;
	
	/**
	 * Extracts the raw color data from a gd image into an array. AFAIK
	 * there's no quick way to get all the image data in one fell swoop.
	 *
	 * @param GdImage
	 * @return RawImage
	 */
	public static function fromImage(GdImage $image) : RawImage
	{
		$width = imagesx($image);
		$height = imagesy($image);
		$arr = array_fill(0, $width * $height, 0);
		
		for ($y = 0; $y < $height; $y++) {
			for ($x = 0; $x < $width; $x++) {
				$arr[$x + $y * $width] = imagecolorat($image, $x, $y);
			}
		}
		
		$img = new self();
		$img->img = &$arr;
		$img->height = $height;
		$img->width = $width;
		
		return $img;
	}
	
	/**
	 * Extracts the raw color data from a gd image into an array. AFAIK
	 * there's no quick way to get all the image data in one fell swoop.
	 *
	 * @param int[] $arr
	 * @param int $width
	 * @param int $height
	 * @return RawImage
	 */
	public static function fromArray(array &$arr, int $width, int $height) : RawImage
	{
		
		$img = new self();
		$img->img = &$arr;
		$img->height = $height;
		$img->width = $width;
		
		return $img;
	}
	
	/**
	 *
	 * @param int[] $arr
	 * @param int $width
	 * @param int $height
	 * @return GdImage
	 */
	public function toImage() : GdImage
	{
		$img = imagecreatetruecolor($this->width, $this->height);
		
		for ($y = 0; $y < $this->height; $y++) {
			for ($x = 0; $x < $this->width; $x++) {
				imagesetpixel($img, $x, $y, (int)$this->img[$x + $y * $this->width]);
			}
		}
		
		return $img;
	}
	
	/**
	 *
	 * @return RawImage[]
	 */
	public function lab() : array
	{
		$sz = $this->width * $this->height;
		$lvec = $avec = $bvec = array_fill(0, $sz, 0);
		
		for ($j = 0; $j < $sz; $j++) {
			$sR = ($this->img[$j] >> 16) & 0xFF;
			$sG = ($this->img[$j] >>  8) & 0xFF;
			$sB = ($this->img[$j]      ) & 0xFF;
			
			//------------------------
			// sRGB to XYZ conversion
			// (D65 illuminant assumption)
			//------------------------
			$BR = $sR / 255.0;
			$BG = $sG / 255.0;
			$BB = $sB / 255.0;
			
			if ($BR <= 0.04045) {
				$r = $BR / 12.92;
			}
			else {
				$r = pow(($BR + 0.055) / 1.055, 2.4);
			}
			if ($BG <= 0.04045) {
				$g = $BG / 12.92;
			}
			else {
				$g = pow(($BG + 0.055) / 1.055, 2.4);
			}
			if ($BB <= 0.04045) {
				$b = $BB / 12.92;
			}
			else {
				$b = pow(($BB + 0.055) / 1.055, 2.4);
			}
			
			$X = $r * 0.4124564 + $g * 0.3575761 + $b * 0.1804375;
			$Y = $r * 0.2126729 + $g * 0.7151522 + $b * 0.0721750;
			$Z = $r * 0.0193339 + $g * 0.1191920 + $b * 0.9503041;
			//------------------------
			// XYZ to LAB conversion
			//------------------------
			$epsilon = 0.008856; //actual CIE standard
			$kappa = 903.3; //actual CIE standard
			
			$Xr = 0.950456; //reference white
			$Yr = 1.0; //reference white
			$Zr = 1.088754; //reference white
			
			$xr = $X / $Xr;
			$yr = $Y / $Yr;
			$zr = $Z / $Zr;
			
			if ($xr > $epsilon) {
				$fx = pow($xr, 1.0 / 3.0);
			} else {
				$fx = ($kappa * $xr + 16.0) / 116.0;
			}
			if ($yr > $epsilon) {
				$fy = pow($yr, 1.0 / 3.0);
			} else {
				$fy = ($kappa * $yr + 16.0) / 116.0;
			}
			if ($zr > $epsilon) {
				$fz = pow($zr, 1.0 / 3.0);
			} else {
				$fz = ($kappa * $zr + 16.0) / 116.0;
			}
			
			$lvec[$j] = 116.0 * $fy - 16.0;
			$avec[$j] = 500.0 * ($fx - $fy);
			$bvec[$j] = 200.0 * ($fy - $fz);
		}
		
		$_lvec = new self;
		$_avec = new self;
		$_bvec = new self;
		
		$_lvec->height = $_avec->height = $_bvec->height = $this->height;
		$_lvec->width  = $_avec->width  = $_bvec->width  = $this->width ;
		
		$_lvec->img = &$lvec;
		$_avec->img = &$avec;
		$_bvec->img = &$bvec;
		
		return [$_lvec, $_avec, $_bvec];
	}
	
	public function gaussianSmooth($kernel) : RawImage
	{
		$center =(int)(count($kernel) / 2);
		
		$sz = $this->width * $this->height;
		
		$smoothImg = $tempim = array_fill(0, $sz, 0);
		
		$rows = $this->height;
		$cols = $this->width;
		
		//--------------------------------------------------------------------------
		// Blur in the x direction.
		//---------------------------------------------------------------------------
		{
		$index = 0;
		
		for ($r = 0; $r < $rows; $r++) {
			for ($c = 0; $c < $cols; $c++) {
				$kernelsum = 0;
				$sum = 0;
				for ($cc = (-$center); $cc <= $center; $cc++) {
					if ((($c + $cc) >= 0) && (($c + $cc) < $cols)) {
						$sum += $this->img[$r * $cols + ($c + $cc)] * $kernel[$center + $cc];
						$kernelsum += $kernel[$center + $cc];
					}
				}
				$tempim[$index] = $sum / $kernelsum;
				$index++;
			}
		}
		}
		
		//--------------------------------------------------------------------------
		// Blur in the y direction.
		//---------------------------------------------------------------------------
		{
		$index = 0;
		for ($r = 0; $r < $rows; $r++) {
			for ($c = 0; $c < $cols; $c++) {
				$kernelsum = 0;
				$sum = 0;
				for ($rr = (-$center); $rr <= $center; $rr++) {
					if ((($r + $rr) >= 0) && (($r + $rr) < $rows)) {
						$sum += $tempim[($r + $rr) * $cols + $c] * $kernel[$center + $rr];
						$kernelsum += $kernel[$center + $rr];
					}
				}
				$smoothImg[$index] = $sum / $kernelsum;
				$index++;
			}
		}
		}
		
		$img = clone $this;
		$img->img = &$smoothImg;
		
		return $img;
	}
}
