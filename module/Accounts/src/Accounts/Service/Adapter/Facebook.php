<?php

namespace Accounts\Service\Adapter;

use \Accounts\Service\Identity\Facebook as Identity;
use \Accounts\Service\OAuth2\Consumer;
use \Zend\Authentication\Result as Result;

/**
 * Adapter para Facebook
 *
 * @author CALRAIDEN
 *        
 */
class Facebook implements \Zend\Authentication\Adapter\AdapterInterface {
	protected $_accessToken;
	protected $_requestToken;
	protected $_options;
	protected $_config;
	/**
	 * Construtor
	 *
	 * @param string $requestToken        	
	 * @param string $options        	
	 */
	public function __construct($requestToken = NULL, $options = NULL) {
		$this->_setOptions ( $options );
		try {
			$this->_setRequestToken ( $requestToken );
		} catch ( \Exception $e ) {
			throw new \Exception ( $e->getMessage (), $e->getCode (), $e->getPrevious () );
		}
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see \Zend\Authentication\Adapter\AdapterInterface::authenticate()
	 */
	public function authenticate() {
		$result = array ();
		$result ['code'] = Result::FAILURE;
		$result ['identity'] = NULL;
		$result ['messages'] = array ();
		$identity = new Identity ( $this->_accessToken );

		if (NULL !== $identity->getId ()) {
			$result ['code'] = Result::SUCCESS;
			$result ['identity'] = $identity;
		}
		return new Result ( $result ['code'], $result ['identity'], $result ['messages'] );
	}
	/**
	 * Obtem URL para autenticacao
	 *
	 * @param unknown $config        	
	 * @return string
	 */
	public static function getAuthorizationUrl($config) {
		$options = is_object ( $config ) ? $config->toArray () : $config;
		return Consumer::getAuthorizationUrl ( $options ['Facebook'] );
	}
	/**
	 * Solicita o Token de Sessao
	 *
	 * @param unknown $requestToken        	
	 */
	protected function _setRequestToken($requestToken) {
		try {
			if (NULL === $requestToken) {
				return;
			}
			$this->_options ['code'] = $requestToken;
			$accesstoken = Consumer::getAccessToken ( $this->_options );
			$accesstoken ['timestamp'] = time ();
			$this->_accessToken = $accesstoken;
		} catch ( \Exception $e ) {
			throw new \Exception ( $e->getMessage (), $e->getCode (), $e->getPrevious () );
		}
	}
	/**
	 * Setta o Token de Sessao
	 *
	 * @param unknown $token        	
	 */
	public function setAccessToken($token) {
		$accesstoken ['timestamp'] = time ();
		$accesstoken ['access_token'] = $token;
		$this->_accessToken = $token;
	}
	/**
	 * Setta as opcoes
	 *
	 * @param string $options        	
	 */
	public function _setOptions($options = null) {
		$options = is_object ( $options ) ? $options->toArray () : $options;
		$this->_options = $options ['Facebook'];
	}
}