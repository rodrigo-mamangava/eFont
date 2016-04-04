<?php

namespace Accounts\Controller;

use Cryptography\Controller\CryptController;
use Validador\Controller\ValidadorController;
use RESTful\Controller\AbstractRestfulJsonController;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Authentication\Adapter\DbTable;

/**
 * HTTP Basic Authentication for API
 */
class OAuthMobileController extends AbstractRestfulJsonController {
	/**
	 * Constants compartilhada entre as APIs
	 */
	const AUTHORIZATION_HEADER = 'Authorization';
	const AUTHORIZATION_BASIC = 'BASIC';
	const ACL_CUSTOMER = 'CUSTOMER';
	const ANSWER_ACCEPTED = 'ACCEPT';
	const ANSWER_DECLINED = 'DECLINE';
	protected $_user = null;
	protected $_version = 3;
	
	/**
	 * Verificar se foi passado o token de sessao na requisicao e se eh valido
	 */
	public function onDispatch(\Zend\Mvc\MvcEvent $e) {
		// Traducao
		$language = self::getParamsfromJson ( 'language' );
		$ControlController = new \Useful\Controller\ControlController ( $this->getServiceLocator () );
		$this->_translate = $ControlController->getTranslate ();
		$this->setLocale ( $language );
		// Token
		$token = self::getTheTokenHeader ();
		// The header needs to be base64 decoded, then match the regex in order to proceed.
		$authorizationHeader = base64_decode ( $token );
		// Encontrou algo, vamos verificar se eh valido
		$decrypted = CryptController::decrypt ( $authorizationHeader );
		if (! $decrypted) { // Nao aceitou
			throw new \Exception ( $this->translate ( 'Token not acceptable' ) . " - [1001]", 401 );
		} else { // Aceitou, mas contem os campos esperados?
			if (! self::isValidToken ( $decrypted )) {
				throw new \Exception ( $this->translate ( 'Token is invalid or expired' ) . " - [1005]", 401 );
			}
		}
		// Verificando versao do token
		if(!self::checkVersion ( self::getTokenParam ( $decrypted, 'version' ) )){
			throw new \Exception ( $this->translate ( 'Upgrade Required. You need to upgrade application or try the validation number again .' ) . " - [1014]", 426 );
		}
		// Verifica a permissao do futuro para o recurso
		self::checkAcl ( $ControlController->getConfig (), $e->getRouteMatch ()->getMatchedRouteName (), self::getTokenParam ( $decrypted, 'role' ) );
		//Verifica o UUID
		if(!self::checkOAuthVerify(self::getTokenParam ( $decrypted, 'user_id' ), self::getTokenParam ( $decrypted, 'uniqid' ))){
			throw new \Exception ( $this->translate ( 'Your code has been disabled. because it was used in another phone.' ) . " - [1015]", 406 );
		}
		return parent::onDispatch ( $e );
	}
	/**
	 * Obter um param de um json
	 *
	 * @param unknown $param        	
	 * @return Ambigous <NULL, unknown>|boolean
	 */
	private function getParamsfromJson($param) {
		$body = $this->getRequest ()->getContent ();
		if (! empty ( $body )) {
			$json = json_decode ( $body, true );
			if (! empty ( $json )) {
				return isset ( $json [$param] ) ? $json [$param] : null;
			}
		}
		
		return false;
	}
	/**
	 * @setter
	 *
	 * @param array $user        	
	 */
	public function setUser($user) {
		$this->_user = $user;
	}
	/**
	 * Retorna o valor contido na sessao do usuario
	 *
	 * @param string $value        	
	 * @return boolean
	 */
	public function getUser($value) {
		if (isset ( $this->_user [$value] )) {
			return $this->_user [$value];
		}
		return false;
	}
	public function getVersion() {
		return $this->_version;
	}
	/**
	 * Chegando permissoes
	 *
	 * @param unknown $config        	
	 * @param unknown $route        	
	 * @param unknown $acl        	
	 * @throws \Exception
	 */
	private function checkAcl($config, $route, $acl) {
		if (isset ( $config ['ACL_' . strtoupper ( $acl )] )) {
			if (in_array ( $route, $config ['ACL_' . strtoupper ( $acl )] ) || in_array ( '*', $config ['ACL_' . strtoupper ( $acl )] )) {
				return true;
			}
		}
		throw new \Exception ( 'You do not have permission to access this feature ' . $acl . ' * ' . $route . " - [1002]", 403 );
	}
	/**
	 * Chega se a versao do token esta atualizada
	 * @param unknown $version
	 * @return boolean
	 */
	private function checkVersion($version) {
		if (! is_null ( $version )) {
			if ($version >= self::getVersion ()) {
				return true;
			}
		}
		return false;
	}
	/**
	 * Checa se a conexao contem o Uuid valido
	 * @param unknown $user_id
	 * @param unknown $uuid
	 * @return boolean
	 */
	private function checkOAuthVerify($user_id, $uuid){
		$OAuthVerify = new \Quiz\Controller\SecurityTokenController($this->getServiceLocator ());
		return $OAuthVerify->checkIsValid($user_id, $uuid);
	}
	/**
	 * Procede com a autenticacao e retorna o token de sessao em caso de sucesso
	 * @param unknown $company_id
	 * @param unknown $username
	 * @param unknown $uuid
	 * @param unknown $role
	 * @param unknown $sm
	 * @param unknown $remote
	 * @throws \Exception
	 */
	public function authentication($company_id, $username, $uuid, $role, $sm, $remote) {
		// Authenticate.
		try {
			$User = $this->authorization ($company_id,  $username, $uuid, $role, $sm );
			if (is_object ( $User )) {
				// Retornando
				$token = base64_encode ( CryptController::grantCryptToken ( $User->id, $uuid, $company_id, $role, $remote, self::getLocale (), self::getVersion () ) );
				// Salvando somente o user id, pois vamos precisa deles na API pulls
				$this->setUser ( array (
						'user_id' => $User->id,
						'company_id' =>$company_id
				) );
				return $token;
			}
			throw new \Exception ( $this->translate ( 'Unknown error in authentication, contact the support, please.' ) . " - [1002]", 401 );
		} catch ( \Exception $e ) {
			throw new \Exception ( $this->translate ( $e->getMessage () ) );
		}
		return;
	}
	
