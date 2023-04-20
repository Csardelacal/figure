<?php namespace app\figure\pipeline\video;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Media\Video;
use spitfire\exceptions\user\ApplicationException;
use spitfire\io\stream\Stream;

/**
 * I am separating the video and image pipeline contexts. The media and the
 * underlying libraries just make it too difficult to create a unified pipeline.
 *
 * The pipeline context should still be able to be created from different media
 * types (including video and gif), but the pipeline should then operate using
 * a GD object. Making it more consistent.
 *
 * @todo Add an option to create from Stream?
 */
class VideoPipelineContext
{
	
	private Video $resource;
	
	private int $width;
	private int $height;
	
	public function __construct(Video $resource, int $width, int $height)
	{
		$this->resource = $resource;
		$this->width = $width;
		$this->height = $height;
	}
	
	public function getResource() : Video
	{
		return $this->resource;
	}
	
	public function getWidth() : int
	{
		return $this->width;
	}
	
	public function getHeight() : int
	{
		return $this->height;
	}
	
	/**
	 *
	 * @todo Determine the content-type of the file
	 * @todo Load a context from an image file
	 * @todo Create context from a video file
	 */
	public static function fromFile(string $filename) : VideoPipelineContext
	{
		$resource = FFMpeg::create()->open($filename);
		$stream   = $resource->getStreams()->videos()->first();
		
		return new self(
			$resource,
			$stream->getDimensions()->getWidth(),
			$stream->getDimensions()->getHeight()
		);
	}
	
	public function render($format) : Stream
	{
		if ($format !== 'video/mp4') {
			throw new ApplicationException();
		}
		$tmpfilename = tempnam(sys_get_temp_dir(), 'ffmpeg') . '.mp4';
		$tmp = fopen($tmpfilename, 'w+');
		
		$this->resource->save(new X264(), stream_get_meta_data($tmp)['uri']);
		$_return = Stream::fromString(file_get_contents($tmpfilename));
		
		fclose($tmp);
		unlink($tmpfilename);
		
		return $_return;
	}
}
