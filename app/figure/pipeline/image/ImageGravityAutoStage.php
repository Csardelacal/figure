<?php namespace app\figure\pipeline\image;

use app\classes\msss\Gravity;
use app\classes\msss\SaliencyMap;
use app\figure\pipeline\image\ImagePipelineContext;

class ImageGravityAutoStage implements ImagePipelineStage
{
	
	public function run(ImagePipelineContext $ctx): ImagePipelineContext
	{
		/**
		 * @todo This should be moved somewhere where it belongs. We should
		 * not be setting ini in here.
		 */
		ini_set('memory_limit', '512M');
		
		[$x, $y] = Gravity::fromSaliencyMap(SaliencyMap::generate($ctx->getResource()));
		
		$gravity = new ImageGravity(
			(int)($x * $ctx->getWidth()),
			(int)($y * $ctx->getHeight())
		);
		
		return $ctx->withGravity($gravity);
	}
}
