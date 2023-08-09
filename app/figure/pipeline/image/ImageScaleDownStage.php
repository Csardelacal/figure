<?php namespace app\figure\pipeline\image;

use app\figure\pipeline\image\ImagePipelineContext;
use spitfire\exceptions\user\ApplicationException;

class ImageScaleDownStage implements ImagePipelineStage
{
	
	const MAX_PIXEL_SIZE = 9999;
	
	private ?int $width;
	private ?int $height;
	
	public function __construct(int $width = null, int $height = null)
	{
		$this->width = $width;
		$this->height = $height;
		
		assume(!($width === null && $height === null), 'Invalid dimensions');
	}
	
	public function run(ImagePipelineContext $ctx): ImagePipelineContext
	{
		
		if ($this->height !== null) {
			$height = (int)clamp(1, $this->height, self::MAX_PIXEL_SIZE);
			$width = (int)clamp(1, $ctx->getWidth() * $this->height / $ctx->getHeight(), self::MAX_PIXEL_SIZE);
		}
		
		elseif ($this->width !== null) {
			$width = (int)clamp(1, $this->width, self::MAX_PIXEL_SIZE);
			$height = (int)clamp(1, $ctx->getHeight() * $this->width / $ctx->getWidth(), self::MAX_PIXEL_SIZE);
		}
		
		else {
			throw new ApplicationException('Invalid scaling specified', 2206161209);
		}
		
		if ($width > $ctx->getWidth()) {
			return $ctx;
		}
		
		$img = imagecreatetruecolor($width, $height);
		imagecolortransparent($img, imagecolorallocatealpha($img, 0, 0, 0, 127));
		imagealphablending($img, false);
		imagesavealpha($img, true);
		imagecopyresampled($img, $ctx->getResource(), 0, 0, 0, 0, $width, $height, $ctx->getWidth(), $ctx->getHeight());
		
		return new ImagePipelineContext($img, $width, $height, 0);
	}
}
