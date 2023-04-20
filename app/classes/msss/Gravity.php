<?php namespace app\classes\msss;

use GdImage;

class Gravity
{
	
	const DEFINITION = 128;
	
	public static function fromSaliencyMap(GdImage $sal)
	{
		
		$map = imagecreatetruecolor(self::DEFINITION, self::DEFINITION);
		imagecopyresampled($map, $sal, 0, 0, 0, 0, self::DEFINITION, self::DEFINITION, imagesx($sal), imagesy($sal));
		imagefilter($sal, IMG_FILTER_CONTRAST, 255);
		
		$max = $xout = $yout = 0;
		$integral = new IntegralImage(RawImage::fromImage($map), self::DEFINITION, self::DEFINITION);
		for ($y = 0; $y < self::DEFINITION; $y++) {
			$yoff = 3;
			$y1 = max($y - $yoff, 0);
			$y2 = min($y + $yoff, self::DEFINITION - 1);
			
			for ($x = 0; $x < self::DEFINITION; $x++) {
				$xoff = 3;
				$x1 = max($x - $xoff, 0);
				$x2 = min($x + $xoff, self::DEFINITION - 1);
				$sum = $integral->sum($x1, $y1, $x2, $y2);
				
				if ($sum > $max) {
					$max = $sum;
					$yout = $y;
					$xout = $x;
				}
			}
		}
		
		return [$xout / self::DEFINITION, $yout / self::DEFINITION];
	}
}
