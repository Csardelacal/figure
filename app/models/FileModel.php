<?php namespace app\models;

use spitfire\core\Collection;
use spitfire\model\attribute\CharacterString;
use spitfire\model\attribute\HasMany;
use spitfire\model\attribute\InIndex;
use spitfire\model\attribute\Integer;
use spitfire\model\attribute\Table;
use spitfire\model\Model;
use spitfire\model\traits\WithId;
use spitfire\model\traits\WithTimestamps;

#[Table('files')]
class FileModel extends Model
{
	use WithTimestamps, WithId;
	
	#[CharacterString(255)]
	private string $filename;
	
	#[CharacterString(255)]
	private string $poster;
	
	#[CharacterString(1024)]
	private string $lqip;
	
	#[CharacterString(255)]
	private string $contentType;
	
	/**
	 * When the animated flag is set, the media can be retrieved via the video
	 * tag, allowing for granular control of the content (like pausing when off-screen)
	 *
	 * @var bool
	 */
	#[Integer(true)]
	private bool $animated;
	
	#[CharacterString(255)]
	#[InIndex('hashidx', 1)]
	private string $md5;
	
	#[Integer(true)]
	#[InIndex('hashidx', 2)]
	private string $length;
	
	#[HasMany(UploadModel::class, 'file_id')]
	private Collection $uploads;
	
	public function getFileName()
	{
		return $this->filename;
	}
	
	public function setFilename(string $filename)
	{
		$this->filename = $filename;
	}
	
	public function getPoster()
	{
		return $this->poster;
	}
	
	public function setPoster(string $poster)
	{
		$this->poster = $poster;
	}
	
	public function getLQIP()
	{
		return $this->lqip;
	}
	
	public function setLQIP(string $lqip)
	{
		$this->lqip = $lqip;
	}
	
	public function getContentType()
	{
		return $this->contentType;
	}
	
	public function setContentType(string $contentType)
	{
		$this->contentType = $contentType;
	}
	
	public function getAnimated() : bool
	{
		return $this->animated;
	}
	
	public function getMD5() : string
	{
		return $this->md5;
	}
	
	public function setAnimated(bool $animated)
	{
		$this->animated = $animated;
	}
	
	public function setMD5(string $md5)
	{
		$this->md5 = $md5;
	}
	
	public function setLength(string $length)
	{
		$this->length = $length;
	}
	
	public function getLength()
	{
		return $this->length;
	}
}
