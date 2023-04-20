<?php 

$img = imagecreatefromjpeg('./browsertest.jpg');
$img2 = imagecreatetruecolor(imagesx($img), imagesy($img));

imagecopy($img2, $img, 0, 0, 0, 0, imagesx($img), imagesy($img));
imagejpeg($img2, 'output.jpg');
