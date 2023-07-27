<?php namespace app\services;

use app\glide\CacheController;
use app\glide\ffmpeg\ServerFactory as FfmpegServerFactory;
use app\glide\Server;
use app\glide\ServerFactory;
use app\models\FileCache;
use League\Glide\Responses\PsrResponseFactory;
use League\Glide\Signatures\SignatureFactory;
use League\Glide\Signatures\SignatureInterface;
use League\Glide\Urls\UrlBuilder;
use League\Glide\Urls\UrlBuilderFactory;
use Psr\Container\ContainerInterface;
use spitfire\contracts\services\ProviderInterface;
use spitfire\core\Response;
use spitfire\io\stream\Stream;
use spitfire\provider\Container;

class GlideServiceProvider implements ProviderInterface
{
	
	public function register(ContainerInterface $container): void
	{
		/**
		 * @var Container
		 */
		$provider = $container->get(Container::class);
		$drive = storage()->drive(config('storage.writeto'));
		
		$provider->factory(
			UrlBuilder::class,
			fn() => UrlBuilderFactory::create('/image/', config('figure.secret'))
		);
		
		$provider->factory(
			SignatureInterface::class,
			fn() => SignatureFactory::create(config('figure.secret'))
		);
		
		$provider->factory(
			Server::class,
			fn() => ServerFactory::create([
				'response' => new PsrResponseFactory(
					new Response(Stream::fromString('')),
					fn($handle) => Stream::fromHandle($handle)
				),
				'cache_controller' => (new class extends CacheController {
					public function map(string $path, string $hash): string
					{
						$_hash = md5(dirname($path));
						return implode('/', [substr($_hash, 0, 2), substr($_hash, 2, 2), $path]);
					}
					
					public function hit(string $cachefile, string $path, string $hash, int $size): void
					{
						//
						/**
						 * @var FileCache
						 */
						$cache = db()->from(FileCache::class)->where('cachefile', $cachefile)->first();
						
						if (!$cache) { $this->miss($cachefile, $path, $hash, $size); return; }
						if ($cache->getUsed() > time() - 86400) { return; }
						
						$cache->setUsed(time());
						$cache->store();
					}
					
					public function miss(string $cachefile, string $path, string $hash, int $size): void
					{
						/**
						 * @var FileCache
						 */
						$cache = db()->create(FileCache::class);
						$cache->setFilename($path);
						$cache->setCacheFilename($cachefile);
						$cache->setHash($hash);
						$cache->setSize($size);
						$cache->setUsed(time());
						$cache->store();
					}
				}),
				'source' => $drive->fly(),
				'cache' => storage()->drive(config('storage.cacheto'))->fly()
			])
		);
		
		$provider->factory(
			'ffmpegserver',
			fn() => FfmpegServerFactory::create([
				'response' => new PsrResponseFactory(
					new Response(Stream::fromString('')),
					fn($handle) => Stream::fromHandle($handle)
				),
				'cache_controller' => (new class extends CacheController {
					public function map(string $path, string $hash): string
					{
						$_hash = md5(dirname($path));
						return implode('/', [substr($_hash, 0, 2), substr($_hash, 2, 2), $path]);
					}
					
					public function hit(string $cachefile, string $path, string $hash, int $size): void
					{
						//
					}
					public function miss(string $cachefile, string $path, string $hash, int $size): void
					{
						/**
						 * @var FileCache
						 */
						$cache = db()->create(FileCache::class);
						$cache->setFilename($path);
						$cache->setCacheFilename($cachefile);
						$cache->setHash($hash);
						$cache->setSize($size);
						$cache->setUsed(time());
						$cache->store();
					}
				}),
				'source' => $drive->fly(),
				'cache' => storage()->drive(config('storage.cacheto'))->fly()
			])
		);
	}
	
	public function init(ContainerInterface $container): void
	{
		
	}
}
