<?php namespace app\types\apiv1\graphql;

use app\models\UploadModel;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

#[Type()]
class UploadType
{
	
	private UploadModel $upload;
	
	public function __construct(UploadModel $uploadModel)
	{
		$this->upload = $uploadModel;
	}
	
	#[Field()]
	public function getId() : int
	{
		return $this->upload->getId();
	}
}
