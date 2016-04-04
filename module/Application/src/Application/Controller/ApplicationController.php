<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ApplicationController extends AbstractActionController {
	
	/**
	 * Classe para mensagens nas views
	 *
	 * @var Zend\View\Model\ViewModel
	 */
	protected $viewModel;
	/**
	 * Classe responsavel pelas sessoes
	 *
	 * @var Zend\Authentication\AuthenticationService;
	 */
	protected $authService;
	/**
	 * Usuario logado, caso tenha sessao
	 *
	 * @var unknown
	 */
	protected $userSession = null;
	/**
	 * (non-PHPdoc)
	 *
	 * @see \Zend\Mvc\Controller\AbstractActionController::onDispatch()
	 */
	public function onDispatch(\Zend\Mvc\MvcEvent $e) {
		/**
		 * Verifica se o usuario se encontra logado, caso contrario redireciona ele para o login
		 *
		 * @return bool
		 */
		// $this->authService = new AuthenticationService ();
		$this->authService = \Accounts\Controller\AuthenticatorController::getInstance ();
		$controller = $this->getEvent ()->getRouteMatch ()->getParam ( 'controller' );
		if (! $this->authService->hasIdentity ()) {
			if ($controller == 'Application\Controller\Index' || $controller == 'Application\Controller\Welcome') { // Liberado sem sessao
				return parent::onDispatch ( $e );
			} else {
				return $this->redirect ()->toRoute ( "login" );
			}
		} else {
			self::userLogged ();
			if ($controller == 'Application\Controller\Company') { // Root + Reseller
				if (self::get_privilege_type () != 2) {
					return $this->redirect ()->toRoute ( "logout" ); // Saindo
				}
			}elseif ($controller == 'Application\Controller\UserSystem'){
				if (self::get_privilege_type () != 2 && self::get_privilege_type () != 1) {
					return $this->redirect ()->toRoute ( "logout" ); // Saindo
				}
			}
		}
		return parent::onDispatch ( $e );
	}
	/**
	 * Controller das informacoes dos usuarios logados
	 *
	 * @return Boolean
	 */
	private function userLogged() {
		if ($this->authService->hasIdentity ()) {
			$providers = $this->authService->getIdentity ();
			$user = null;
			foreach ( $providers as $provider ) {
				$user = $provider;
				break;
			}
			$root = isset ( $user->root ) ? $user->root : false;
			// Controller, Actions e Views
			$this->viewModel->setVariable ( 'USER_EMAIL', $user->getEmail () );
			$this->viewModel->setVariable ( 'USER_ID', $user->getId () );
			$this->viewModel->setVariable ( 'USER_PROVIDER', $user->getUsername () );
			
			$this->viewModel->setVariable ( 'USER_ROOT', $root );
			$this->viewModel->setVariable ( 'USER_LOGGED', true );
			$this->viewModel->setVariable ( 'USER_COMPANY_ID', $user->getCompany_id () );
			$this->viewModel->setVariable ( 'USER_PRIVILEGE_TYPE', $user->getPrivilege_type () );
			
			$this->viewModel->setVariable ( 'USER_WHO', array (
					'USER_EMAIL' => $user->getEmail (),
					'USER_ID' => $user->getId (),
					'USER_PROVIDER' => $user->getName (),
					'USER_COMPANY_ID' => $user->getCompany_id (),
					'USER_PRIVILEGE_TYPE' => $user->getPrivilege_type () 
			) );
			
			$this->viewModel->setVariable ( 'USER_TWO_FACTOR', $user->getTwo_factor () );
			$this->viewModel->setVariable ( 'USER_TWO_FACTOR_SECRET', $user->getTwo_factor_secret () );
			
			$this->viewModel->setVariable ( 'USER_IMAGE', $user->getImage () );
			// Layouts externos
			$this->layout ()->setVariable ( 'USER_EMAIL', $user->getEmail () );
			$this->layout ()->setVariable ( 'USER_ID', $user->getId () );
			$this->layout ()->setVariable ( 'USER_PROVIDER', $user->getUsername() );
			$this->layout ()->setVariable ( 'USER_ROOT', $root );
			$this->layout ()->setVariable ( 'USER_LOGGED', true );
			$this->layout ()->setVariable ( 'USER_COMPANY_ID', $user->getCompany_id () );
			$this->layout ()->setVariable ( 'USER_PRIVILEGE_TYPE', $user->getPrivilege_type () );
			
			$this->layout ()->setVariable ( 'USER_TWO_FACTOR', $user->getTwo_factor () );
			$this->layout ()->setVariable ( 'USER_TWO_FACTOR_SECRET', $user->getTwo_factor_secret () );
			
			if(!self::getDefaultSessionImage()){
				$this->layout ()->setVariable ( 'USER_IMAGE', $user->getImage () );
			}
			// Salva usuario
			$this->userSession = $user;
			// Retornando resposta para quem chamou
			return $root;
		}
		return false;
	}
	/**
	 * Forca a atualizacao da imagem de avatar na sessao
	 * @param unknown $image
	 */
	public function setDefaultSessionImage($image){
		// Zend\Session\Container
		$Session = new \Zend\Session\Container ( 'USER_IMAGE' );
		$Session->offsetSet('image', $image);
	}
	/**
	 * Verifca se contem imagem salva na sessao, se sim retorna
	 */
	public function getDefaultSessionImage(){
		// Zend\Session\Container
		$Session = new \Zend\Session\Container ( 'USER_IMAGE' );
		if($Session->offsetExists('image')){
			$image = $Session->offsetGet('image');
			$this->layout ()->setVariable ( 'USER_IMAGE', $image );
			return 	$image;
		}
		
		return false;
	}

	/**
	 * Retorna a sessao do two Step
	 */
	public function getTwoStepVerification() {
		// Zend\Session\Container
		$Session = new \Zend\Session\Container ( 'USER_2_STEP_VERIFICATION' );
		// Check that key exists in session
		$rs = $Session->offsetExists ( 'ack' );
		if ($rs) {
			$ack = $Session->offsetGet ( 'ack' );
			return $ack;
		}
		return false;
	}
	/**
	 * Retorna a resposta do processo de validacao
	 *
	 * @param string $ack        	
	 */
	public function setTwoStepVerification($ack = false) {
		// Zend\Session\Container
		$Session = new \Zend\Session\Container ( 'USER_2_STEP_VERIFICATION' );
		$rs = $Session->offsetSet ( 'ack', $ack );
		return $rs;
	}
	/**
	 * Limpando cache/sessao
	 */
	public function unsetTwoStepVerification() {
		$Session = new \Zend\Session\Container ( 'USER_2_STEP_VERIFICATION' );
		return $Session->offsetUnset ( 'ack' );
	}
	/**
	 * Retorna o usuario que contem a sessao
	 */
	public function getUserSession() {
		return $this->userSession;
	}
	/**
	 * Consulta do company_id
	 */
	public function get_company_id() {
		$id = self::getUserSession ()->getCompany_id ();
		$user_id = self::getUserSession ()->getId ();
		if (is_null ( $id ) || $id == 0) {
			$Company = new \Quiz\Controller\CompanyController ( $this->getMyServiceLocator () );
			$id = $Company->findCompanyIdByUserId ( $user_id, true );
		}
		return $id;
	}
	/**
	 * Funcao abreviada para obter previlegios
	 */
	public function get_privilege_type() {
		return $this->viewModel->getVariable ( 'USER_PRIVILEGE_TYPE' );
	}
	/**
	 * Retorna o ID/Nome do usuario logado
	 *
	 * @param Boolean $is_id,
	 *        	True retorna o id, caso contrario o nome
	 * @throws Exception
	 * @return Boolean
	 */
	protected function get_user_id() {
		if ($this->authService->hasIdentity ()) {
			$providers = $this->authService->getIdentity ();
			$user = null;
			foreach ( $providers as $provider ) {
				$user = $provider;
				break;
			}
			
			$user_id = $user->getId ();
			if ($user_id) {
				return $user_id;
			} else {
				return $this->redirect ()->toRoute ( "logout" );
				exit ( 0 );
			}
		}
		// throw new Exception('Nao foi possivel verificar sua sessao');
		return $this->redirect ()->toRoute ( "login" );
	}
	/**
	 * Chegando permisso de acesso
	 *
	 * @param unknown $id        	
	 * @throws \Exception
	 * @return boolean
	 */
	function checkPermission($id) {
		// Property By Permission
		$permission = false;
		$Permission = new \Quiz\Controller\CompanyController ( $this->getServiceLocator () );
		
		if ($id == 0) {
			return true;
		} elseif ($this->get_privilege_type () == 2) {
			$permission = true;
		} elseif ($this->get_privilege_type () == 1 && $this->get_company_id () == $id) {
			$permission = true;
		} elseif ($this->get_privilege_type () == 3) {
			$permission = $Permission->amIReseller ( $this->get_company_id (), $id );
		}
		
		if (! $permission) {
			throw new \Exception ( $this->translate ( 'Oops! No permissions.' ) );
		}
		return true;
	}
	/**
	 * Verifica o limite do plano de uma empresa
	 */
	function checkLimitPlans() {
		// Set numbers by type
		$ready_to_use = 0;
		if ($this->get_privilege_type () != 1) {
			$ready_to_use = 99;
		}
		// Control
		$available_staff = $available_web = $plan_tech_users = $plan_web_users = $use_web = $use_staff = $ready_to_use;
		
		if ($this->get_privilege_type () != 2) {//Root nem perde tempo conferido
			$Company = new \Quiz\Controller\CompanyController ( $this->getMyServiceLocator () );
			$Paginator = $Company->filter ( null, null, null, $this->get_company_id () );
			if ($Paginator->count () > 0) {
				foreach ( $Paginator as $rs ) {
					if ($rs->id == $this->get_company_id ()) {
						$plan_web_users = isset ( $rs->plan_web_users ) ? $rs->plan_web_users : 0;
						$plan_tech_users = isset ( $rs->plan_tech_users ) ? $rs->plan_tech_users : 0;
						$use_web = isset ( $rs->use_web ) ? $rs->use_web : 0;
						$use_staff = isset ( $rs->use_tech ) ? $rs->use_tech : 0;
						
						$available_web = ($plan_web_users > 0 && $use_web > 0) ? ($plan_web_users - $use_web) : $plan_web_users;
						$available_staff = ($plan_tech_users > 0 && $use_staff > 0) ? ($plan_tech_users - $use_staff) : $plan_tech_users;
						break;
					}
				}
			}
		}
		
		$this->viewModel->setVariable ( 'USE_WEB', $use_web );
		$this->viewModel->setVariable ( 'USE_STAFF', $use_staff );
		$this->viewModel->setVariable ( 'PLAN_WEB', $plan_web_users );
		$this->viewModel->setVariable ( 'PLAN_STAFF', $plan_tech_users );
		$this->viewModel->setVariable ( 'AVAILABLE_WEB', $available_web );
		$this->viewModel->setVariable ( 'AVAILABLE_STAFF', $available_staff );
		
		$this->layout ()->setVariable ( 'USE_WEB', $use_web );
		$this->layout ()->setVariable ( 'USE_STAFF', $use_staff );
		$this->layout ()->setVariable ( 'PLAN_WEB', $plan_web_users );
		$this->layout ()->setVariable ( 'PLAN_STAFF', $plan_tech_users );
		$this->layout ()->setVariable ( 'AVAILABLE_WEB', $available_web );
		$this->layout ()->setVariable ( 'AVAILABLE_STAFF', $available_staff );
		
		//var_dump ( $available_staff, $available_web ); 
		
		return;
	}
	/**
	 * Retorna o idioma salvo na sessao do login
	 *
	 * @return string
	 */
	protected function getLocale() {
		if ($this->authService->hasIdentity ()) {
			$user = $this->authService->getIdentity ();
			if (isset ( $user->locale )) {
				return $user->locale;
			} else {
				$Session = new \Zend\Session\Container ( 'language' );
				if ($Session && $Session->locale) {
					return $Session->locale;
				} else {
					return 'en_US';
				}
			}
		} else {
			$Session = new \Zend\Session\Container ( 'language' );
			if ($Session && $Session->locale) {
				return $Session->locale;
			}
		}
		// throw new Exception('Nao foi possivel verificar sua sessao');
		// return $this->redirect ()->toRoute ( "login" );
	}
	/**
	 * Construct
	 */
	public function __construct() {
		// View
		$this->viewModel = new ViewModel ();
	}
	
	/**
	 * Atalho para chamada da funcao de autenticacao
	 *
	 * @param String $text        	
	 */
	public function translate($text) {
		return self::getTranslator ()->translate ( $text );
	}
	public function getTranslator() {
		return $this->getServiceLocator ()->get ( 'translator' );
	}
	public function getMyServiceLocator() {
		return $this->getServiceLocator ();
	}
	public function getDbTable($DbTable) {
		return $this->getMyServiceLocator ()->get ( $DbTable );
	}
	public function getSystemConfig() {
		return $this->getServiceLocator ()->get ( 'config' );
	}
	/**
	 * Settar uma variavel para o layout
	 *
	 * @param unknown $name        	
	 * @param unknown $value        	
	 */
	public function setLayoutVariable($name, $value) {
		$this->layout ()->setVariable ( $name, $value );
	}
	/**
	 * Resposta em uma formato especifico
	 *
	 * @param boolean $status,
	 *        	TRUE processado com sucesso, FALSE ocorreu alguma falha
	 * @param String|Array $data,
	 *        	mensagem de resposta ou dado relevante de acordo com o formato previsto
	 * @param String|Array|Int $outcome,
	 *        	dado resultado do processamento
	 * @return \Zend\View\Model\JsonModel
	 */
	public function showResponse($status, $data, $outcome = false, $header = true) {
		if ($header) {
			header ( 'Content-Type: application/json' );
		}
		echo \Zend\Json\Json::encode ( array (
				'status' => $status,
				'data' => $data,
				'outcome' => $outcome 
		) );
		die ();
	}
	
	/**
	 * Obter o objeto de um json post
	 */
	public function postJsonp(){
		return json_decode(file_get_contents('php://input'), true);
	}	
	
	/**
	 * Carregando funcoes comuns do controller
	 */
	protected function initServiceOrder() {
		// Customers
		$CustomersController = new \Quiz\Controller\CustomersController ( $this->getMyServiceLocator () );
		$Paginator = $CustomersController->filter ( null, null, null, $this->get_company_id () );
		if ($Paginator->count () > 0) {
			$this->viewModel->setVariable ( 'CUSTOMERS', $Paginator );
		}
		// Reasons
		$Reasons = new \Quiz\Controller\ReasonsController ( $this->getMyServiceLocator () );
		$Paginator = $Reasons->filter ( null, null, null, $this->get_company_id () );
		if ($Paginator->count () > 0) {
			$this->viewModel->setVariable ( 'REASONS', $Paginator );
		}
	}
	
}