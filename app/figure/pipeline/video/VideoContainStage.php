<?php namespace app\figure\pipeline\video;

use app\figure\pipeline\image\ImagePipelineContext;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\Point;
use FFMpeg\Filters\Video\ResizeFilter;
use spitfire\exceptions\user\ApplicationException;

class VideoContainStage implements VideoPipelineStage
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
		
		$ctx
			->getResource()
			->filters()
			->resize(
				new Dimension($width, $height),
				ResizeFilter::RESIZEMODE_INSET
			);
		
		return $ctx;
	}
}
