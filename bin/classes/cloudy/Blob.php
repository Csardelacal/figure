<?php namespace cloudy;

/**
 * A blob is a long binary object, usually a file. It is composed of a long stream
 * of data and a mime-type informing what that data contains.
 * 
 * In order for Cloudy to operate properly, we also need to know which bucket this
 * Blob comes from, which servers it can be located on, how we address it (either
 * by name or uniqid) and what secrets we have to generate links to it.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class Blob
{
	
	/**
	 * The bucket is the collection that houses this blob. Buckets can contain
	 * an almost infinite amount of blobs.
	 *
	 * @var Bucket
	 */
	private $bucket;
	
	/**
	 * The name of the blob can be used by humans to identify the blob. It makes
	 * it easier to understand what the blob is to a human.
	 *
	 * @var string
	 */
	private $filename;
	
	/**
	 * The uniqid is a unique identifier that is fixed in length and therefore is
	 * usually more consistent than the filename (which can be up to hundreds of
	 * characters long or just a few characters short)
	 *
	 * @var string
	 */
	private $uniqid;
	
	/**
	 * The mime-type allows an application interacting with the blob understand 
	 * how the blob should be treated. Certain applications, for example, may support
	 * compressing images but not video, and they need to make an informed decision
	 * on whether they can use the blob to work.
	 *
	 * @var string
	 */
	private $mime;
	
	/**
	 * A collection of secrets that are used to generate links. When a blob is shared,
	 * to only a select few users, the system can generate a link that expires regularly
	 * preventing files linked from being accessible for too long before they
	 * expire and can no longer be used.
	 *
	 * @var string[]
	 */
	private $links;
	
	/**
	 * This is a collection of servers housing the blob on Cloudy's end. Any of
	 * these servers can be linked to in order to retrieve the file.
	 *
	 * @var Server[]
	 */
	private $servers;
	
	/**
	 * 
	 * @param Bucket $bucket
	 * @param string $filename
	 * @param string $uniqid
	 */
	public function __construct($bucket, $filename, $uniqid = null) {
		$this->bucket = $bucket;
		$this->filename = $filename;
		$this->uniqid = $uniqid;
	}
	
	/**
	 * 
	 * @return Bucket
	 */
	public function getBucket() {
		return $this->bucket;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getUniqid() {
		return $this->uniqid;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->filename;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getMime() {
		return $this->mime;
	}
	
	/**
	 * 
	 * @param string $mime
	 * @return Blob
	 */
	public function setMime($mime) {
		$this->mime = $mime;
		return $this;
	}
	
	/**
	 * 
	 * @return string[]
	 */
	public function getLinks() {
		return $this->links;
	}
	
	/**
	 * 
	 * @param string[] $links
	 * @return Blob
	 */
	public function setLinks($links) {
		$this->links = $links;
		return $this;
	}
	
	/**
	 * Creates a new link with a given TTL to share with a user. Depending on whether
	 * the link has a TTL, it will automatically expire after the TTL has expired,
	 * or it will be available until revoked.
	 * 
	 * @param int $ttl
	 * @return string
	 */
	public function makeLink($ttl = null) {
		$r = $this->bucket->getMaster()->request('/link/create.json');
		$r->get('signature', (string)$this->bucket->getCloudy()->signature());
		$r->post('media', $this->uniqid);
		$r->post('bucket', $this->bucket->uniqid());
		$r->post('name', $this->filename);
		$r->post('ttl', $ttl);
		
		$link = $r->send()->expect(200)->json();
		
		return $link->uniqid;
	}
	
	/**
	 * 
	 * @return Server[]
	 */
	public function getServers() {
		return $this->servers;
	}
	
	/**
	 * 
	 * @param Server[] $servers
	 * @return Blob
	 */
	public function setServers($servers) {
		$this->servers = $servers;
		return $this;
	}
	
	/**
	 * Removes a this blob from the cloudyNAS cluster.
	 * 
	 * @return bool
	 */
	public function delete() {
		return $this->bucket->remove($this->filename);
	}
	
}