	/**
	 * Retira o token do header
	 *
	 * @throws \Exception
	 * @return String $token
	 */
	public function getTheTokenHeader() {
		return self::getRequestHeader($this->getRequest ());
	}
	/**
	 * 
	 * @param unknown $Request
	 * @throws \Exception
	 */
	public function getRequestHeader($Request){
		$Identity = $Request->getHeader ( self::AUTHORIZATION_HEADER );
		if (! empty ( $Identity )) {
			if ($Identity != null || $Identity != '') {
				$value = $Identity->getFieldValue ();
				$authorizationParts = explode ( ' ', $value );
				if (strtoupper ( trim ( $authorizationParts [0] ) ) == self::AUTHORIZATION_BASIC) {
					if (isset ( $authorizationParts [1] )) {
						return $authorizationParts [1];
					} else {
						throw new \Exception ( $this->translate ( 'Format token is invalid.' ) . " - [1003]", 401 );
					}
				}
			}
		}
		throw new \Exception ( 'Where is my token?', 401 );
	}
	/**
	 * Valida se o Token e valido
	 *
	 * @param String $token        	
	 * @throws \Exception
	 * @return boolean
	 */
	public function isValidToken($token) {
		if (is_null ( $token ) || strlen ( $token ) < 30) {
			throw new \Exception ( $this->translate ( 'Invalid Key/Token/Session ' ) . " - [1004]", 401 );
		} else {
			$rs = CryptController::getPrivilegesToken ( $token );
			if (! empty ( $rs ) && ! is_null ( $rs )) {
				if (isset ( $rs ['expire'] )) {
// 					if ($rs ['expire'] > time ()) { //DESABILITANDO VALIDADE DO TOKEN
						self::setUser ( $rs );
						self::setLocale ( (isset ( $rs ['local'] ) ? $rs ['local'] : 'UNK') );
						return true;
// 					}
				}
			}
		}
		return false;
	}
	
