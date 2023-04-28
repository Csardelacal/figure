<?php namespace app\controllers\apiv1\graphql;

use app\models\FileModel;
use app\types\apiv1\graphql\FileType;
use TheCodingMachine\GraphQLite\Annotations\Query;

class FileController
{
	
	#[Query()]
	public function getFile(int $id) : ?FileType
	{
		$fetched = db()->fetch(FileModel::class, $id);
		return $fetched? new FileType($fetched) : null;
	}
	
}
