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
		return $this->viewModel;
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
			return $this->redirect ()->toRoute ( 'login' );
			exit ();
		} else {
			return $this->redirect ()->toRoute ( 'welcome', array ('action' => 'index'));
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
		return $this->redirect ()->toRoute ( 'login' );
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
				if (! ValidadorController::isValidUsername ( $email )) {
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
									$User = new \Quiz\Controller\UserSystemController ( $this->getServiceLocator () );
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
						// }
						// throw new \Exception ( $this->translate ( 'The user name or password is incorrect.' ) );
						throw new \Exception ( $e->getMessage () );
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
		return $this->viewModel;
	}
	/**
	 * Iniciando processo de lembrete senha
	 *
	 * @return \Application\Controller\Zend\View\Model\ViewModel
	 */
	public function forgetAction() {
		// Default
		$data = ("Unknown Error, try again, please.");
		$rs = $record = $outcome = $status = false;
		// Request
		$Request = $this->getRequest ();
		$remote_address = $Request->getServer ( 'REMOTE_ADDR' );
		$role = 'USERS';
		// POST
		if ($Request->isPost ()) {
			try {
				$post = $this->postJsonp ();
				$email = isset ( $post ['email'] ) ? $post ['email'] : null;
				// Validacao basica
				if (! ValidadorController::isValidEmail ( $email ) && ! ValidadorController::isValidUsername ( $email )) {
					$data = $this->translate ( "You can't leave this empty." ) . '<br/><br/>';
					$data .= $this->translate ( "Email." ) . ' ' . $this->translate ( "Please use only letters (a-z), numbers and full stops." ) . '<br/>';
					$data .= $this->translate ( "Username." ) . ' ' . $this->translate ( "Please use only letters (a-z), numbers and full stops." ) . '<br/>';
				} else {
					$UserSystemController = new \Quiz\Controller\UserSystemController ( $this->getServiceLocator () );
					if (ValidadorController::isValidEmail ( $email )) {
						$res = $UserSystemController->fetchByEmail ( $email );
						if ($res) {
							if (count ( $res ) == 1) {
								$record = current ( $res );
							} else {
								throw new \Exception ( $this->translate ( 'There is more than one registered user with this email, please enter the user name and click submit again.' ) );
							}
						} else {
							throw new \Exception ( $this->translate ( 'Could not validate your email/username, please make sure filled out correctly or are a registered email/username.' ) );
						}
					} elseif (ValidadorController::isValidUsername ( $email )) {
						$record = $UserSystemController->findByUsername ( $email );
					}
					
					// Contem registro?
					if (! $record) {
						throw new \Exception ( $this->translate ( 'Could not validate your email/username, please make sure filled out correctly or are a registered email/username.' ) );
					} else {
						if ($record ['removed'] == 0 && ValidadorController::isValidEmail ( $record ['email'] )) { // Nao esta bloqueado
							$Forgot = new \Forgot\Controller\ForgotController ( $this->getServiceLocator (), $this->getLocale () );
							$rs = $Forgot->recover ( $role, $record ['id'], $record ['email'], $remote_address );
							if (! $rs) {
								throw new \Exception ( $this->translate ( 'An unknown error occurred, could not send the forget email, please try again.' ) );
							} else {
								// Success
								$this->flashMessenger ()->addSuccessMessage ( $this->translate ( 'An email has been sent automatically to the email address associated with your login. Please check the email folder and span filters for the email. The link will expire in 24 hours.' ) );
								$data = $this->translate ( 'An email has been sent automatically to the email address associated with your login. Please check the email folder and span filters for the email. The link will expire in 24 hours.' );
								$record = $outcome = $status = true;
							}
						} else {
							throw new \Exception ( $this->translate ( 'Your account has not been activated, please fill out the registration again or confirm your registration and email.' ) );
						}
					}
				}
			} catch ( \Exception $e ) {
				$data = $this->translate ( $e->getMessage () );
			}
			// Response
			self::showResponse ( $status, $data, $outcome, true );
			die ();
		}
		return $this->viewModel;
	}
	/**
	 * Resetando a senha
	 *
	 * @return \Application\Controller\Zend\View\Model\ViewModel
	 */
	public function resetAction() {
		// GET
		$Params = $this->params ();
		$hash = $Params->fromQuery ( 'hash' );
		// var_dump(base64_decode ( $hash ) );
		if (! ValidadorController::isValidNotEmpty ( $hash )) {
			$this->viewModel->setVariables ( array (
					'FAILURE' => $this->translate ( 'An attempt has been made to operate on an impersonation token by a thread that is not currently impersonating a client. [001]' ) 
			) );
		} elseif (! ValidadorController::isValidRegexp ( $hash, 'base64' )) {
			$this->viewModel->setVariables ( array (
					'FAILURE' => $this->translate ( 'An attempt has been made to operate on an impersonation token by a thread that is not currently impersonating a client. [002]' ) 
			) );
		} else {
			// Decrypt
			$ForgotController = new \Forgot\Controller\ForgotController ( $this->getServiceLocator () );
			try {
				$decrypt = $ForgotController->decode ( $hash );
				if (! $decrypt) {
					$this->viewModel->setVariables ( array (
							'FAILURE' => $this->translate ( 'An attempt has been made to operate on an impersonation token by a thread that is not currently impersonating a client. [003]' ) 
					) );
				} else {
					$User = $decrypt;
					if (isset ( $User ['user_id'] )) {
						// Desabilitando o Hash
						if ($ForgotController->reset ( $User )) {
							// Redirecionando para a pagina de entrada
							$this->flashMessenger ()->addSuccessMessage ( $this->translate ( 'Your password has been updated and a new password has been sent to your email. Please, check your mail.' ) );
						} else {
							$this->viewModel->setVariables ( array (
									'FAILURE' => $this->translate ( 'The token for this account has expired or blocked due to several attempts to change. [006]' ) 
							) );
						}
					}
				}
			} catch ( Exception $e ) {
				$hash = null;
				$this->viewModel->setVariables ( array (
						'FAILURE' => $this->translate ( 'An attempt has been made to operate on an impersonation token by a thread that is not currently impersonating a client. [008]' ) 
				) );
			}
		}
		return $this->viewModel;
	}
}