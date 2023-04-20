<?php namespace app\figure\pipeline\image;

use app\figure\pipeline\image\ImagePipelineContext;

class ImageGravitySideStage implements ImagePipelineStage
{
	
	private float $x;
	private float $y;
	
	public function __construct(float $x, float $y)
	{
		$this->x = $x;
		$this->y = $y;
	}
	
	public function run(ImagePipelineContext $ctx): ImagePipelineContext
	{
		
		$gravity = new ImageGravity(
			(int)($this->x * $ctx->getWidth()),
			(int)($this->y * $ctx->getHeight())
		);
		
		return $ctx->withGravity($gravity);
	}
	
	public static function left()
	{
		return new self(0, .5);
	}
	
	public static function right()
	{
		return new self(1, .5);
	}
	
	public static function top()
	{
		return new self(.5, 0);
	}
	
	public static function bottom()
	{
		return new self(.5, 1);
	}
	
	public static function at(float $x, float $y)
	{
		return new self($x, $y);
	}
	
	/**
	 *
	 * @throws UserApplicationException If the numbers are not valid.
	 */
	public static function fromString(string $definition)
	{
		[$x, $y] = explode('x', $definition, 2);
		
		assume(is_numeric($x), 'Expected X coordinate of gravity to be a number');
		assume(is_numeric($y), 'Expected Y coordinate of gravity to be a number');
		
		return new self((float)$x, (float)$y);
	}
}
