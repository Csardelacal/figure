<?php

namespace app\glide\ffmpeg\manipulators;

use FFMpeg\Coordinate\Dimension;
use FFMpeg\FFProbe\DataMapping\Stream;
use FFMpeg\Filters\Video\ResizeFilter;
use FFMpeg\Media\Video;

/**
 * @property string      $dpr
 * @property string|null $fit
 * @property string      $h
 * @property string      $w
 */
class Size extends BaseManipulator
{
    /**
     * Maximum image size in pixels.
     *
     * @var int|null
     */
    protected $maxImageSize;

    /**
     * Create Size instance.
     *
     * @param int|null $maxImageSize Maximum image size in pixels.
     */
    public function __construct($maxImageSize = null)
    {
        $this->maxImageSize = $maxImageSize;
    }

    /**
     * Set the maximum image size.
     *
     * @param int|null Maximum image size in pixels.
     *
     * @return void
     */
    public function setMaxImageSize($maxImageSize)
    {
        $this->maxImageSize = $maxImageSize;
    }

    /**
     * Get the maximum image size.
     *
     * @return int|null Maximum image size in pixels.
     */
    public function getMaxImageSize()
    {
        return $this->maxImageSize;
    }

    /**
     * Perform size image manipulation.
     *
     * @param Video $video The source image.
     *
     * @return Video The manipulated image.
     */
    public function run(Video $video) : Video
    {
        $width = $this->getWidth();
        $height = $this->getHeight();
        $fit = $this->getFit();
        $dpr = $this->getDpr();
		
		$stream   = $video->getStreams()->videos()->first();
		assert($stream !== null);

        list($width, $height) = $this->resolveMissingDimensions($stream, $width, $height);
        list($width, $height) = $this->applyDpr($width, $height, $dpr);
        list($width, $height) = $this->limitImageSize($width, $height);

        if ((int) $width !== (int) $stream->getDimensions()->getWidth() || (int) $height !== (int) $stream->getDimensions()->getWidth()) {
            $image = $this->runResize($video, $fit, (int) $width, (int) $height);
        }

        return $image;
    }

    /**
     * Resolve width.
     *
     * @return int|null The resolved width.
     */
    public function getWidth()
    {
        if (!is_numeric($this->w)) {
            return;
        }

        if ($this->w <= 0) {
            return;
        }

        return (int) $this->w;
    }

    /**
     * Resolve height.
     *
     * @return int|null The resolved height.
     */
    public function getHeight()
    {
        if (!is_numeric($this->h)) {
            return;
        }

        if ($this->h <= 0) {
            return;
        }

        return (int) $this->h;
    }

    /**
     * Resolve fit.
     *
     * @return string The resolved fit.
     */
    public function getFit()
    {
        return 'contain';
    }

    /**
     * Resolve the device pixel ratio.
     *
     * @return float The device pixel ratio.
     */
    public function getDpr()
    {
        if (!is_numeric($this->dpr)) {
            return 1.0;
        }

        if ($this->dpr < 0 or $this->dpr > 8) {
            return 1.0;
        }

        return (float) $this->dpr;
    }

    /**
     * Resolve missing image dimensions.
     *
     * @param Stream    $stream  The source image.
     * @param int|null $width  The image width.
     * @param int|null $height The image height.
     *
     * @return int[] The resolved width and height.
     */
    public function resolveMissingDimensions(Stream $stream, $width, $height)
    {
        if (is_null($width) and is_null($height)) {
            $width = $stream->getDimensions()->getWidth();
            $height = $stream->getDimensions()->getHeight();
        }

        if (is_null($width) || is_null($height)) {
            $size = (new \Intervention\Image\Size($stream->getDimensions()->getWidth(), $stream->getDimensions()->getHeight()))
              ->resize($width, $height, function ($constraint) {
                  $constraint->aspectRatio();
              });

            $width = $size->getWidth();
            $height = $size->getHeight();
        }

        return [
            (int) $width,
            (int) $height,
        ];
    }

    /**
     * Apply the device pixel ratio.
     *
     * @param int   $width  The target image width.
     * @param int   $height The target image height.
     * @param float $dpr    The device pixel ratio.
     *
     * @return int[] The modified width and height.
     */
    public function applyDpr($width, $height, $dpr)
    {
        $width = $width * $dpr;
        $height = $height * $dpr;

        return [
            (int) round($width),
            (int) round($height),
        ];
    }

    /**
     * Limit image size to maximum allowed image size.
     *
     * @param int $width  The image width.
     * @param int $height The image height.
     *
     * @return int[] The limited width and height.
     */
    public function limitImageSize($width, $height)
    {
        if (null !== $this->maxImageSize) {
            $imageSize = $width * $height;

            if ($imageSize > $this->maxImageSize) {
                $width = $width / sqrt($imageSize / $this->maxImageSize);
                $height = $height / sqrt($imageSize / $this->maxImageSize);
            }
        }

        return [
            (int) $width,
            (int) $height,
        ];
    }

    /**
     * Perform resize image manipulation.
     *
     * @param Video  $image  The source image.
     * @param string $fit    The fit.
     * @param int    $width  The width.
     * @param int    $height The height.
     *
     * @return Video The manipulated Video.
     */
    public function runResize(Video $video, $fit, $width, $height) : Video
    {
        if ('contain' === $fit) {
            return $this->runContainResize($video, $width, $height);
		}

        return $video;
    }

    /**
     * Perform contain resize image manipulation.
     *
     * @param Video $image  The source image.
     * @param int   $width  The width.
     * @param int   $height The height.
     *
     * @return Video The manipulated image.
     */
    public function runContainResize(Video $stream, $width, $height) : Video
    {
		$width = $width - $width % 2;
		$height = $height - $height % 2;
		
		$stream
			->filters()
			->resize(
				new Dimension($width, $height),
				ResizeFilter::RESIZEMODE_INSET
			);
		
		return $stream;
    }
}
