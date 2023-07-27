<?php namespace app\glide;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;

class Cache
{
    /**
     * Whether to group cache in folders.
     *
     * @var bool
     */
    protected $groupCacheInFolders = true;

    /**
     * Cache path prefix.
     *
     * @var string
     */
    protected $cachePathPrefix;

    /**
     * Whether to cache with file extensions.
     *
     * @var bool
     */
    protected $cacheWithFileExtensions = false;
	
    /**
     * Cache file system.
     *
     * @var FilesystemOperator
     */
    protected $cache;
	
	public function __construct(FilesystemOperator $cache)
	{
		$this->cache = $cache;
	}
	
	public function deleteFile($path) : void
	{
		$this->cache->delete($path);
		
		do {
			$_path = $path;
			$path = dirname($path);
			echo $path, PHP_EOL;
			
			if ($path !== '.' && count($this->cache->listContents($path)->toArray()) === 0) {
				$this->cache->deleteDirectory($path);
			}
			else {
				return;
			}
		}
		while($path !== $_path);
	}

    /**
     * Set the group cache in folders setting.
     *
     * @param bool $groupCacheInFolders Whether to group cache in folders.
     *
     * @return void
     */
    public function setGroupCacheInFolders($groupCacheInFolders)
    {
        $this->groupCacheInFolders = $groupCacheInFolders;
    }

    /**
     * Get the group cache in folders setting.
     *
     * @return bool Whether to group cache in folders.
     */
    public function getGroupCacheInFolders()
    {
        return $this->groupCacheInFolders;
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
        $this->cachePathPrefix = trim($cachePathPrefix, '/');
    }

    /**
     * Get cache path prefix.
     *
     * @return string Cache path prefix.
     */
    public function getCachePathPrefix()
    {
        return $this->cachePathPrefix;
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
        $this->cacheWithFileExtensions = $cacheWithFileExtensions;
    }

    /**
     * Get the cache with file extensions setting.
     *
     * @return bool Whether to cache with file extensions.
     */
    public function getCacheWithFileExtensions()
    {
        return $this->cacheWithFileExtensions;
    }
	
    /**
     * Check if a cache file exists.
     *
     * @param string $path   Image path.
     * @param array  $params Image manipulation params.
     *
     * @return bool Whether the cache file exists.
     */
    public function cacheFileExists($path, array $params)
    {
        try {
            return $this->cache->fileExists(
                $this->getPath($path, $params)
            );
        } catch (FilesystemException $exception) {
            return false;
        }
    }
	
    /**
     * Get cache path.
     *
     * @param string $path   Image path.
     * @param array  $params Image manipulation params.
     *
     * @return string Cache path.
     */
	public function getPath(string $path, array $params = []) : string
	{
		
        unset($params['s'], $params['p']);
        ksort($params);

        $cachedPath = md5($path.'?'.http_build_query($params));

        if ($this->groupCacheInFolders) {
            $cachedPath = $path.'/'.$cachedPath;
        }

        if ($this->cachePathPrefix) {
            $cachedPath = $this->cachePathPrefix.'/'.$cachedPath;
        }

        if ($this->cacheWithFileExtensions) {
            $ext = (isset($params['fm']) ? $params['fm'] : pathinfo($path)['extension']);
            $ext = ('pjpg' === $ext) ? 'jpg' : $ext;
            $cachedPath .= '.'.$ext;
        }

        return $cachedPath;
	}
}
