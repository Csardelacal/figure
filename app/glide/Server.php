<?php namespace app\glide;

use League\Flysystem\FilesystemOperator;
use League\Glide\Api\ApiInterface;
use League\Glide\Server as GlideServer;

class Server extends GlideServer
{
	private Cache $_cache;
	private ?CacheController $cacheController;
	
    /**
     * Create Server instance.
     *
     * @param FilesystemOperator $source Source file system.
     * @param FilesystemOperator $cache  Cache file system.
     * @param ApiInterface       $api    Image manipulation API.
     */
    public function __construct(FilesystemOperator $source, FilesystemOperator $cache, ApiInterface $api)
    {
		$this->_cache = new Cache($cache);
		parent::__construct($source, $cache, $api);
    }
	
	public function setGroupCacheInFolders($groupCacheInFolders)
	{
		$this->_cache->setGroupCacheInFolders($groupCacheInFolders);
		$this->groupCacheInFolders = $groupCacheInFolders;
	}
	
	public function getGroupCacheInFolders()
	{
		return $this->_cache->getGroupCacheInFolders();
	}

    /**
     * Set cache path prefix.
     *
     * @param string $cachePathPrefix Cache path prefix.
     *
     * @return void
     */
    public function setCachePathPrefix($cachePathPrefix)
    {
		$this->_cache->setCachePathPrefix($cachePathPrefix);
        $this->cachePathPrefix = trim($cachePathPrefix, '/');
    }

    /**
     * Get cache path prefix.
     *
     * @return string Cache path prefix.
     */
    public function getCachePathPrefix()
    {
        return $this->_cache->getCachePathPrefix();
    }
	
	public function setCacheController(CacheController $controller)
	{
		$this->cacheController = $controller;
	}
	
	/**
	 * @param string $path
	 */
	public function getCachePath($path, array $params = [])
	{
        $sourcePath = $this->getSourcePath($path);

        if ($this->sourcePathPrefix) {
            $sourcePath = substr($sourcePath, strlen($this->sourcePathPrefix) + 1);
        }
		
		return $this->cacheController->map(
			$this->_cache->getPath($path, $params),
			$this->getHash($path, $params)
		);
	}
    /**
     * Set the cache with file extensions setting.
     *
     * @param bool $cacheWithFileExtensions Whether to cache with file extensions.
     *
     * @return void
     */
    public function setCacheWithFileExtensions($cacheWithFileExtensions)
    {
        $this->_cache->setCacheWithFileExtensions($cacheWithFileExtensions);
    }

    /**
     * Get the cache with file extensions setting.
     *
     * @return bool Whether to cache with file extensions.
     */
    public function getCacheWithFileExtensions()
    {
        return $this->_cache->getCacheWithFileExtensions();
    }
	
	/**
	 * @param string $path
	 */
	public function makeImage($path, array $params)
	{
		unset($params['s']);
		$hit = $this->cacheFileExists($path, $params);
		$_return = parent::makeImage($path, $params);
		
		/**
		 * @todo Replace with _cache object
		 */
		$size = $this->cache->fileSize($_return);
		
		if ($hit) {
			$this->cacheController?->hit($_return, $path, $this->getHash($path, $params), $size);
		}
		else {
			$this->cacheController?->miss($_return, $path, $this->getHash($path, $params), $size);
		}
		
		return $_return;
	}
	
	/**
	 * @param string $path
	 */
	public function getHash($path, array $params = []) : string
	{
		return md5($path.'?'.http_build_query($params));
	}
	
	public function deleteCache($path)
	{
		echo $path, PHP_EOL;
		echo dirname($this->getCachePath($path, [])), PHP_EOL;
		return parent::deleteCache($path);
	}
	
	public function deleteCacheFile($path)
	{
		echo $path;
		$this->_cache->deleteFile($path);
	}
	
}
