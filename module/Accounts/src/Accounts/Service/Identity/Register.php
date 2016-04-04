<?php

namespace Accounts\Service\Identity;

use \Accounts\Service\Resource\Register as Resource;

class Register extends \Accounts\Service\Identity\Generic {
	protected $_api;
	public function __construct($data) {
		$this->_api = new Resource ( $data );
		$this->_name = 'register';
		$this->_id = $this->_api->getId ();
		$this->_username = $this->_api->getName();
		$this->_email = $this->_api->getEmail();
		$this->_company_id = $this->_api->getCompany_id();
		$this->_privilege_type = $this->_api->getPrivilege_type();
		$this->_two_factor = $this->_api->getTwo_factor();
		$this->_two_factor_secret = $this->_api->getTwo_factor_secret();
		$this->_image = $this->_api->getImage();
	}
	public function getApi() {
		return $this->_api;
	}
	
	public function setImage($image){
		return $this->_image = $image;
	}
}