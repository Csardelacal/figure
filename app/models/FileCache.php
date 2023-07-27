<?php namespace app\models;

use spitfire\model\attribute\CharacterString;
use spitfire\model\attribute\Integer;
use spitfire\model\attribute\Table;
use spitfire\model\Model;
use spitfire\model\traits\WithId;
use spitfire\model\traits\WithSoftDeletes;
use spitfire\model\traits\WithTimestamps;

/**
 *
 *
 */
#[Table('file_caches')]
class FileCache extends Model
{
	use WithTimestamps, WithId;
	
	#[CharacterString(255)]
	private string $cachefile = '';
	
	#[CharacterString(255)]
	private string $filename = '';
	
	#[CharacterString(255)]
	private ?string $hash;
	
	#[Integer(true)]
	private ?string $used;
	
	#[Integer(true)]
	private ?string $size;
	
	/**
	 * Get the value of filename
	 * @return string
	 */
	public function getFilename(): string
	{
		return $this->filename;
	}
	
	/**
	 * Set the value of filename
	 *
	 * @param string $filename
	 * @return self
	 */
	public function setFilename(string $filename): self
	{
		$this->filename = $filename;
		return $this;
	}
	
	/**
	 * Get the value of filename
	 * @return string
	 */
	public function getCacheFilename(): string
	{
		return $this->cachefile;
	}
	
	/**
	 * Set the value of filename
	 *
	 * @param string $filename
	 * @return self
	 */
	public function setCacheFilename(string $filename): self
	{
		$this->cachefile = $filename;
		return $this;
	}

	/**
	 * Get the value of hash
	 * @return ?string
	 */
	public function getHash(): ?string
	{
		return $this->hash;
	}

	/**
	 * Set the value of hash
	 *
	 * @param ?string $hash
	 * @return self
	 */
	public function setHash(?string $hash): self
	{
		$this->hash = $hash;
		return $this;
	}

	/**
	 * Get the value of used
	 * @return ?string
	 */
	public function getUsed(): ?string
	{
		return $this->used;
	}

	/**
	 * Set the value of used
	 *
	 * @param ?string $used
	 * @return self
	 */
	public function setUsed(?string $used): self
	{
		$this->used = $used;
		return $this;
	}

	/**
	 * Get the value of size
	 * @return ?string
	 */
	public function getSize(): ?string
	{
		return $this->size;
	}

	/**
	 * Set the value of size
	 *
	 * @param ?string $size
	 * @return self
	 */
	public function setSize(?string $size): self
	{
		$this->size = $size;
		return $this;
	}
}
