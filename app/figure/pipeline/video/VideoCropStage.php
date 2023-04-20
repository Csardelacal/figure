<?php namespace app\figure\pipeline\video;

use app\figure\pipeline\video\VideoPipelineContext;

class VideoCropStage implements VideoPipelineStage
{
	
	private ?int $width;
	private ?int $height;
	
	public function __construct(int $width = null, int $height = null)
	{
		$this->width = $width;
		$this->height = $height;
		
		assume(!($width === null && $height === null), 'Invalid dimensions');
	}
	
	public function run(VideoPipelineContext $ctx): VideoPipelineContext
	{
		
		if ($ctx->getWidth() < $this->width && $ctx->getHeight() < $this->height) {
			return $ctx;
		}
		
		$surrogate = new VideoCoverStage($this->width, $this->height);
		return $surrogate->run($ctx);
	}
}
