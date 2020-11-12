<?php namespace spitfire\core\async;

class Result
{
	
	private $payload;
	
	public function __construct($payload) {
		$this->payload = $payload;
	}
	
	public function getPayload() {
		return $this->payload;
	}
	
}
