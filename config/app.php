<?php

return [
	'debug' => env('DEBUG', false),
	'url'   => env('APP_URL'),
	
	'providers' => [
		\spitfire\core\router\providers\RouterServiceProvider::class,
		\app\services\router\RouteProvider::class,
		\spitfire\core\http\URLServiceProvider::class,
		\app\services\LoggingServiceProvider::class,
		\spitfire\io\session\SessionProvider::class,
		\spitfire\mvc\providers\DirectorProvider::class,
		\spitfire\storage\database\support\services\DatabaseServiceProvider::class,
		\spitfire\storage\database\support\services\MigrationServiceProvider::class,
		\spitfire\model\providers\ModelServiceProvider::class,
		\spitfire\storage\support\providers\StorageServiceProvider::class,
		\app\services\GlideServiceProvider::class,
		\app\services\console\CommandServiceProvider::class,
		\app\services\GraphQLServiceProvider::class,
	]
];
