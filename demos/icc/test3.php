<?php

require __DIR__ . '/../vendor/autoload.php';

use Jcupitt\Vips;

$image = Vips\Image::newFromFile('./browsertest.jpg');

echo 'width = ' . $image->width . "\n";

//$image = $image->reduce(1.0, 1.0, ["kernel" => Vips\Kernel::LINEAR]);
//var_dump($image->get('icc-profile-data'));
$image = $image->icc_transform('srgb');

$image->writeToFile('./output.jpg', ['Q' => 75]);