<?php namespace app\classes\utils;

use app\figure\pipeline\image\ImagePipeline;
use app\figure\pipeline\image\ImagePipelineContext;
use app\figure\pipeline\image\ImageScaleDownStage;

class LQIP
{
	
	public static function generate(string $filename)
	{
		$t = self::generateH16($filename);
		$t = (strlen($t) > 1024)? self::generateW16($filename) : $t;
		return (strlen($t) > 1024)? file_get_contents(__DIR__ . '/_fallback.data') : $t;
	}
	
	private static function generateH16(string $filename)
	{
		/**
		 * Scale down the image
		 */
		$pipeline = new ImagePipeline();
		$pipeline->push(new ImageScaleDownStage(null, 16));
		$result = $pipeline->apply(ImagePipelineContext::fromFile($filename));
		
		/**
		 * Generate a data URL
		 */
		$res = $result->render('image/webp', ['quality' => 20]);
		return sprintf('data:image/webp;base64,%s', base64_encode($res));
	}
	
	private static function generateW16(string $filename)
	{
		/**
		 * Scale down the image
		 */
		$pipeline = new ImagePipeline();
		$pipeline->push(new ImageScaleDownStage(16, null));
		$result = $pipeline->apply(ImagePipelineContext::fromFile($filename));
		
		/**
		 * Generate a data URL
		 */
		$res = $result->render('image/webp', ['quality' => 20]);
		return sprintf('data:image/webp;base64,%s', base64_encode($res));
	}
}
