<?php namespace app\services;

use app\models\ApiToken;
use app\models\App;
use app\models\UploadModel;
use app\support\AppAuthentication;
use Closure;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use spitfire\contracts\core\kernel\ConsoleKernelInterface;
use spitfire\contracts\core\kernel\KernelInterface;
use spitfire\contracts\services\ProviderInterface;
use spitfire\core\Request;
use spitfire\core\ResponseFactory;
use spitfire\io\stream\StreamFactory;
use spitfire\model\ModelFactory;
use spitfire\provider\Container;
use spitfire\storage\database\ConnectionInterface;
use spitfire\utils\Strings as UtilsStrings;
use Strings;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;
use TheCodingMachine\GraphQLite\Http\Psr15GraphQLMiddlewareBuilder;
use TheCodingMachine\GraphQLite\Http\WebonyxGraphqlMiddleware;
use TheCodingMachine\GraphQLite\SchemaFactory;
use TheCodingMachine\GraphQLite\Security\AuthenticationServiceInterface;
use TheCodingMachine\GraphQLite\Security\AuthorizationServiceInterface;

class GraphQLServiceProvider implements ProviderInterface
{
	
	public function register(ContainerInterface $container): void
	{
		/**
		 * In the console, nobody can hear you GraphQL
		 */
		if ($container->get(KernelInterface::class) instanceof ConsoleKernelInterface) {
			$container->get(Container::class)->singleton(
				AppAuthentication::class,
				fn() => new AppAuthentication(null)
			);
		}
		else {
			$container->get(Container::class)->singleton(
				AppAuthentication::class,
				function (Request $request, ModelFactory $db) {
					$header  = $request->getHeader('Authorization');
					if (!$header) { return new AppAuthentication(null); }
					
					$_header = reset($header);
	
					if (!UtilsStrings::startsWith($_header, 'Bearer ')) { return new AppAuthentication(null); }
					$token = trim(substr($_header, strlen('Bearer')));
					
					return new AppAuthentication($db->from(ApiToken::class)->where('secret', $token)->first());
				}
			);
		}
		
		
		/**
		 * @todo This needs a better home and a refactoring
		 */
		$container->get(Container::class)->singleton(
			AuthenticationServiceInterface::class,
			fn(AppAuthentication $authn) => new class($authn) implements AuthenticationServiceInterface
			{
				private AppAuthentication $authn;
				
				public function __construct(AppAuthentication $appAuthentication)
				{
					$this->authn = $appAuthentication;
				}
				
				public function isLogged(): bool
				{
					return $this->authn->isAuthenticated();
				}
				
				public function getUser(): ?App
				{
					if (!$this->authn->isAuthenticated()) {
						return null;
					}
					
					return $this->authn->getToken()->getApp();
				}
			}
		);
		
		$container->get(Container::class)->singleton(
			AuthorizationServiceInterface::class,
			fn(AppAuthentication $authn) => new class($authn) implements AuthorizationServiceInterface
			{
				private AppAuthentication $authn;
				
				public function __construct(AppAuthentication $appAuthentication)
				{
					$this->authn = $appAuthentication;
				}
				
				public function isAllowed(string $right, mixed $subject = null): bool
				{
					switch ($right) {
						case "upload.claim":
							return $this->authn->isAuthenticated();
						case "upload.delete":
							return $this->authn->isAuthenticated();
						default:
							return false;
					}
				}
			}
		);
		
		$container->get(Container::class)->singleton(
			WebonyxGraphqlMiddleware::class,
			fn(Container $container) => (new Psr15GraphQLMiddlewareBuilder(
				(new SchemaFactory(new Psr16Cache(new ArrayAdapter()), $container))
				->addControllerNamespace('\\app\\controllers\\apiv1\\graphql')
				->addTypeNamespace('\\app\\types\\apiv1\\graphql')
				->setAuthenticationService($container->get(AuthenticationServiceInterface::class))
				->setAuthorizationService($container->get(AuthorizationServiceInterface::class))
				->createSchema()
			))
			->setUrl('/api/v1')
			->setStreamFactory(new StreamFactory)
			->setResponseFactory(new ResponseFactory)
			->createMiddleware()
		);
	}
	
	public function init(ContainerInterface $container): void
	{
		//
	}
}
