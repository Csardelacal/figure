<?php

use app\controllers\HomeController;
use app\glide\Server;
use app\models\ApiToken;
use app\models\UploadModel;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;
use League\Glide\Signatures\SignatureInterface;
use Psr\Http\Message\ResponseInterface;
use spitfire\core\Request;
use spitfire\io\stream\Stream;
use spitfire\core\Response;
use spitfire\core\ResponseFactory;
use spitfire\core\router\Router;
use spitfire\exceptions\user\NotFoundException;

/**
 * Feel free to add your routes inside this closure. This ensures that the
 * application can load it and appropriately scope your routes to function
 * within it's selected URL space (not unlike a directory).
 */
return function (Router $router) {
	
	$router->get('', fn() => new Response(Stream::fromHandle(fopen(spitfire()->locations()->resources('views/home.html'), 'r'))));
	
	$router->get('/create', [HomeController::class, 'create']);
	
	$router->get('/test', function (ResponseFactory $rf) {
		return response($rf->json(['test' => true]));
	});
	
	/**
	 * @todo The model hydration for closures does not work
	 */
	$router->get('/image/{uploadid}/{expires}', function(string $uploadid, string $expires, Request $request, SignatureInterface $signature) : ResponseInterface {
		
		assume($expires === 'never' || $expires > time(), 'Upload is expired');
		
		/**
		 * @var UploadModel
		 */
		$upload = db()->find(UploadModel::class, $uploadid);
		
		if ($upload === null) {
			throw new NotFoundException('Upload not found');
		}
		
		try {
			$token = db()->from(ApiToken::class)->where('app_id', $upload->getAppId())->first(fn() => throw new SignatureException(''));
			SignatureFactory::create($token->getToken())->validateRequest($request->getUri()->getPath(), $request->getQueryParams());
		}
		catch (SignatureException $e) {
			$signature->validateRequest($request->getUri()->getPath(), $request->getQueryParams());
		}
		
		$file   = $upload->getFile();
		
		[$drive, $_path] = storage()->pathInfo($file->getPoster());
		
		/**
		 * @var Server
		 */
		$glide = spitfire()->provider()->get(Server::class);
		$glide->setSource($drive->fly());
		
		/**
		 * @var ResponseInterface
		 */
		$response = $glide->getImageResponse($_path, $request->getQueryParams());
		return $response->withHeader('Expires', gmdate("r", $expires === "never"? strtotime("+3 YEAR") : $expires));
	});
	
	$router->get('/video/{uploadid}/{expires}', function(string $uploadid, string $expires, Request $request, SignatureInterface $signature) : ResponseInterface {
		
		assume($expires === 'never' || $expires > time(), 'Upload is expired');
		
		/**
		 * @var UploadModel
		 */
		$upload = db()->find(UploadModel::class, $uploadid);
		
		if ($upload === null) {
			throw new NotFoundException('Upload not found');
		}
		
		if (!$upload->getFile()->getAnimated()) {
			throw new NotFoundException('Upload not animated');
		}
		
		try {
			$token = db()->from(ApiToken::class)->where('app_id', $upload->getAppId())->first(fn() => throw new SignatureException(''));
			SignatureFactory::create($token->getToken())->validateRequest($request->getUri()->getPath(), $request->getQueryParams());
		}
		catch (SignatureException $e) {
			$signature->validateRequest($request->getUri()->getPath(), $request->getQueryParams());
		}
		
		$file   = $upload->getFile();
		
		[$drive, $_path] = storage()->pathInfo($file->getFileName());
		
		$glide = spitfire()->provider()->get('ffmpegserver');
		$glide->setSource($drive->fly());
		
		/**
		 * @var ResponseInterface
		 */
		$response = $glide->getImageResponse($_path, $request->getQueryParams());
		return $response->withHeader('expires', gmdate("r", $expires === "never"? strtotime("+3 YEAR") : $expires));
	});
};
