<?php

use auth\SSO;
use auth\SSOCache;
use auth\Token;
use chad\Chad;
use permission\Permission;
use ping\Ping;
use spitfire\cache\MemcachedAdapter;
use spitfire\core\Environment;
use spitfire\io\session\Session;

class BaseController extends Controller
{
	
	/** @var Session */
	protected $session;
	
	/** @var SSO */
	public $sso;
	
	/** 
	 * @var Token 
	 */
	protected $token;
	
	/** @var object */
	protected $user;
	
	/**
	 *
	 * @var auth\App
	 */
	protected $authapp;
	
	protected $permission;
	
	public function _onload()
	{
		$this->sso   = new SSOCache(Environment::get('SSO'));
		
		
		$s = $this->session = Session::getInstance();
		$t = isset($_GET['token'])? $this->sso->makeToken($_GET['token']) : $s->getUser();
		
		#Create a cache to reduce the load on PHPAuth
		$c = new MemcachedAdapter();
		$c->setTimeout(120);
		
		$this->token = $t?                          $t                       : null;
		$this->user  = $t ? $c->get('token_' . $this->token->getId(), function () use ($t) { 
			return $t->isAuthenticated()? $t->getTokenInfo()->user : null; 
		}) : null;
		
		if (isset($_GET['signature']) && is_string($_GET['signature'])) {
			$this->authapp = $this->sso->authApp($_GET['signature'])->getSrc();
		}
		
		$this->view->set('authUser', $this->user);
		$this->view->set('authSSO', $this->sso);
	}
}
