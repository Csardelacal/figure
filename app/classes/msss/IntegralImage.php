<?php namespace app\classes\msss;

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
class IntegralImage
{
	
	private RawImage $raw;
	
	/**
	 *
	 * @param RawImage $img
	 * @param int $width
	 * @param int $height
	 */
	public function __construct(RawImage $img, int $width, int $height)
	{
		// Initialize
		$out = array_fill(0, $height, array_fill(0, $width, 0));
		
		for ($j = 0; $j < $height; $j++) {
			$sumRow = 0;
			
			for ($k = 0; $k < $width; $k++) {
				$sumRow += $img->img[$k + $j * $width];
				
				if (0 == $j) {
					$out[$j][$k] = $sumRow;
				}
				else {
					$out[$j][$k] = $out[$j - 1][$k] + $sumRow;
				}
			}
		}
		
		$this->raw = RawImage::fromArray($out, $width, $height);
	}
	
	public function sum(int $x1, int $y1, int $x2, int $y2)
	{
		$sum = 0;
		
		if ($x1 - 1 < 0 && $y1 - 1 < 0) {
			$sum = $this->raw->img[$y2][$x2];
		}
		elseif ($x1 - 1 < 0) {
			$sum = $this->raw->img[$y2][$x2] - $this->raw->img[$y1 - 1][$x2];
		}
		elseif ($y1 - 1 < 0) {
			$sum = $this->raw->img[$y2][$x2] - $this->raw->img[$y2][$x1 - 1];
		}
		else {
			$sum = $this->raw->img[$y2][$x2] + $this->raw->img[$y1 - 1][$x1 - 1]
			- $this->raw->img[$y1 - 1][$x2] - $this->raw->img[$y2][$x1 - 1];
		}
		
		return $sum;
	}
	
	public function toArray()
	{
		return $this->img;
	}
}
