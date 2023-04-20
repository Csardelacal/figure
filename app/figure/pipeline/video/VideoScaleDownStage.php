<?php namespace app\figure\pipeline\video;

use FFMpeg\Coordinate\Dimension;
use spitfire\exceptions\user\ApplicationException;

class VideoScaleDownStage implements VideoPipelineStage
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
		
		if ($this->height !== null) {
			$height = $this->height;
			$width = $ctx->getWidth() * $this->height / $ctx->getHeight();
		}
		
		elseif ($this->width !== null) {
			$width = $this->width;
			$height = $ctx->getHeight() * $this->width / $ctx->getWidth();
		}
		
		else {
			throw new ApplicationException('Invalid scaling specified', 2206161209);
		}
		
		if ($width > $ctx->getWidth()) {
			return $ctx;
		}
		
		$ctx->getResource()->filters()->resize(
			new Dimension(
				$width,
				$height -  $height % 2
			)
		);
		
		return $ctx;
	}
}
