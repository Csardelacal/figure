<?php namespace app\controllers\apiv1\graphql;

use app\models\UploadModel;
use app\types\apiv1\graphql\UploadType;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;
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
		$fetched = db()->fetch(UploadModel::class, $id);
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
	public function claimUpload(int $id, string $secret, ?string $blame) : UploadType
	{
		/**
		 * @var UploadModel
		 */
		$fetched = db()->fetch(UploadModel::class, $id);
		
		assume(
			$fetched !== null,
			fn() => throw new GraphQLException('Invalid upload id', 404)
		);
		
		assume(
			$secret === $fetched->getSecret(),
			fn() => throw new GraphQLException('Invalid secret', 403)
		);
		
		return $fetched? new UploadType($fetched) : null;
	}
}
