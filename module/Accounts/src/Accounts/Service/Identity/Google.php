<?php

namespace Accounts\Service\Identity;

use \Accounts\Service\Resource\Google as Resource;

class Google extends Generic {
	protected $_api;
	public function __construct($token) {
		$this->_api = new Resource ( $token );
		$this->_name = 'google';
		$this->_id = $this->_api->getId ();
		$this->_username = $this->_api->getUsername();
		$this->_email = $this->_api->getEmail();
	}
	public function getApi() {
		return $this->_api;
	}
}