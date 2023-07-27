<?php namespace app\types\apiv1\graphql;

use app\models\UploadModel;
use League\Glide\Urls\UrlBuilder;
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
	
	#[Field()]
	public function getSignature(int $expires, int $w = 700) : string
	{
		$builder = spitfire()->provider()->get(UrlBuilder::class);
		$salt    = bin2hex(random_bytes(10));
		
		return url()->to($builder->getUrl(
			sprintf('%d/%s/%s', $this->upload->getId(), $expires, $salt), 
			['w' => $w]
		));
	}
}
