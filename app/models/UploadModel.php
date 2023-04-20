<?php namespace app\models;

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
	
	#[References(AppModel::class)]
	private ?AppModel $app = null;
	
	#[References(FileModel::class)]
	private ?FileModel $file;
	
	public function file()
	{
		return new BelongsToOne(
			new Field($this, 'file'),
			new Field(new FileModel($this->getConnection()), '_id')
		);
	}
	
	public function setFile(FileModel $file)
	{
		$this->file = $file;
	}
	
	public function getFile() :? FileModel
	{
		return $this->file?? $this->lazy('file')->getPayload()->first();
	}
	
	public function initSecret()
	{
		$this->secret = sha1(random_bytes(255));
	}
	
	public function getSecret()
	{
		return $this->secret;
	}
}