	/**
	 * Retorna um especifico valor que se encontra dentro do token
	 *
	 * @param unknown $token        	
	 * @param unknown $param        	
	 * @throws \Exception
	 * @return unknown boolean
	 */
	public function getTokenParam($decrypted, $param) {
		if (is_null ( $decrypted ) || strlen ( $decrypted ) < 30) {
			throw new \Exception ( $this->translate ( 'Invalid Key/Token/Session ' ) . " - [1004]", 401 );
		} else {
			$rs = CryptController::getPrivilegesToken ( $decrypted );
			if (! empty ( $rs ) && ! is_null ( $rs )) {
				if (isset ( $rs [$param] )) {
					return $rs [$param];
				}
			}
		}
		return false;
	}
	
	/**
	 * Executa o processo de autenticacao
	 *
	 * @param unknown $username        	
	 * @param unknown $password        	
	 * @param unknown $sm        	
	 * @throws \Exception
	 * @return Object
	 */
	protected function authorization($company_id, $username, $uuid, $role, $sm) {
		// Validacao basica
		if (! ValidadorController::isValidNotEmpty( $username )) {
			throw new \Exception ( $this->translate ( 'Invalid user' ) . " - [3040]" );
		} elseif (! ValidadorController::isValidNotEmpty ( $uuid )) {
			throw new \Exception ( $this->translate ( 'Invalid password' ) . " - [3005]" );
		} else {
			// Adapter
			$zendDb = $sm->get ( 'Adapter' );
			// Table, coluna de usuario e coluna de senha
			$table = null;
			switch ($role) {
				case 'CUSTOMER' :
					$table = 'user';
					break;
				default :
					throw new \Exception ( $this->translate ( 'Houston, We have a problem with your privileges.' ) . " - [1006]", 401 );
					break;
			}
			
			$authAdapter = new DbTable ( $zendDb, $table, 'username', 'uuid' );
			$authAdapter->setIdentity ( $username );
			$authAdapter->setCredential ( $uuid );
			$authAdapter->getDbSelect()->where("company_id='{$company_id}'"); // added
			// Instanciando o AutenticationService para fazer a altenticacao com os dados passados para o authAdapter
			$authService = new AuthenticationService ();
			$result = $authService->authenticate ( $authAdapter );
			
			switch ($result->getCode ()) {
				case Result::FAILURE :
					throw new \Exception ( $this->translate ( 'Authentication Failure' ) . " - [1013]", 401 );
					break;
				
				case Result::FAILURE_IDENTITY_NOT_FOUND :
					throw new \Exception ( $this->translate ( 'Failure due to identity not being found.' ) . " - [1007]", 401 );
					break;
				
				case Result::FAILURE_IDENTITY_AMBIGUOUS :
					throw new \Exception ( $this->translate ( 'Failure due to identity being ambiguous.' ) . " - [1008]", 401 );
					break;
				
				case Result::FAILURE_CREDENTIAL_INVALID :
					throw new \Exception ( $this->translate ( 'Failure due to invalid credential being supplied.' ) . " - [1009]", 401 );
					break;
				
				case Result::FAILURE_UNCATEGORIZED :
					throw new \Exception ( $this->translate ( 'Failure due to uncategorized reasons.' ) . " - [1010]", 401 );
					break;
				
				case Result::SUCCESS :
					// Se validou, salvamos os dados da sessao para ser usado depois na consulta do usuario logado
					$User = $authAdapter->getResultRowObject ( null, 'uuid' );
					if ($User->removed != 0) { // senao so pode ser costumer
						throw new \Exception ( $this->translate ( 'Your registration is blocked, please contact support service.' ) . " - [1011]", 423 );
						return;
					}
					
					return $User;
					
					break;
				default :
					throw new \Exception ( $this->translate ( 'Authentication Failure' ) . " - [1013]", 401 );
			}
		}
		throw new \Exception ( $this->translate ( 'Authentication Failure' ) . " - [1013]", 401 );
	}
}