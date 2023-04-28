<?php namespace app\models;

use spitfire\model\attribute\CharacterString;
use spitfire\model\attribute\Table;
use spitfire\model\Model;
use spitfire\model\traits\WithId;
use spitfire\model\traits\WithSoftDeletes;
use spitfire\model\traits\WithTimestamps;

/**
 *
 *
 */
#[Table('apps')]
class AppModel extends Model
{
	use WithTimestamps, WithId, WithSoftDeletes;
	
	#[CharacterString(255)]
	private string $secret = '';
	
	#[CharacterString(255)]
	private ?string $publickey;
}
