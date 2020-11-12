<?php namespace cloudy\sf;

use auth\SSOCache;
use cloudy\Bucket;
use cloudy\Cloudy;
use spitfire\exceptions\PrivateException;
use spitfire\storage\objectStorage\DriverInterface;
use spitfire\storage\objectStorage\IOStream;
use function request;
use function spitfire;

/**
 * An SF Mount allows the system to use Spitfire's virtual storage in order to
 * access files and blobs. We use virtual disks so we can actually plug stuff 
 * like CloudyNAS into a working system and it will continue to operate as intended.
 * 
 * One of the major advantages of a system like this is that integrations like 
 * Cloudy allow offloading the distribution of the files to them. This makes it 
 * easy to have four servers hosting your files with an integration that takes
 * just a few minutes to make.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class Mount implements DriverInterface 
{
	
	/**
	 * A connection to the cluster so blobs and data can be stored / read from
	 * the servers.
	 *
	 * @var Cloudy
	 */
	private $cloudy;
	
	/**
	 * An array of buckets that can be used to cache communication between the server
	 * and cloudy's leader server to retrieve information about the bucket.
	 * 
	 * @var Bucket[]
	 */
	private $buckets = [];
	
	/**
	 * Instance a new Virtual disk.
	 * 
	 * @param string $dsn
	 */
	public function __construct($dsn) {
		list($endpoint, $sso) = explode('|', $dsn, 2);
		$this->cloudy = new Cloudy($endpoint, new SSOCache($sso));
	}
	
	/**
	 * Retrieve the bucket's metadata from CloudyNAS.
	 * 
	 * @param type $id
	 * @return Bucket
	 */
	private function bucket($id) {
		if (isset($this->buckets[$id])) { return $this->buckets[$id]; }
		
		$this->buckets[$id] = $this->cloudy->bucket($id);
		return $this->buckets[$id];
	}
	
	/**
	 * Analyzes a location string and returns the bucket (and potentially) the name
	 * to locate the blob with.
	 * 
	 * @param string $location
	 * @return string[]
	 * @throws PrivateException
	 */
	private function parse($location) {
		$pieces = explode('/', $location);
		
		if (count($pieces) === 2) { return $pieces; }
		if (count($pieces) === 1) { return [$pieces[0], null]; }
		throw new PrivateException('Invalid path', 2006091131);
	}
	
	/**
	 * Currently, cloudyNAS does not manage the last access time of a blob or file.
	 * This information is unavailable.
	 * 
	 * @param string $key
	 * @return int
	 */
	public function atime($key) {
		return null;
	}
	
	/**
	 * Returns whether a file is available in a bucket or not.
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function contains($key) {
		$path = $this->parse($key);
		
		try {
			$bucket = $this->bucket($path[0]);
			$bucket->getMedia($path[1]);
			return true;
		}
		catch (PrivateException$e) {
			return false;
		}
	}
	
	/**
	 * Removes the blob from cloudyNAS. Please note that this is advisory deletion,
	 * meaning that cloudy will not immediately remove the blob, it may take some
	 * time before it's really gone for good.
	 * 
	 * @param string $key
	 */
	public function delete($key) {
		
		$path = $this->parse($key);
		
		$bucket = $this->bucket($path[0]);
		$bucket->getMedia($path[1])->delete();
	}

	public function mime($key) {
		
		$path = $this->parse($key);
		
		$bucket = $this->bucket($path[0]);
		return $bucket->getMedia($path[1])->getMime();
	}

	public function mtime($key) {
		return null;
	}
	
	/**
	 * Gets the contents of a blob from cloudy. Please note that this is not the
	 * intended behavior for cloudy servers. Usually they expect the file to be
	 * uploaded and then manage the distribution.
	 * 
	 * @param type $key
	 * @return type
	 * @throws PrivateException
	 */
	public function read($key) {
		
		$path = $this->parse($key);
		
		$bucket = $this->bucket($path[0]);
		$media  = $bucket->getMedia($path[1]);
		
		$servers = $media->getServers();
		
		if (!count($servers)) {
			throw new PrivateException('No servers for ' . $this->media->getName());
		}
		
		$server  = $servers[rand(0, count($servers) - 1)]->getEndpoint();
		$links   = $media->getLinks();
		
		$r = request($server . '/file/retrieve/link/' . reset($links));
		$r->get('signature', (string)$media->getBucket()->getCloudy()->signature());
		
		spitfire()->log(sprintf('Trying to fetch file %s from %s', $media->getUniqid(), $server));
		
		return $this->body = $r->send()->expect(200)->html();
	}

	public function readonly($key) {
		return false;
	}

	public function stream($key): IOStream {
		throw new PrivateException('Unimplemented', 2006091140);
	}
	
	/**
	 * Generates a URL that the server can use to direct the user to a slave that
	 * will host the file
	 * 
	 * @param string $key
	 * @param int|null $ttl
	 * @return string
	 * @throws PrivateException
	 */
	public function url($key, $ttl) {
		
		$path = $this->parse($key);
		
		$bucket  = $this->bucket($path[0]);
		$media   = $bucket->getMedia($path[1]);
		$servers = $media->getServers();
		
		
		if ($ttl) {
			$links = [$media->makeLink($ttl)];
		}
		else {
			$links = $media->getLinks();
		}
		
		if (!count($servers)) {
			throw new PrivateException('No servers for ' . $this->media->getName());
		}
		
		return $servers[rand(0, count($servers) - 1)]->getEndpoint() . '/file/retrieve/link/' . reset($links);
	}
	
	/**
	 * Writes the contents to cloudy.
	 * 
	 * @param string $key
	 * @param string $contents
	 * @param int $ttl Is ignored
	 * @return int The amount of bytes written
	 */
	public function write($key, $contents, $ttl = null) {
		
		
		$path   = $this->parse($key);
		$bucket = $this->bucket($path[0]);
		
		$tmp  = tmpfile();
		fwrite($tmp, $contents);
		
		$meta_data = stream_get_meta_data($tmp);
		$filename = $meta_data["uri"];
		
		$this->media = $bucket->upload($filename, basename($key));
		$this->body = $contents;
		return strlen($contents);
	}

}
