<?php

use spitfire\storage\database\drivers\mysqlpdo\Driver as MysqlPDO;

return [
	'mysql' => [
		'name'   => 'mysql',
		'driver' => MysqlPDO::class,
		'settings' => [
			'server' => env('mysql_host')?: 'mysql',
			'user' => env('mysql_user')?: 'www',
			'password' => env('mysql_pass')?: 'test',
			'schema' => env('mysql_schema')?: 'testdb'
		]
	]
];
