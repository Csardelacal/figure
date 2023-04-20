<?php 

$image = new Imagick('./browsertest_lut.jpg');
$version = $image->getVersion();
$profile = 'http://www.color.org/sRGB_IEC61966-2-1_no_black_scaling.icc';

var_dump($version);

if ((is_array($version) === true) && (array_key_exists('versionString', $version) === true))
{
    $version = preg_replace('~ImageMagick ([^-]*).*~', '$1', $version['versionString']);

    if (is_file(sprintf('/usr/share/ImageMagick-%s/config/sRGB.icm', $version)) === true)
    {
        $profile = sprintf('/usr/share/ImageMagick-%s/config/sRGB.icm', $version);
    }
}

if (($srgb = file_get_contents($profile)) !== false)
{
    $image->profileImage('icc', $srgb);
    $image->setImageColorSpace(Imagick::COLORSPACE_SRGB);
}

$image->thumbnailImage(1024, 0);

$image->writeImage('./output.jpg');