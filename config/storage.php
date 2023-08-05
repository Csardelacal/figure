<?php

return [
	'writeto' => env('storage_provider')?: 's3',
	'cacheto' => env('caching_provider')?: 'cache',
	'engines' => [
		'private' => [
			'driver' => 'local',
			'root'   => dirname(__DIR__) . '/storage/private'
		],
		'public' => [
			'driver' => 'local',
			'root'   => dirname(__DIR__) . '/storage/public'
		],
		'cache' => [
			'driver' => 'local',
			'root'   => dirname(__DIR__) . '/storage/cache'
		],
		's3' => [
			'driver' => 's3',
			'endpoint' => env('s3_endpoint')?: 'http://minio:9000',
			'bucket' => env('s3_bucket')?: 'figure',
			'key' => env('s3_key')?: 'figure',
			'secret' => env('s3_secret')?: 'figuretest',
			'use_path_style_endpoint' => true,
			'region' => env('s3_region')?: 'us-east-1'
		]
	]
];
