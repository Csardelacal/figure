<?php namespace auth;

use Exception;

class User
{
	
	private $id;
	private $username;
	private $aliases;
	private $groups;
	private $verified;
	private $registered;
	private $attributes;
	private $avatar;
	
	public function __construct($id, $username, $aliases, $groups, $verified, $registered, $attributes, $avatar) {
		$this->id = $id;
		$this->username = $username;
		$this->aliases = $aliases;
		$this->groups = $groups;
		$this->verified = $verified;
		$this->registered = $registered;
		$this->attributes = $attributes;
		$this->avatar = $avatar;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getUsername() {
		return $this->username;
	}
	
	public function getAvatar($size) {
		return $this->avatar->{$size};
	}
	
	public function getAttribute($name) {
		if (!isset($this->attributes->{$name})) { throw new Exception("Attribute {$name} is not set"); }
		if (!is_object($this->attributes->{$name})) { return $this->attributes->{$name}->value; }
		
		$data = $this->attributes->{$name};
		
		if ($data->value === null) {
			throw new Exception("Attribute {$name} is not set");
		}
		
		switch($data->type) {
			case 'file': return $data->value === null? null : new File($data->value->preview, $data->value->download);
			case 'text': return $data->value;
			default: throw new Exception('Invalid data type');
		}
	}
	
}

