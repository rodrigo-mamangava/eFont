<?php

namespace Accounts\Service\Adapter;

use \Zend\Authentication\Adapter\DbTable;
use \Zend\Authentication\Result as Result;
use \Zend\Authentication\AuthenticationService;
use \Accounts\Service\Identity\Register as Identity;
use \Validador\Controller\ValidadorController;

/**
 * Conexao com o autenticacao via registro
 *
 * @author Calraiden
 *        
 */
class Register implements \Zend\Authentication\Adapter\AdapterInterface {
	protected $_serviceLocator;
	protected $_options;
	protected $_username;
	protected $_password;
	
	/**
	 *
	 * @param unknown $ServiceLocator        	
	 * @param string $options        	
	 */
	public function __construct($ServiceLocator, $options = NULL) {
		$this->_setOptions ( $options );
		$this->_setServiceLocator ( $ServiceLocator );
	}
	
	/**
	 *
	 * @param unknown $ServiceLocator        	
	 */
	protected function _setServiceLocator($ServiceLocator) {
		$this->_serviceLocator = $ServiceLocator;
	}
	
	/**
	 *
	 * @param unknown $username        	
	 */
	public function setUsernameAndPassword($username, $password) {
		$this->_username = $username;
		$this->_password = $password;
	}
	
	/**
	 *
	 * @param string $options        	
	 */
	protected function _setOptions($options = null) {
		$options = is_object ( $options ) ? $options->toArray () : $options;
		$this->_options = $options ['Register'];
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see \Zend\Authentication\Adapter\AdapterInterface::authenticate()
	 */
	public function authenticate() {
		$username = $this->_username;
		$password = $this->_password;
		// Validacao basica
		if (! ValidadorController::isValidUsername ( $username ) && ! ValidadorController::isValidEmail ( $username )) {
			throw new \Exception ( 'The USERNAME or EMAIL is invalid. An illegal character was encountered. Please, should be contain letters, numbers, underscores (_), dot or period (.) (-).'. " - [1009]" );
		} elseif (! ValidadorController::isValidSenha ( $password )) {
			throw new \Exception ( 'The value provided as the current password is incorrect.'. " - [1010]" );
		} else {
			// Default
			$result = array ();
			$result ['code'] = Result::FAILURE;
			$result ['identity'] = NULL;
			$result ['messages'] = array ();
			// Adapter
			$zendDb = $this->_serviceLocator->get ( 'Accounts' );
			$authAdapter = new DbTable ( $zendDb, $this->_options ['users_table'], $this->_options ['username_column'], $this->_options ['password_column']);
			// Setando credenciais
			$authAdapter->setCredentialTreatment ( 'MD5(?)' );
			$authAdapter->setIdentity ( $username );
			$authAdapter->setCredential ( $password );

			// Autenticando 
			$authService = new AuthenticationService ();
			$Result = $authService->authenticate ( $authAdapter );
			//var_dump($Result); exit;
			switch ($Result->getCode ()) {
				case Result::FAILURE :
					throw new \Exception ( 'General Failure'. " - [1011]" , 423);
					break;
				case Result::FAILURE_IDENTITY_NOT_FOUND :
					throw new \Exception ( 'The user name or password is incorrect.'. " - [1012]" , 423);
					break;
				
				case Result::FAILURE_IDENTITY_AMBIGUOUS :
					throw new \Exception ( 'The user name or password is incorrect.'. " - [1013]", 423 );
					break;
				
				case Result::FAILURE_CREDENTIAL_INVALID :
					throw new \Exception ( 'The user name or password is incorrect.'. " - [1014]", 423 );
					break;
				
				case Result::FAILURE_UNCATEGORIZED :
					throw new \Exception ( 'The user name or password is incorrect.'. " - [1015]" , 423);
					break;
				
				case Result::SUCCESS :
					// Se validou, salvamos os dados da sessao para ser usado depois na consulta do usuario logado
					$user = $authAdapter->getResultRowObject ( null, 'password' );
					if (isset ( $user->status )) {
						if ($user->status == 0 || $user->status == null) {
							throw new \Exception ( 'Your account has not been activated, if you have not received the email, please complete the registration again and wait.'. " - [1016]", 423 );
							return;
						} elseif ($user->status == 2) {
							throw new \Exception ( 'The account is unavailable, or access has been denied, please contact support service.'. " - [1017]", 423 );
							return;
						}
					}
					
					// Removed?
					if (isset ( $user->removed )) {
						if ($user->removed == 1 || $user->removed > 0) {
							throw new \Exception ( 'The account is unavailable, or access has been denied, please contact support service.'. " - [1018]", 423 );
							return;
						}
					}
					//Empresa Desabilitada
					$Company = new \Shop\Controller\CompanyController($this->_serviceLocator);
					$appos_customers = $Company->find($user->company_id);
					if($appos_customers){
						$removed = isset($appos_customers['removed']) ? $appos_customers['removed'] : 0; 
						if ($removed == 1 ||$removed > 0) {
							$authService->clearIdentity();
							throw new \Exception ( 'The company is unavailable, or access has been denied, please contact support service.'. " - [1019]", 423 );
							return;
						}
					}else{
						throw new \Exception ( 'The company is unavailable, or access has been denied, please contact support service.'. " - [1020]", 423 );
						return;						
					}
					// Result/Array
					$result ['code'] = Result::SUCCESS;
					
					$user_id = $user->{$this->_options ['id_column']};
					$company_id = $user->{$this->_options ['company_id_column']};
					
					$result ['identity'] = new Identity ( array (
							'name' => $user->{$this->_options ['name_column']},
							'id' => $user_id,
							'email' => $user->{$this->_options ['email_column']},
							'username' => $user->{$this->_options ['username_column']},
							'company_id' => $company_id,
							'privilege_type' => $user->{$this->_options ['privilege_type_column']},
							'two_factor' => $user->{$this->_options ['two_factor']},
							'two_factor_secret' => $user->{$this->_options ['two_factor_secret']},
							'image' => $user->{$this->_options ['image']}
					) );
					break;
				default :
					throw new \Exception ( 'The user name or password is incorrect.'. " - [2018]" );
			}
		}
		// Finality
		return new Result ( $result ['code'], $result ['identity'], $result ['messages'] );
	}
}