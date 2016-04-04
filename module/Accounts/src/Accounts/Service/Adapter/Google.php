<?php

namespace Accounts\Service\Adapter;

use \Accounts\Service\Identity\Google as Identity;
use \Accounts\Service\OAuth2\Consumer as Consumer;
use \Zend\Authentication\Result as Result;

/**
 * Conexao com o Google
 * @author Calraiden
 *
 */
class Google implements \Zend\Authentication\Adapter\AdapterInterface {
	protected $_accessToken;
	protected $_requestToken;
	protected $_options;
	/**
	 * Constructor
	 * @param unknown $requestToken
	 * @param string $options
	 */
	public function __construct($requestToken, $options = NULL) {
		$this->_setOptions ( $options );
		$this->_setRequestToken ( $requestToken );
	}
	/**
	 * (non-PHPdoc)
	 * @see \Zend\Authentication\Adapter\AdapterInterface::authenticate()
	 */
	public function authenticate() {
		$result = array ();
		$result ['code'] = Result::FAILURE;
		$result ['identity'] = NULL;
		$result ['messages'] = array ();
		if (! array_key_exists ( 'error', $this->_accessToken )) {
			$result ['code'] = Result::SUCCESS;
			$result ['identity'] = new Identity ( $this->_accessToken );
		}
		return new Result ( $result ['code'], $result ['identity'], $result ['messages'] );
	}
	/**
	 * Obtem URL de autenticacao
	 * @param unknown $config
	 * @return string
	 */
	public static function getAuthorizationUrl($config) {
		$options = is_object ( $config ) ? $config->toArray () : $config;
		return Consumer::getAuthorizationUrl ( $options ['Google'] );
	}
	/**
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
	 * 
	 * @param string $options
	 */
	protected function _setOptions($options = null) {
		$options = is_object ( $options ) ? $options->toArray () : $options;
		$this->_options = $options ['Google'];
	}
}