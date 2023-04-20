<?php

use app\migrations\FileMigration;
use spitfire\storage\database\migration\relational\TagLayoutMigration;

return [
	new TagLayoutMigration(),
	new FileMigration()
];