<?php namespace app\glide;

class CacheController
{
	
	public function map(string $path, string $hash) : string
	{
		return $path;
	}
	
	public function hit(string $cachefile, string $path, string $hash, int $size) : void
	{
		//
	}
	
	public function miss(string $cachefile, string $path, string $hash, int $size) : void
	{
		//
	}
}
