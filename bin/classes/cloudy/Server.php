<?php namespace cloudy;

/**
 * A server object represents a server within a cloudyNAS network. These will allow
 * the SDK to connect with cloudy and interact with it.
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class Server
{
	
	/**
	 * The URL the server is listening on. The SDK will make calls to a server within
	 * the scope of this URL.
	 *
	 * @var string
	 */
	private $endpoint;
	
	/**
	 * Instances a new Server connection. All the requests generated through this
	 * object are scoped to the URL of the server.
	 * 
	 * @param string $endpoint
	 */
	public function __construct($endpoint) {
		$this->endpoint = $endpoint;
	}
	
	public function getEndpoint() {
		return $this->endpoint;
	}
	
	/**
	 * Create a new request to this server. You need to provide a path to the request
	 * which should be obtained from the CloudNAS documentation.
	 * 
	 * @param string $url Path to request on the server.
	 * @return \spitfire\io\curl\Request
	 */
	public function request($url) {
		return request($this->endpoint . $url);
	}
	
}
