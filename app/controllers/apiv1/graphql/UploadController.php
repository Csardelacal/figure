<?php namespace app\controllers\apiv1\graphql;

use app\models\App;
use app\models\UploadModel;
use app\types\apiv1\graphql\UploadType;
use TheCodingMachine\GraphQLite\Annotations\InjectUser;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLException;

class UploadController
{
	
	/**
	 * This endpoint provides access to the file uploads in the server
	 * 
	 * @todo This needs to actually authenticate an app to make sure that
	 * metadata is not leaked.
	 */
	#[Query()]
	public function getUpload(int $id) : ?UploadType
	{
		$fetched = db()->find(UploadModel::class, $id);
		return $fetched? new UploadType($fetched) : null;
	}
	
	/**
	 * 
	 * @param ?string $blame String informing the server operator where the resource
	 * will be used. This allows somebody reviewing uploads to reference where a certain
	 * image is being used when used multiple times.
	 * 
	 * @todo Add blame to the upload model
	 */
	#[Mutation()]
	#[Logged]
	#[Right("upload.claim")]
	public function claimUpload(#[InjectUser()] App $auth, int $id, string $secret, ?string $blame) : UploadType
	{
		/**
		 * @var UploadModel
		 */
		$fetched = db()->find(UploadModel::class, $id);
		
		assume(
			$fetched !== null,
			fn() => throw new GraphQLException('Invalid upload id', 404)
		);
		
		assume(
			$fetched->getApp() === null,
			fn() => throw new GraphQLException('Image has already been claimed', 403)
		);
		
		assume(
			$secret === $fetched->getSecret(),
			fn() => throw new GraphQLException('Invalid secret', 403)
		);
		
		$fetched->setApp($auth);
		$fetched->setBlame($blame);
		$fetched->store();
		
		return $fetched? new UploadType($fetched) : null;
	}
	
	/**
	 * 
	 * 
	 * @todo Add blame to the upload model
	 */
	#[Mutation()]
	#[Logged]
	#[Right("upload.delete")]
	public function deleteUpload(#[InjectUser()] App $auth, int $id) : bool
	{
		/**
		 * @var UploadModel
		 */
		$fetched = db()->find(UploadModel::class, $id);
		
		assume(
			$auth->getId() === $fetched->getApp()->getId(),
			fn() => throw new GraphQLException('You are not the owner of the upload', 400)
		);
		
		$fetched->delete();
		return true;
	}
}
