<?php namespace app\models;

use spitfire\model\attribute\CharacterString;
use spitfire\model\attribute\Table;
use spitfire\model\Model;
use spitfire\model\traits\WithId;
use spitfire\model\traits\WithSoftDeletes;
use spitfire\model\traits\WithTimestamps;

/**
 *
 *
 */
#[Table('apps')]
class App extends Model
{
	use WithTimestamps, WithId, WithSoftDeletes;
	
	#[CharacterString(255)]
	private string $name = '';

	/**
	 * Get the value of name
	 *
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * Set the value of name
	 *
	 * @param string $name
	 * @return self
	 */
	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}
}
