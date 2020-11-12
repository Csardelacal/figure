<?php namespace spitfire\core\async;

use Serializable;

abstract class Task implements Serializable
{
	
	private $settings;
	
	public function __construct($settings) {
		$this->settings = $settings;
	}
	
	public function serialize() {
		return serialize(['settings' => $this->settings]);
	}
	
	public function unserialize($serialized) {
		$data = unserialize($serialized);
		$this->settings = $data['settings'];
	}
	
	/**
	 * 
	 * @throws FailureException
	 */
	abstract function body() : Result;
	
	/**
	 * 
	 * @param FailureException $e
	 * @return Result
	 */
	public function handleFailure(FailureException$e): Result {
		return new Result(sprintf('%s %s%s%s', $e->getCode(), $e->getMessage(), PHP_EOL, $e->getExtended()));
	}
	
	public function getSettings() {
		return $this->settings;
	}
}
