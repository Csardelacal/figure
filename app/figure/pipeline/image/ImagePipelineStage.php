<?php namespace app\figure\pipeline\image;

use app\figure\pipeline\image\ImagePipelineContext;

/**
 *
 */
interface ImagePipelineStage
{
	
	public function run(ImagePipelineContext $ctx) : ImagePipelineContext;
}
