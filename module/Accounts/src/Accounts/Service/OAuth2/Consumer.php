<?php

namespace Accounts\Service\OAuth2;

class Consumer {
	/**
	 * Montar a URL da requisicao
	 *
	 * @param unknown $urlparams        	
	 * @throws \Exception
	 * @return string
	 */
	public static function getAuthorizationUrl($urlparams) {
		$authparams = array ();
		if (! isset ( $urlparams ['auth_url'] ))
			throw new \Exception ( 'No auth url specified' );
		if (isset ( $urlparams ['client_id'] ))
			$authparams ['client_id'] = $urlparams ['client_id'];
		if (isset ( $urlparams ['state'] ))
			$authparams ['state'] = $urlparams ['state'];
		if (isset ( $urlparams ['redirect_uri'] ))
			$authparams ['redirect_uri'] = $urlparams ['redirect_uri'];
		if (isset ( $urlparams ['scope'] ))
			$authparams ['scope'] = $urlparams ['scope'];
		if (isset ( $urlparams ['response_type'] ))
			$authparams ['response_type'] = $urlparams ['response_type'];
		if (isset ( $urlparams ['status'] ))
			$authparams ['status'] = $urlparams ['status'];
		if (isset ( $urlparams ['cookie'] ))
			$authparams ['cookie'] = $urlparams ['cookie'];
		
		$authparams ['display'] = isset ( $urlparams ['display'] ) ? $urlparams ['display'] : 'popup';
		$out = $urlparams ['auth_url'] . '?' . http_build_query ( $authparams );
		return $out;
	}
	/**
	 * Montar a URL para obter o Token
	 *
	 * @param unknown $urlparams        	
	 * @throws \Exception
	 * @return array|unknown
	 */
	public static function getAccessToken($urlparams) {
		$authparams = array ();
		if (! isset ( $urlparams ['token_url'] ))
			throw new \Exception ( 'No token url specified' );
		if (isset ( $urlparams ['client_id'] ))
			$authparams ['client_id'] = $urlparams ['client_id'];
		if (isset ( $urlparams ['client_secret'] ))
			$authparams ['client_secret'] = $urlparams ['client_secret'];
		if (isset ( $urlparams ['redirect_uri'] ))
			$authparams ['redirect_uri'] = $urlparams ['redirect_uri'];
		if (isset ( $urlparams ['scope'] ))
			$authparams ['scope'] = $urlparams ['scope'];
		if (isset ( $urlparams ['code'] ))
			$authparams ['code'] = $urlparams ['code'];
		if (isset ( $urlparams ['grant_type'] ))
			$authparams ['grant_type'] = $urlparams ['grant_type'];
		try {
			return self::getSend ( $urlparams ['token_url'], $authparams );
		} catch ( \Exception $e ) {
			throw new \Exception ( $e->getMessage (), $e->getCode (), $e->getPrevious () );
		}
	}
	/**
	 * Funcao de requisicao
	 *
	 * @param unknown $url        	
	 * @param unknown $urlparams        	
	 * @param string $redirects        	
	 * @param string $method        	
	 * @return array|unknown|boolean
	 */
	public static function getSend($url, $urlparams, $redirects = false, $method = 'POST', $token = NULL) {
		// Adapter
		$adapter = new \Zend\Http\Client\Adapter\Curl ();
		$adapter = $adapter->setCurlOption ( CURLOPT_SSL_VERIFYHOST, false );
		$adapter = $adapter->setCurlOption ( CURLOPT_SSL_VERIFYPEER, false );
		// HTTP
		$client = new \Zend\Http\Client ();
		$client->setAdapter ( $adapter );
		$client->setUri ( $url );
		$response = false;
		// Send/Request
		if ($method == 'POST') {
			$client->setParameterPost ( $urlparams );
			$client->setMethod ( 'POST' );
			$response = $client->send ();
			list ( $contentType, $_ ) = explode ( ';', $response->getHeaders ( "Content-type" )->toString (), 2 );
			if (trim ( $contentType ) == "application/json") {
				return \Zend\Json\Json::decode ( $response->getBody ()->toString (), \Zend\Json\Json::TYPE_ARRAY );
			}elseif(trim ( $contentType ) == "Content-Type: application/json"){
				return \Zend\Json\Json::decode ( $response->getBody (), \Zend\Json\Json::TYPE_ARRAY );
			} else {
				$body = $response->getBody ();
				try {
					if (\Zend\Json\Json::decode ( $body )) { // Is Json?
						$body = \Zend\Json\Json::decode ( $body, \Zend\Json\Json::TYPE_OBJECT );
						if (isset ( $body->error )) {
							$message = isset ( $body->error->message ) ? $body->error->message : $body->error;
							$code = isset ( $body->error->code ) ? $body->error->code : null;
							throw new \Exception ( $message, $code );
						}
					}
				} catch ( \Zend\Json\Exception\RuntimeException $e ) {
					// None, nao eh um Json
				}
				$token;
				parse_str ( $response->getBody (), $token );
				return $token;
			}
		} else {
			$client->setParameterGet ( array (
					'access_token' => $token 
			));
			
			if ($redirects) {
				$client->setMethod ( 'GET' );
				$response = $client->send ()->getBody ();
			} else {
				$client->setOptions ( array (
						'maxredirects' => 0 
				) );
				$response = $client->send ();
				$responseHeaders = $response->getHeaders ();
				if ($responseHeaders->has ( 'Location' )) {
					$subject = $responseHeaders->get ( 'Location' );
					$response = str_replace(array('Location: '), '', $subject);
					$client->setOptions ( array (
							'maxredirects' => 5 
					) );
				}
			}
			return $response;
		}
		return false;
	}
	/**
	 * Obtem dados com o token via GET/HEADER
	 *
	 * @param unknown $url        	
	 * @param unknown $accesstoken        	
	 * @param string $redirects        	
	 * @return Ambigous <multitype:, \Accounts\Service\OAuth2\unknown, boolean>
	 */
	public static function getData($url, $accesstoken, $redirects = true) {
		return self::getSend ( $url, null, $redirects, 'GET', $accesstoken );
	}
}