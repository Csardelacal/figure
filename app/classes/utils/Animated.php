<?php namespace app\classes\utils;

use FFMpeg\FFProbe;

class Animated
{
	
	public static function isAnimated(string $filename)
	{
		$mime = mime($filename);
		
		switch ($mime) {
			case 'video/mp4':
				$ffprobe = FFProbe::create();
				$audio = $ffprobe
					->streams($filename)
					->audios()                      // filters video streams
					->first();
				
				assume(!$audio, 'MP4 files with audio are not allowed');
				return true;
			case 'image/gif':
				return self::isGifAnimated($filename);
		}
		
		return false;
	}
	
	/**
	 *
	 * @see https://www.php.net/manual/en/function.imagecreatefromgif.php#104473
	 */
	public static function isGifAnimated($filename)
	{
		if (!($fh = @fopen($filename, 'rb'))) {
			return false;
		}
		
		$count = 0;
		
		/**
		 * An animated gif contains multiple "frames", with each frame having a
		 * header made up of:
		 * * a static 4-byte sequence (\x00\x21\xF9\x04)
		 * * 4 variable bytes
		 * * a static 2-byte sequence (\x00\x2C) (some variants may use \x00\x21 ?)
		 *
		 * We read through the file til we reach the end of the file, or we've found
		 * at least 2 frame headers
		 *
		 * @todo fixme Technically, it would be possible that a gif where the frame gets
		 * split at the 100kb mark exactly will cause issues. Half the header would be in
		 * the first chunk, and half of it in the second.
		 */
		while (!feof($fh) && $count < 2) {
			$chunk = fread($fh, 1024 * 100); //read 100kb at a time
			$count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $chunk, $matches);
		}
		
		fclose($fh);
		return $count > 1;
	}
}
