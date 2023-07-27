<?php namespace app\models;

use spitfire\model\attribute\BelongsToOne as AttributeBelongsToOne;
use spitfire\model\attribute\CharacterString;
use spitfire\model\attribute\References;
use spitfire\model\attribute\Table;
use spitfire\model\Field;
use spitfire\model\Model;
use spitfire\model\relations\BelongsToOne;
use spitfire\model\traits\WithId;
use spitfire\model\traits\WithSoftDeletes;
use spitfire\model\traits\WithTimestamps;

/**
 *
 *
 */
#[Table('api_tokens')]
class ApiToken extends Model
{
	use WithTimestamps, WithId, WithSoftDeletes;
	
	#[References(App::class)]
	#[AttributeBelongsToOne(App::class, '_id')]
	private ?App $app;
	
	#[CharacterString(255)]
	private string $secret = '';
	
	public function generate() : void
	{
		$this->secret = 't_' . base64_encode(random_bytes(64));
	}
	
	public function getToken() : string
	{
		return $this->secret;
	}
	
	/**
	 * Get the value of app
	 *
	 * @return ?App
	 */
	public function getApp(): ?App
	{
		return $this->app?? $this->lazy('app');
	}

	/**
	 * Set the value of app
	 *
	 * @param ?App $app
	 *
	 * @return self
	 */
	public function setApp(?App $app): self
	{
		$this->app = $app;
		return $this;
	}
}
