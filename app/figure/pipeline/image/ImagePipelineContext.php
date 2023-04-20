<?php namespace app\figure\pipeline\image;

use GdImage;
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
class ImagePipelineContext
{
	
	private GdImage $resource;
	
	private int $width;
	private int $height;
	
	/**
	 *
	 */
	private ?ImageGravity $gravity;
	
	/**
	 * Some phones and cameras will just read the raw data from the sensor and
	 * add a flag to indicate how the image is rotated. This flag is ignored by
	 * GD internally and when manipulating the image, GD will actually strip the
	 * information and causing browsers to display the image sideways.
	 */
	private int $rotation;
	
	public function __construct(GdImage $resource, int $width, int $height, int $rotation, ImageGravity $imageGravity = null)
	{
		$this->resource = $resource;
		$this->width = $width;
		$this->height = $height;
		$this->rotation = $rotation;
		$this->gravity = $imageGravity;
	}
	
	public function getResource() : GdImage
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
	
	public function getGravity() : ImageGravity
	{
		return $this->gravity?: new ImageGravity((int)($this->width) / 2, (int)($this->height / 2));
	}
	
	public function withGravity(ImageGravity $gravity) : ImagePipelineContext
	{
		$copy = clone $this;
		$copy->gravity = $gravity;
		return $copy;
	}
	
	public function withResource(GdImage $resource) : ImagePipelineContext
	{
		$copy = clone $this;
		$copy->resource = $resource;
		$copy->width = imagesx($resource);
		$copy->height = imagesy($resource);
		return $copy;
	}
	
	/**
	 *
	 * @todo Determine the content-type of the file
	 * @todo Load a context from an image file
	 * @todo Create context from a video file
	 */
	public static function fromFile(string $filename) : ImagePipelineContext
	{
		
		switch (mime($filename)) {
			case 'image/png':
				$resource = imagecreatefrompng($filename);
				[$width, $height] = getimagesize($filename);
				break;
			
			case 'image/jpeg':
			case 'image/jpg':
				$resource = imagecreatefromjpeg($filename);
				[$width, $height] = getimagesize($filename);
				break;
		}
		
		return new self($resource, $width, $height, 0);
	}
	
	public static function fromStream(string $contentType, Stream $payload) : ImagePipelineContext
	{
		
		switch ($contentType) {
			case 'image/png':
			case 'image/gif':
			case 'image/jpg':
			case 'image/jpeg':
				$resource = imagecreatefromstring($payload->getContents());
				break;
		}
		
		return new self($resource, imagesx($resource), imagesy($resource), 0);
	}
	
	public function render($format, array $options = []) : Stream
	{
		switch ($format) {
			case 'image/jpg':
			case 'image/jpeg':
				$handle = fopen('php://memory', 'r+');
				imagejpeg($this->resource, $handle, $options['quality']?? 75);
				return Stream::fromHandle($handle);
			case 'image/png':
				$handle = fopen('php://memory', 'r+');
				imagepng($this->resource, $handle);
				return Stream::fromHandle($handle);
			case 'image/webp':
				$handle = fopen('php://memory', 'r+');
				imagewebp($this->resource, $handle);
				return Stream::fromHandle($handle);
		}
	}
}
