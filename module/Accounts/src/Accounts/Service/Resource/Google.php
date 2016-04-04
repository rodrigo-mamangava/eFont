<?php

namespace Accounts\Service\Resource;

use \Accounts\Service\OAuth2\Consumer as Consumer;

class Google {
	protected $_accessToken;
	protected $data = array ();
	public function __construct($accessToken) {
		$this->_accessToken = $accessToken;
	}
	public function getId() {
		$profile = $this->getProfile ();
		return $profile ['id'];
	}
	
	/**
	 * Obtem o Email
	 *
	 * @return NULL
	 */
	public function getEmail() {
		$profile = $this->getProfile ();
		return $profile ['email'];
	}
	/**
	 * Returna o username da conta
	 * 
	 * @return NULL
	 */
	public function getUsername() {
		$profile = $this->getProfile ();
		return $profile ['name'];
	}
	public function getProfile() {
		$endpoint = 'https://www.googleapis.com/oauth2/v2/userinfo';
		return \Zend\Json\Json::decode ( $this->_getData ( 'profile', $endpoint ), \Zend\Json\Json::TYPE_ARRAY );
	}
	protected function _getData($label, $url, $redirects = true) {
		// Check
		if (! $this->_hasData ( $label )) {
			if (isset ( $this->_accessToken ['access_token'] )) {
				$value = Consumer::getData ( $url, $this->_accessToken ['access_token'], $redirects );
				$this->_setData ( $label, $value );
			} else {
				// Has error?
				if (isset ( $this->_accessToken ['error'] )) {
					throw new \Exception ( 'No Authorization Token ' . $this->_accessToken ['error'] );
				}
			}
		}
		// Verify
		if (isset ( $this->data [$label] )) {
			return $this->data [$label];
		}
		return false;
	}
	protected function _setData($label, $value) {
		$this->data [$label] = $value;
	}
	protected function _hasData($label) {
		return isset ( $this->data [$label] ) && (NULL !== $this->data [$label]);
	}
}