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
class SaliencyMap
{
	
	/**
	 *
	 * @var int[]
	 */
	public array $map;
	
	private int $width;
	private int $height;
	
	public function __construct(array $map, int $width, int $height)
	{
		$this->map = $map;
		$this->height = $height;
		$this->width = $width;
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
	 */
	public function normalize() : RawImage
	{
		$maxval = max($this->map);
		$minval = min($this->map);
		
		$sz = $this->width * $this->height;
		
		$range = $maxval - $minval;
		assert($range > 0);
		
		{
		for ($i = 0; $i < $sz; $i++) {
			$salmap[$i] = ((255.0 * ($this->map[$i] - $minval)) / $range);
		}
		}
		
		return RawImage::fromArray($salmap, $this->width, $this->height);
	}
	
	public static function generate(GdImage $img)
	{
		$width = imagesx($img);
		$height = imagesy($img);
		
		$sz = $width * $height;
		$salmap = array_fill(0, $sz, 0);
		
		[$lvec, $avec, $bvec] = RawImage::fromImage($img)->lab();
		
		$kernel = [1, 2, 1];
		$ls = $lvec->GaussianSmooth($kernel);
		$as = $avec->GaussianSmooth($kernel);
		$bs = $bvec->GaussianSmooth($kernel);
		
		$lint = new IntegralImage($lvec, $width, $height);
		$aint = new IntegralImage($avec, $width, $height);
		$bint = new IntegralImage($bvec, $width, $height);
		
		$ind = 0;
		
		for ($j = 0; $j < $height; $j++) {
			$yoff = min($j, $height - $j);
			$y1 = max($j - $yoff, 0);
			$y2 = min($j + $yoff, $height - 1);
			
			for ($k = 0; $k < $width; $k++) {
				$xoff = min($k, $width - $k);
				$x1 = max($k - $xoff, 0);
				$x2 = min($k + $xoff, $width - 1);
				
				$area = ($x2 - $x1 + 1) * ($y2 - $y1 + 1);
				
				$lval = $lint->sum($x1, $y1, $x2, $y2) / $area;
				$aval = $aint->sum($x1, $y1, $x2, $y2) / $area;
				$bval = $bint->sum($x1, $y1, $x2, $y2) / $area;
				
				//square of the euclidean distance
				$salmap[$ind] =  ($lval - $ls->img[$ind]) * ($lval - $ls->img[$ind])
				+ ($aval - $as->img[$ind]) * ($aval - $as->img[$ind])
				+ ($bval - $bs->img[$ind]) * ($bval - $bs->img[$ind]);
				//------
				$ind++;
				//------
			}
		}
		assert(count($salmap) === $width * $height);
		//----------------------------------------------------
		// Normalize the values to lie in the interval [0,255]
		//----------------------------------------------------
		return (new SaliencyMap($salmap, $width, $height))->normalize()->toImage();
	}
}
