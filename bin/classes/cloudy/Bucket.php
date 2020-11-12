<?php namespace cloudy;

use CURLFile;
use spitfire\exceptions\PrivateException;
use function mime;

/**
 * CloudyNAS distinguishes between two types of data, buckets and blobs. Buckets 
 * are gigantic collections of blobs. Buckets, unlike directories in a filesystem
 * cannot be nested.
 * 
 * They're intended to be used as computer centric storage. Their size makes it
 * unpractical to list their contents, and instead their forte lies with the ability
 * to distribute them across multiple slaves.
 */
class Bucket
{
	
	/**
	 * The context is the cluster the bucket is located in. This is required for
	 * the application to make requests to the right servers, generate signatures
	 * that are valid in the cluster and understand the topography of the network.
	 *
	 * @var Cloudy
	 */
	private $ctx;
	
	/**
	 * The master is the server that manages the index of the bucket, this makes
	 * it easy to provide contextualized requests.
	 *
	 * @var Server 
	 */
	private $master;
	
	/**
	 * The uniqid of the bucket. This is needed to find the files for this bucket
	 * on the servers.
	 *
	 * @var string
	 */
	private $uniqid;
	
	/**
	 * Instances a new bucket. The bucket can then be used to retrieve the blobs
	 * from the cluster.
	 * 
	 * @param string $uniqid
	 * @param Server $master
	 * @param Cloudy $ctx
	 */
	public function __construct($uniqid, $master, $ctx) {
		$this->ctx = $ctx;
		$this->master = $master;
		$this->uniqid = $uniqid;
	}
	
	/**
	 * Upload a blob to the cluster. The system will always upload files to the 
	 * writing master, which will proceed to push data to the read slaves.
	 * 
	 * @param string $file Full path to a file to be uploaded to the server
	 * @param string $name File name to be using for the file
	 * 
	 * @return \cloudy\Blob
	 * @throws PrivateException
	 */
	public function upload($file, $name = null) {
		
		if (filesize($file) === 0) {
			throw new PrivateException('File is empty', 1811091157);
		}
		
		$r = $this->master->request('/media/create.json');
		$r->get('signature', (string)$this->ctx->signature());
		$r->post('bucket', $this->uniqid);
		$r->post('name', $name? : basename($file));
		$r->post('file', new CURLFile($file));
		$r->post('mime', mime($file));
		
		$response = $r->send()->expect(200)->json();
		
		$media = new Blob($this, $response->name, $response->uniqid);
		$media->setLinks([$response->link]);
		$media->setMime(mime($file));
		return $media;
	}
	
	/**
	 * Retrieves the metadata for a blob from the cluster. This will not yet contain
	 * the contents of the blob, instead it will provide the server with information
	 * on where the file is located and how it can be retrieved.
	 * 
	 * @param string $name
	 * @return \cloudy\Blob
	 */
	public function getMedia($name) {
		
		/*
		 * Start by requesting the raw JSON data from the remote server. This can
		 * then be packeted into objects that the SDK exposes.
		 */
		$r = $this->master->request(sprintf('/media/read/%s/%s.json', $this->uniqid, $name));
		$r->get('signature', (string)$this->ctx->signature());
		$response = $r->send()->expect(200)->json();
		
		$servers = $links = [];
		
		foreach ($response->servers as $server) {
			$servers[] = new Server($server->hostname);
		}
		
		foreach ($response->links as $link) {
			$links[] = $link->uniqid;
		}
		
		
		$file = new Blob($this, $name, $response->uniqid);
		$file->setMime($response->mime);
		$file->setLinks($links);
		$file->setServers($servers);
		
		return $file;
	}
	
	/**
	 * Deletes a blob from the cluster. Please note that the cluster will proceed
	 * to expire the file and it may take several days (usually 30) until the blob
	 * is purged from the cluster.
	 * 
	 * @param string $filename
	 * @return boolean
	 */
	public function remove($filename) {
		$r = $this->master->request(sprintf('/media/delete/%s/%s.json', $this->uniqid, $filename));
		$r->get('signature', (string)$this->ctx->signature());
		$r->send()->expect(200)->json();
		
		return true;
	}
	
	/**
	 * Returns the context for this bucket.
	 * 
	 * @return Cloudy
	 */
	public function getCloudy() {
		return $this->ctx;
	}
	
	/**
	 * Returns the master server for this bucket.
	 * 
	 * @return Server
	 */
	public function getMaster() {
		return $this->master;
	}
	
	/**
	 * Returns the uniqid of the bucket. The uniqid can be used to identify the 
	 * bucket on the cluster.
	 * 
	 * @return string
	 */
	public function uniqid() {
		return $this->uniqid;
	}
	
}
