<?php namespace app\glide;

use Exception;
use League\Glide\ServerFactory as GlideServerFactory;

class ServerFactory extends GlideServerFactory
{
	
	public CacheController $cache;
	
	public function makeServer()
	{
		return new Server(
			$this->getSource(),
			$this->getCache(),
			$this->getApi()
        );
	}
	
	public function getServer()
	{
        $server = $this->makeServer();

        $server->setSourcePathPrefix($this->getSourcePathPrefix() ?: '');
        $server->setCachePathPrefix($this->getCachePathPrefix() ?: '');
        $server->setGroupCacheInFolders($this->getGroupCacheInFolders());
        $server->setCacheWithFileExtensions($this->getCacheWithFileExtensions());
        $server->setDefaults($this->getDefaults());
        $server->setPresets($this->getPresets());
        $server->setBaseUrl($this->getBaseUrl() ?: '');
        $server->setResponseFactory($this->getResponseFactory());
        $server->setCacheController($this->getCacheController());

        if ($this->getTempDir()) {
            $server->setTempDir($this->getTempDir());
        }

        return $server;
	}
	
	public function setCacheController(CacheController $controller)
	{
		$this->config['cache_controller'] = $controller;
	}
	
	public function getCacheController() : ?CacheController
	{
		return $this->config['cache_controller'];
	}
	
    /**
     * Create configured server.
     *
     * @param array $config Configuration parameters.
     *
     * @return Server Configured server.
     */
    public static function create(array $config = [])
    {
        return (new static($config))->getServer();
    }
}
