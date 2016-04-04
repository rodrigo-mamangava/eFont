<?php

namespace Accounts\Service\Identity;

class Generic {
	protected $_id;
	protected $_name;
	protected $_email;
	protected $_username;
	protected $_company_id;
	protected $_privilege_type;
	protected $_two_factor;
	protected $_two_factor_secret;
	protected $_image;
	
	
	public function __construct($name, $id, $email = null, $username = null, $company_id = null, $privilege_type = null, $two_factor = null , $two_factor_secret = null, $image = null) {
		$this->_name = $name;
		$this->_id = $id;
		$this->_email = $email;
		$this->_username = $username;
		$this->_company_id = $company_id;
		$this->_privilege_type = $privilege_type;
		$this->_two_factor = $two_factor;
		$this->_two_factor_secret = $two_factor_secret;
		$this->_image = $image;
	}
	public function getName() {
		return $this->_name;
	}
	public function getId() {
		return $this->_id;
	}
	public function setId($id){
		$this->_id = $id;
	}
	public function getEmail() {
		return $this->_email;
	}
	public function getUsername() {
		return $this->_username;
	}
	public function getCompany_id(){
		return $this->_company_id;
	}
	public function getPrivilege_type(){
		return $this->_privilege_type;
	}
	
	public function getTwo_factor(){
		return $this->_two_factor;
	}
	
	public function getTwo_factor_secret(){
		return $this->_two_factor_secret;
	}
	public function getImage() {
		return $this->_image;
	}	
}