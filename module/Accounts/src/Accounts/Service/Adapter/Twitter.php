<?php

namespace Accounts\Service\Adapter;

use \Accounts\Service\Auth\Identity\Twitter as Identity;

use \Zend\Authentication\Result as Result;
use \ZendOAuth\Consumer as Consumer;
use \Zend_Session_Namespace as SessionNameSpace;


class Twitter implements \Zend\Authentication\Adapter\AdapterInterface {
	protected $_accessToken;
	protected $_requestToken;
	protected $_params;
	protected $_options;
	protected $_consumer;
	/**
	 * Construtor
	 * @param unknown $params
	 */
	public function __construct($params) {
		$this->_setOptions ();
		$this->_consumer = new Consumer ( $this->_options );
		$this->_setRequestToken ( $params );
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
		$data = array (
				'tokens' => array (
						'access_token' => $this->_accessToken 
				) 
		);
		$identity = new Identity ( $this->_accessToken, $this->_options );
		$result ['code'] = Result::SUCCESS;
		$result ['identity'] = $identity;
		return new Result ( $result ['code'], $result ['identity'], $result ['messages'] );
	}
	/**
	 * Retorna a URL de autenticacao
	 */
	public static function getAuthorizationUrl($config) {
		$options = is_object ( $config ) ? $config->toArray () : $config;
		
		$consumer = new Consumer ( $options ['Twitter'] );
		$token = $consumer->getRequestToken ();
		$twitterToken = new SessionNamespace ( 'twitterToken' );
		$twitterToken->rt = serialize ( $token );
		
		return $consumer->getRedirectUrl ( null, $token );
	}
	/**
	 * Seta as opcoes
	 * @param string $options
	 */
	protected function _setOptions($options = null) {
		$config = $this->SystemConfig();
		$options = is_object ( $config ) ? $config->toArray () : $config;
		$this->_options = $options ['Twitter'];
	}
	/**
	 * Executa a solicitacao do token
	 * @param unknown $params
	 */
	protected function _setRequestToken($params) {
		$twitterToken = new SessionNameSpace ( 'twitterToken' );
		$token = unserialize ( $twitterToken->rt );
		$accesstoken = $this->_consumer->getAccessToken ( $params, $token );
		unset ( $twitterToken->rt );
		$this->_accessToken = $accesstoken;
	}
}