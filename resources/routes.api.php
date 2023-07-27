<?php

use app\controllers\apiv1\UploadController;
use spitfire\core\ResponseFactory;
use spitfire\core\router\Router;
use spitfire\io\stream\Stream;
use spitfire\io\stream\StreamFactory;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;
use TheCodingMachine\GraphQLite\Http\Psr15GraphQLMiddlewareBuilder;
use TheCodingMachine\GraphQLite\Http\WebonyxGraphqlMiddleware;
use TheCodingMachine\GraphQLite\SchemaFactory;

/**
 * Feel free to add your routes inside this closure. This ensures that the
 * application can load it and appropriately scope your routes to function
 * within it's selected URL space (not unlike a directory).
 *
 * This file is focused on providing API style access to your application,
 * without interfering with your user facing service.
 *
 * You can write different versions of the API like this:
 *
 * $v1 = $router->scope('api/v1');
 * $v1->route(...);
 */
return function (Router $router) {
	$v1 = $router->scope('/api/v1');
	
	$v1->middleware(spitfire()->provider()->get(WebonyxGraphqlMiddleware::class));
	
	$v1->post('/upload.json', [UploadController::class, 'upload']);
	$v1->get('/image/{id}/{expiration}/{salt}:{hash}', [UploadController::class, 'retrieve']);
	
	$v1->request('/', fn() => response(Stream::fromString('Okay')));
};
