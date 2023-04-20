<?php namespace app\figure\pipeline\video;

use spitfire\exceptions\user\ApplicationException;

class VideoCoverStage implements VideoPipelineStage
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
		
		if ($this->height === null && $this->width === null) {
			throw new ApplicationException();
		}
		
		$width = $this->width;
		$height = $this->height;
		
		if ($this->height === null) {
			$width = $this->width;
			$height = $ctx->getHeight() * $this->width / $ctx->getWidth();
		}
		
		if ($this->width === null) {
			$height = $this->height;
			$width = $ctx->getWidth() * $this->height / $ctx->getHeight();
		}
		
		$width = $width - $width % 2;
		$height = $height - $height % 2;
		
		$ratio = max($width / $ctx->getWidth(), $height / $ctx->getHeight());
		$_width = ($width / $ratio) - ($width / $ratio) % 2;
		$_height = ($height / $ratio) -  ($height / $ratio) % 2;
		
		$ctx
			->getResource()
			->filters()
			->custom(sprintf(
				'crop=%d:%d:%d:%d,scale=w=%d:h=%d',
				$_width,
				$_height,
				(int)(($ctx->getWidth() - $_width) / 2),
				(int)(($ctx->getHeight() - $_height) / 2),
				$width,
				$height
			));
		
		return $ctx;
	}
}
