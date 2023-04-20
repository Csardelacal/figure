<?php

declare(strict_types=1);

use app\classes\msss\Gravity;
use app\classes\msss\IntegralImage;
use app\classes\msss\RawImage;
use app\classes\msss\SaliencyMap;

include '../vendor/autoload.php';


function main($num)
{
	$filename = __DIR__ . '/img' . $num . '.jpg';
	$mapfile  = __DIR__ . '/out' . $num . '.jpg';
	$img = imagecreatefromjpeg($filename);
	$h =  imagesy($img);
	$w =  (int)(imagesx($img) * $h / imagesy($img));
	$res = imagecreatetruecolor($w, $h);
	
	imagecopyresampled($res, $img, 0, 0, 0, 0, $w, $h, imagesx($img), imagesy($img));
	
	$sal = SaliencyMap::generate($res, $w, $h);
	
	[$xout, $yout] = Gravity::fromSaliencyMap($sal);
	
	imagesetpixel($sal, (int)($xout * imagesx($sal)), (int)($yout * imagesy($sal)), imagecolorallocate($sal, 255, 0, 0));
	imagepng($sal, $mapfile);
	
	echo sprintf('[%d, %d]', $xout * $w, $yout * $h), PHP_EOL;
}

$started  = microtime(true);

main($argv[1]);

echo number_format(microtime(true) - $started, 4), PHP_EOL;
