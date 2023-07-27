<?php namespace app\support;

use app\models\ApiToken;

class AppAuthentication
{
	
	private ?ApiToken $auth;
	
	public function __construct(?ApiToken $apiToken)
	{
		$this->auth = $apiToken;
	}
	
	public function isAuthenticated() : bool
	{
		return $this->auth !== null;
	}
	
	public function getToken() : ApiToken
	{
		assert($this->auth !== null);
		return $this->auth;
	}
}
