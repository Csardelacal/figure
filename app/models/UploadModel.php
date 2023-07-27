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

#[Table('uploads')]
class UploadModel extends Model
{
	use WithTimestamps, WithId, WithSoftDeletes;
	
	#[CharacterString()]
	private string $secret = '';
	
	#[References(App::class)]
	#[AttributeBelongsToOne(App::class, '_id')]
	private ?App $app;
	
	#[References(FileModel::class)]
	#[AttributeBelongsToOne(FileModel::class, '_id')]
	private ?FileModel $file;
	
	#[CharacterString(512)]
	private ?string $blame = null;
	
	public function setFile(FileModel $file)
	{
		$this->file = $file;
	}
	
	public function getFile() :? FileModel
	{
		return $this->file?? $this->lazy('file');
	}
	
	public function initSecret()
	{
		$this->secret = sha1(random_bytes(255));
	}
	
	public function getSecret()
	{
		return $this->secret;
	}

	/**
	 * Get the value of blame
	 *
	 * @return string
	 */
	public function getBlame(): string
	{
		return $this->blame;
	}

	/**
	 * Set the value of blame
	 *
	 * @param string $blame
	 * @return self
	 */
	public function setBlame(string $blame): self
	{
		$this->blame = $blame;
		return $this;
	}
	
	public function app() : BelongsToOne
	{
		return new BelongsToOne(
			new Field($this, 'app'),
			new Field(new App($this->getConnection()), '_id')
		);
	}
	
	public function getAppId() : ?int
	{
		return $this->getActiveRecord()->get('app_id');
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
