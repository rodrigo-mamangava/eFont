<?php

namespace Accounts\Service\Identity;

use \Accounts\Service\Resource\Facebook as Resource;

class Facebook extends \Accounts\Service\Identity\Generic {
	protected $_api;
	public function __construct($token) {
		$this->_api = new Resource ( $token );
		$this->_name = 'facebook';
		$this->_id = $this->_api->getId ();
		$this->_username = $this->_api->getUsername();
		$this->_email = $this->_api->getEmail();
	}
	public function getApi() {
		return $this->_api;
	}
}