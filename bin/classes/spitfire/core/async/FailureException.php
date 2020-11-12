<?php namespace spitfire\core\async;

class FailureException extends \spitfire\exceptions\PrivateException
{
	
	private $extended;
	
	public function __construct(string $message = "", int $code = 0, string $extended = "") {
		$this->extended = $extended;
		parent::__construct($message, $code, null);
	}

	
	public function getExtended() {
		return $this->extended;
	}

	
}
