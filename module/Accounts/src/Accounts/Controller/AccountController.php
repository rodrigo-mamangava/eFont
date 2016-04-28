<?php

namespace Accounts\Controller;

use Validador\Controller\ValidadorController;

class AccountController extends \Application\Controller\ApplicationController {
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		return $this->viewModel;
	}
	
	/**
	 * Criacao de conta
	 *
	 * @return \Application\Controller\Zend\View\Model\ViewModel
	 */
	public function signUpAction() {
		// Request
		$Request = $this->getRequest ();
		$status = $outcome = false;
		$data = 'Please use only letters (a-z), numbers, and periods.';
		// POST
		if ($Request->isPost ()) {
			try {
				$post = $this->postJsonp ();
				
				$firstname = isset ( $post ['firstname'] ) ? $post ['firstname'] : null;
				$lastname = isset ( $post ['lastname'] ) ? $post ['lastname'] : null;
				$fullname = isset ( $post ['fullname'] ) ? $post ['fullname'] : $firstname . ' ' . $lastname;
				$phone = isset ( $post ['tel'] ) ? $post ['tel'] : null;
				
				$email = isset ( $post ['remail'] ) ? strtolower ( ValidadorController::removeBlank ( $post ['remail'] ) ) : null;
				$username = isset ( $post ['username'] ) ? strtolower ( ValidadorController::removeBlank ( $post ['username'] ) ) : $email;
				$password = isset ( $post ['rpassword'] ) ? $post ['rpassword'] : null;
				
				$address = isset ( $post ['address'] ) ? $post ['address'] : '';
				$address_complement = isset ( $post ['complement'] ) ? $post ['complement'] : '';
				$address_state = '';
				$address_city = isset ( $post ['city'] ) ? $post ['city'] : '';
				$address_country = isset ( $post ['country'] ) ? $post ['country'] : '';
				$address_postcode = isset ( $post ['postcode'] ) ? $post ['postcode'] : '';
				// Default, para uso futuro
				$privilege_type_id = 3;
				$provider= 'register';
				
				// Validacao basica
				if (! ValidadorController::isValidEmail ( $email )) {
					throw new \Exception ( $this->translate ( 'Email' ) . ' ' . $this->translate ( 'You can\'t leave this empty.' ) );
				} elseif (! ValidadorController::isValidSenha ( $password )) {
					throw new \Exception ( $this->translate ( 'Password: Short passwords are easy to guess. Try one with at least 8 characters. <br/> Use at least 8 characters. Don\'t use a password from another site or something too obvious like your pet\'s name.' ) );
				} else {
					// Controller necessarios
					$CompanyController = new \Shop\Controller\CompanyController ( $this->getServiceLocator () );
					$UserController = new \Shop\Controller\UserSystemController ( $this->getServiceLocator () );
					// Usuario ja existe ?
					$user = $UserController->findByEmail ( $email );
					if ($user) {
						throw new \Exception ( $this->translate ( 'Email already registered. Would you like try another or forgot your password?' ) );
					} else {
						$company_id = $CompanyController->save ( null, $fullname, $phone, $email, $phone, $address, $address_complement, $address_city, $address_state, $address_country, $address_postcode );
						if ($company_id) {
							// Auth
							$auth = \Accounts\Controller\AuthenticatorController::getInstance ();
							$auth->clearIdentity ();
							// Salvando novo usuario
							$rs = $UserController->save ( null, $username, $password, $email, $phone, $privilege_type_id, $company_id, 1, $firstname, $lastname, $address, $address_city, $address_complement, $address_country, $address_postcode );
							if ($rs) {
								try {
									$adapter = new \Accounts\Service\Adapter\Register ( $this->getServiceLocator (), $this->getSystemConfig () );
									$adapter->setUsernameAndPassword ( $email, $password );
									$result = $auth->authenticate ( $adapter );
									
									if (isset ( $result ) && ! $result->isValid ()) {
										throw new \Exception ( $this->translate ( 'Login failed!' ) );
									} else {
										$Identify = $auth->getIdentity ( $provider );
										if (method_exists ( $Identify, 'getId' )) {
											$user_id = $Identify->getId ();
											if ($user_id) {
												// Limpando Identidade para sobrecrever com id do sistema
												// Update date login
												$User = new \Shop\Controller\UserSystemController ( $this->getServiceLocator () );
												$User->updated ( $user_id, array (
														'dt_last_login_web' => date ( 'Y-m-d H:i:s' ) 
												), $Identify->getCompany_id () );
												// Fim, redireciona para a tela inicial
												$this->showResponse ( true, '/connect', $user_id );
												die ();
											} else {
												throw new \Exception ( $this->translate ( 'Unable to register the social network, please sign-up with mail or try again.' ) );
											}
										}
									}
								} catch ( \Exception $e ) {
									throw new \Exception ( $this->translate ( 'The user name or password is incorrect.' ) );
								}
							}
						}
					}
				}
			} catch ( \Exception $e ) {
				$data = $e->getMessage ();
			}
		}
		// Response
		parent::showResponse ( $status, $data, $outcome, true );
		die ();
	}
	/**
	 * Se username/email estao disponivel
	 *
	 * @throws \Exception
	 */
	public function availableAction() {
		// Request
		$Request = $this->getRequest ();
		$status = $outcome = false;
		$data = 'Please use only letters (a-z), numbers, and periods.';
		parent::showResponse ( $status, $data, $outcome, true );
		die ();
	}
	/**
	 * Redirecionamento da conexao de login/logado
	 *
	 * @return \Application\Controller\Zend\View\Model\ViewModel
	 */
	public function connectAction() {
		$auth = \Accounts\Controller\AuthenticatorController::getInstance ();
		if (! $auth->hasIdentity ()) {
			// throw new \Exception('Not logged in!', 404);
			return $this->redirect ()->toRoute ( 'home' );
			exit ();
		} else {
			return $this->redirect ()->toRoute ( 'shop-customer', array (
					'action' => 'welcome' 
			) );
		}
		return $this->viewModel;
	}
	/**
	 * Encerrando sessao
	 *
	 * @return \Application\Controller\Zend\View\Model\ViewModel
	 */
	public function logoutAction() {
		// Identidade
		$auth = \Accounts\Controller\AuthenticatorController::getInstance ();
		$auth->clearIdentity ();
		// Two Step
		$this->unsetTwoStepVerification ();
		return $this->redirect ()->toRoute ( 'home' );
	}
	/**
	 * Criando Sessao
	 *
	 * @return \Application\Controller\Zend\View\Model\ViewModel
	 */
	public function loginAction() {
		// Auth
		$auth = \Accounts\Controller\AuthenticatorController::getInstance ();
		$auth->clearIdentity ();
		// Here the response of the providers are registered
		// Request
		$Request = $this->getRequest ();
		// Validando
		try {
			if ($Request->isPost ()) {
				$provider = 'register';
				$code = $error = $error_code = false;
				
				$post = $this->postJsonp ();
				$email = strtolower ( $post ['username'] );
				$password = $post ['password'];
				// Validacao basica
				if (! ValidadorController::isValidEmail ( $email )) {
					throw new \Exception ( $this->translate ( 'Username: Please use only letters (a-z), numbers and full stops.' ) );
				} elseif (! ValidadorController::isValidNotEmpty ( $password ) || ! ValidadorController::isValidStringLength ( $password, 8, 30 ) || ! ValidadorController::isValidSenha ( $password )) {
					throw new \Exception ( $this->translate ( 'Password is not valid!.<br/>' ) . $this->translate ( 'Password: Short passwords are easy to guess. Try one with at least 8 characters. <br/>
							Use at least 8 characters. Don\'t use a password from another site or something too obvious like your pet\'s name.' ) );
				} else {
					try {
						$adapter = new \Accounts\Service\Adapter\Register ( $this->getServiceLocator (), $this->getSystemConfig () );
						$adapter->setUsernameAndPassword ( $email, $password );
						$result = $auth->authenticate ( $adapter );
						
						if (isset ( $result ) && ! $result->isValid ()) {
							throw new \Exception ( $this->translate ( 'Login failed!' ) );
						} else {
							$Identify = $auth->getIdentity ( $provider );
							if (method_exists ( $Identify, 'getId' )) {
								$user_id = $Identify->getId ();
								if ($user_id) {
									// Limpando Identidade para sobrecrever com id do sistema
									// Update date login
									$User = new \Shop\Controller\UserSystemController ( $this->getServiceLocator () );
									$User->updated ( $user_id, array (
											'dt_last_login_web' => date ( 'Y-m-d H:i:s' ) 
									), $Identify->getCompany_id () );
									// Fim, redireciona para a tela inicial
									$this->showResponse ( true, '/connect', $user_id );
									die ();
								} else {
									throw new \Exception ( $this->translate ( 'Unable to register the social network, please sign-up with mail or try again.' ) );
								}
							}
						}
					} catch ( \Exception $e ) {
						// if ($e->getCode () == 423) {
						// throw new \Exception ( $e->getMessage () );
						// }else{
						// throw new \Exception ( $e->getMessage () );
						// }
						throw new \Exception ( $this->translate ( 'The user name or password is incorrect.' ) );
					}
				}
			}
		} catch ( \Exception $e ) {
			if ($Request->isPost ()) {
				$this->showResponse ( false, $e->getMessage () );
				die ();
			} else {
				$this->viewModel->setVariables ( array (
						'FAILURE' => $e->getMessage () 
				) );
			}
		}
		return $this->viewModel;
	}
	/**
	 * Redirecionamento atraves de login social
	 *
	 * @return \Application\Controller\Zend\View\Model\ViewModel
	 */
	public function socialAction() {
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Iniciando processo de lembrete senha
	 *
	 * @return \Application\Controller\Zend\View\Model\ViewModel
	 */
	public function forgetAction() {
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Resetando a senha
	 *
	 * @return \Application\Controller\Zend\View\Model\ViewModel
	 */
	public function resetAction() {
		return $this->viewModel->setTerminal ( true );
	}
}