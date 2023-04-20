<?php namespace app\classes\utils;

class FeatureDetect
{
	public static function webp() : bool
	{
		/**
		 * If the user agent did not send an accept header we will assume they do not
		 * support webp
		 */
		if (!isset($_SERVER['HTTP_ACCEPT'])) {
			return false;
		}
		
		return mb_strstr($_SERVER['HTTP_ACCEPT'], 'image/webp');
	}
}
