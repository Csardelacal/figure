<?php

use app\controllers\HomeController;
use app\controllers\ImageController;
use app\controllers\UploadController;
use app\controllers\VideoController;
use spitfire\core\router\Router;

/**
 * Feel free to add your routes inside this closure. This ensures that the
 * application can load it and appropriately scope your routes to function
 * within it's selected URL space (not unlike a directory).
 */
return function (Router $router) {
	
	$router->get('', [HomeController::class, 'index']);
	
	$router->get('/create', [HomeController::class, 'create']);
	$router->post('/upload.json', [UploadController::class, 'upload']);
	
	$router->get('/image/{id}/{expiration}/{transform}/{salt}:{hash}', [ImageController::class, 'retrieve']);
	$router->get('/video/{id}/{expiration}/{transform}/{salt}:{hash}', [VideoController::class, 'retrieve']);
	
	$router->get('/test', function () {
		return response(view(null, ['test' => true]));
	});
};
