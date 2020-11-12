<?php namespace cloudy;

use auth\SSO;
use auth\SSOCache;
use Exception;
use spitfire\cache\MemcachedAdapter;
use function request;

/**
 * This is the base cloudyNAS file. When using cloudyNAS, please instance this.
 * 
 * CloudyNAS is a distributed blob storage, allowing a cluster of servers to store
 * and manage a large amount of blob (or file) storage. Your cluster must have a 
 * leader server, this is the server you need to provide.
 */
class Cloudy 
{
	
	/**
	 * Cloudy uses PHPAS to validate that a server that connects to it is indeed 
	 * part of the network and has permission to read and write data to it.
	 *
	 * @var SSOCache
	 */
	private $sso;
	
	/**
	 * The URL of the leader server. The leader server manages clusters and buckets
	 * and is the one that presents a web-interface.
	 *
	 * @var string
	 */
	private $endpoint;
	
	/**
	 * The appID of cloudyNAS on the SSO server. This is used to make sure that 
	 * leaked signatures cannot be used to compromise data connections between 
	 * other servers.
	 *
	 * @var string
	 */
	private $appId;
	
	/**
	 * Instance a new cloudyNAS SDK. Your application can then use this interface
	 * to communicate with the cluster and store or retrieve blobs from it.
	 * 
	 * @param string $endpoint
	 * @param SSOCache|SSO $sso
	 */
	public function __construct($endpoint, $sso) {
		$reflection = URLReflection::fromURL($endpoint);
		
		$this->endpoint  = rtrim($reflection->getProtocol() . '://' . $reflection->getServer() . ':' . $reflection->getPort() . $reflection->getPath(), '/');
		$this->appId     = $reflection->getUser();
		
		$this->sso = $sso instanceof SSO || $sso instanceof SSOCache? $sso : new SSOCache($sso);
	}
	
	/**
	 * Retrieves metadata about a bucket.
	 * 
	 * A bucket contains the relevant information on which servers the data can
	 * be retrieved from / written to.
	 * 
	 * Please note: this makes use of memcached, allowing to reduce the round trips
	 * between the servers to retrieve a piece of information that is mostly static.
	 * 
	 * @param string $uniqid
	 * @return \cloudy\Bucket
	 */
	public function bucket($uniqid) {
		$cache = new MemcachedAdapter();
		
		$response = $cache->get('cloudy_bucket_' . $uniqid, function() use ($uniqid) {
			$r = request(sprintf('%s/bucket/read/%s.json', $this->endpoint, $uniqid));
			$r->get('signature', (string)$this->sso->makeSignature($this->appId));
			$r = $r->send();

			$response = $r->expect(200)->json();

			if (!isset($response->payload)) {
				throw new PrivateException('Response from CloudyNAS was corrupted', 2006101455);
			}
			
			return $response;
		});
		
		$server   = $response->payload->master->hostname;
		
		return new Bucket($uniqid, new Server($server), $this);
	}
	
	/**
	 * Returns the SSO connection this SDK is using to communicate with cloudyNAS 
	 * and authenticate itself.
	 * 
	 * @return SSO|SSOCache
	 */
	public function sso() {
		return $this->sso;
	}
	
	/**
	 * Generates a signature that can be used by this server to authenticate itself
	 * against cloudyNAS.
	 * 
	 * @return string
	 */
	public function signature() {
		return (string)$this->sso->makeSignature($this->appId);
	}
	
}
