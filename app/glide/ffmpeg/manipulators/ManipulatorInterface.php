<?php

namespace app\glide\ffmpeg\manipulators;

use FFMpeg\Media\Video;

interface ManipulatorInterface
{
    /**
     * Set the manipulation params.
     *
     * @param array $params The manipulation params.
     */
    public function setParams(array $params);

    /**
     * Perform the image manipulation.
     *
     * @param Video $image The source image.
     *
     * @return Video The manipulated image.
     */
    public function run(Video $video) : Video;
}
