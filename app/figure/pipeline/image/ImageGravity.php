<?php namespace app\figure\pipeline\image;

class ImageGravity
{
	
	public function __construct(
		private int $x,
		private int $y
	) {
	}
	
	public function getX() : int
	{
		return $this->x;
	}
	
	public function getY() : int
	{
		return $this->y;
	}
}
