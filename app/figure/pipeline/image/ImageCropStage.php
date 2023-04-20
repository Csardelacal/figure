<?php namespace app\figure\pipeline\image;

use app\figure\pipeline\image\ImagePipelineContext;

class ImageCropStage implements ImagePipelineStage
{
	
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
		
		if ($ctx->getWidth() < $this->width && $ctx->getHeight() < $this->height) {
			return $ctx;
		}
		
		$surrogate = new ImageCoverStage($this->width, $this->height);
		return $surrogate->run($ctx);
	}
}
