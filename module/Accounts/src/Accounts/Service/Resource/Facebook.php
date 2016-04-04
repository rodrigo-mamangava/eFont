<?php

namespace Accounts\Service\Resource;

use \Accounts\Service\OAuth2\Consumer as Consumer;

/**
 * Conexao com o Facebook
 * 
 * @author CALRAIDEN
 *        
 */
class Facebook {
	protected $_accessToken;
	protected $data = array ();
	/**
	 * Constructor
	 * 
	 * @param unknown $accessToken        	
	 */
	public function __construct($accessToken) {
		$this->_accessToken = $accessToken;
	}
	/**
	 * Obtem o ID
	 * 
	 * @return NULL
	 */
	public function getId() {
		$endpoint = 'https://graph.facebook.com/v2.2/me?fields=id';
		$data = \Zend\Json\Json::decode ( $this->_getData ( 'id', $endpoint ), \Zend\Json\Json::TYPE_OBJECT );
		if (isset ( $data->error )) {
			return NULL;
		}
		return $data->id;
	}
	
	/**
	 * Obtem o Email
	 *
	 * @return NULL
	 */
	public function getEmail() {
		$endpoint = 'https://graph.facebook.com/v2.2/me?fields=email';
		$data = \Zend\Json\Json::decode ( $this->_getData ( 'email', $endpoint ), \Zend\Json\Json::TYPE_OBJECT );
		if (isset ( $data->error )) {
			return NULL;
		}
		return $data->email;
	}
	/**
	 * Returna o username da conta
	 * @return NULL
	 */
	public function getUsername(){
		$endpoint = 'https://graph.facebook.com/v2.2/me?fields=email';
		$data = \Zend\Json\Json::decode ( $this->_getData ( 'email', $endpoint ), \Zend\Json\Json::TYPE_OBJECT );
		if (isset ( $data->error )) {
			return NULL;
		}
		return $data->email;
	}
	/**
	 * Obtem o profile do usuario
	 * 
	 * @return array
	 */
	public function getProfile() {
		$endpoint = 'https://graph.facebook.com/me';
		return \Zend\Json\Json::decode ( $this->_getData ( 'profile', $endpoint ), \Zend\Json\Json::TYPE_ARRAY );
	}
	/**
	 * Obtem a lista de amigos
	 */
	public function getFriends() {
		$endpoint = 'https://graph.facebook.com/me/friends';
		return \Zend\Json\Json::decode ( $this->_getData ( 'friends', $endpoint ), \Zend\Json\Json::TYPE_OBJECT )->data;
	}
	/**
	 * Obtem a imagem
	 * 
	 * @param string $large        	
	 * @return multitype:
	 */
	public function getPicture($large = false) {
		if (! $large) {
			$endpoint = 'https://graph.facebook.com/me/picture';
			return $this->_getData ( 'picture', $endpoint, false );
		} else {
			$endpoint = 'https://graph.facebook.com/me/picture?type=large';
			return $this->_getData ( 'picture_big', $endpoint, false );
		}
	}
	/**
	 * Solicita os dados do usuario com o token
	 * 
	 * @param unknown $label        	
	 * @param unknown $url        	
	 * @param string $redirects        	
	 * @return multitype:
	 */
	protected function _getData($label, $url, $redirects = true) {
		// Check
		if (! $this->_hasData ( $label )) {
			if (isset ( $this->_accessToken ['access_token'] )) {
				$value = Consumer::getData ( $url, $this->_accessToken ['access_token'], $redirects );
				$this->_setData ( $label, $value );
			} else {
				// Has error?
				if (isset($this->_accessToken ['error'])) {
					throw new \Exception ('No Authorization Token : '.$this->_accessToken ['error'] );
				}
			}
		}
		// Verify
		if (isset ( $this->data [$label] )) {
			return $this->data [$label];
		}
		return false;
	}
	/**
	 * Setta os dados
	 * 
	 * @param unknown $label        	
	 * @param unknown $value        	
	 */
	protected function _setData($label, $value) {
		$this->data [$label] = $value;
	}
	/**
	 * Verifica se contem determinado dado
	 * 
	 * @param unknown $label        	
	 * @return boolean
	 */
	protected function _hasData($label) {
		return isset ( $this->data [$label] ) && (NULL !== $this->data [$label]);
	}
}