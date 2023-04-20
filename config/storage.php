<?php

return [
	'writeto' => env('storage_provider')?: 's3',
	'engines' => [
		'private' => [
			'driver' => 'local',
			'root'   => dirname(__DIR__) . '/storage/private'
		],
		'public' => [
			'driver' => 'local',
			'root'   => dirname(__DIR__) . '/storage/public'
		],
		's3' => [
			'driver' => 's3',
			'endpoint' => env('s3_endpoint')?: 'http://minio:9000',
			'bucket' => 'figure',
			'key' => env('s3_key')?: 'figure',
			'secret' => env('s3_secret')?: 'figuretest',
			'use_path_style_endpoint' => true,
			'region' => 'us-east-1'
		]
	]
];