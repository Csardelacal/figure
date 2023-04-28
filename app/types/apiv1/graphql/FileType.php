<?php namespace app\types\apiv1\graphql;

use app\models\FileModel;
use app\models\UploadModel;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

#[Type()]
class FileType
{
	
	private FileModel $file;
	
	public function __construct(FileModel $FileModel)
	{
		$this->file = $FileModel;
	}
	
	#[Field()]
	public function getId() : int
	{
		return $this->file->getId();
	}
	
	#[Field()]
	public function getContentType() : string
	{
		return $this->file->getContentType();
	}
	
	#[Field()]
	public function getMd5() : string
	{
		return $this->file->getMD5();
	}
	
	#[Field()]
	public function getRefCount() : int
	{
		return db()->from(UploadModel::class)->where('file', $this->file)->count();
	}
}
