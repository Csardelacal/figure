<?php namespace app\figure\pipeline\video;

/**
 *
 */
interface VideoPipelineStage
{
	
	public function run(VideoPipelineContext $ctx) : VideoPipelineContext;
}
