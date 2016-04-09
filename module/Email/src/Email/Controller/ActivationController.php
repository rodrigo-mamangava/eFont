<?php

namespace Email\Controller;

use Useful\Controller\ControlController;
use Validador\Controller\ValidadorController;
use Cryptography\Controller\CryptController;

/**
 * Classe responsavel pelo registro de novos usuarios
 *
 * @author Claudio
 */
class ActivationController extends ControlController {
	protected $SHA1 = '';
	/**
	 * Verifica se contem registro de email ou username
	 * @param unknown $username
	 * @param unknown $email
	 * @param string $try, se verifica todos ou somente o username
	 */
	public function Available($username, $email, $try = true) {
		$User = new \Quiz\Controller\UserController ( $this->getServiceLocator () );
		$rs = $User->findByUsername($username);
		if($rs == false && $try){//Email de uma empresa?
			$Company = new \Quiz\Controller\CompanyController ( $this->getServiceLocator () );
			$rs = $Company->findByEmail($email);
			if($rs == false){//Email de usuario?
				return $User->findByEmail($email);
			}
		}
		return $rs;
	}
	/**
	 * Checa se existe um email ou username e retorna o tipo
	 * @param unknown $username
	 * @param unknown $email
	 */
	public function getAvailableAndType($username, $email){
		$User = new \Quiz\Controller\UserController ( $this->getServiceLocator () );
		$rs = $User->findByUsername($username);
		if($rs){
			return 'username';
		}else{
			$Company = new \Quiz\Controller\CompanyController ( $this->getServiceLocator () );
			$rs = $Company->findByEmail($email);
			if($rs){
				return 'email';
			}else{
				$Company = new \Quiz\Controller\CompanyController ( $this->getServiceLocator () );
				$rs = $Company->findByEmail($email);
				if($rs){
					return 'email';
				}
			}
		}
		
		return $rs;
	}
	/**
	 * Busca por email
	 * 
	 * @param unknown $email        	
	 */
	public function findByEmail($email) {
		// Mapper
		$ActivationTable = $this->getDbTable ( '\Email\Model\ActivationTable' );
		return $ActivationTable->findByEmail ( $email );
	}
	/**
	 * Busca pelo Id e Email
	 *
	 * @param unknown $id        	
	 * @param unknown $email        	
	 */
	public function findByIdAndEmail($id, $email) {
		// Mapper
		$ActivationTable = $this->getDbTable ( '\Email\Model\ActivationTable' );
		return $ActivationTable->findByEmail ( $email, $id );
	}
	
	/**
	 * Cria o registro e retorna o codigo
	 *
	 * @param unknown $email        	
	 * @return multitype:unknown \Cryptography\Controller\unknown |boolean
	 */
	public function create($email, $password, $name, $address, $city, $username, $country, $privilege_type = 0, $encrypt = true) {
		$rs = self::register ( $email );
		if ($rs) {
			// Add user
			$User = new \Quiz\Controller\UserController ( $this->getServiceLocator () );
			$user_id = $User->save ( null, 1, 0, 0, 0, $name, $username, $password, $email, $rs, 1 );
			// Add Company
			if ($user_id) {
				$Company = new \Quiz\Controller\CompanyController ( $this->getServiceLocator () );
				$company_id = $Company->save ( null, null, 5, 1, uniqid (), null, $name, null, 'F', $email, '', $address, '', $city, '', $country );
				// Update User
				$User->updateProfile ( $user_id, array (
						'id_sys_customer' => $company_id 
				) );
				if ($company_id && $user_id) {
					// Active code
					if($encrypt ==  true){
						return self::secure ( $rs, $email, $this->SHA1 );
					}else{
						return array('id'=>$rs, 'email'=>$email, 'secure'=>$this->SHA1,'company_id'=>$company_id );
					}
				}
			}
		}
		
		return false;
	}
	/**
	 * Registro do codigo de verificacao
	 * 
	 * @param unknown $email        	
	 */
	public function register($email) {
		// PIN + Secure
		$PIN = ValidadorController::createPin ();
		$FIR = ValidadorController::createPin ( 2 );
		$SEC = ValidadorController::createPin ( 8 );
		$this->SHA1 = CryptController::createSecureCode ( $PIN, $FIR, $SEC );
		// Mapper
		$ActivationTable = $this->getDbTable ( '\Email\Model\ActivationTable' );
		return $ActivationTable->create ( $email, $SEC, $FIR, $PIN, $this->SHA1 );
	}
	/**
	 * Verifica o codigo e executa a ativacao
	 *
	 * @param unknown $id        	
	 * @param unknown $email        	
	 * @param unknown $SHA1        	
	 * @return boolean
	 */
	public function verifyAndEnable($id, $email, $SHA1) {
		$rs = self::findByIdAndEmail ( $id, $email );
		if ($rs) {
			if (self::disabledHash ( $id )) {
				// Desativando pelo id de confirmacao e email
				$User = new \Quiz\Controller\UserController ( $this->getServiceLocator () );
				return $User->enabling ( $id, $email );
			}
		}
		return false;
	}
	
	/**
	 * Desabilitando codigo de ativacao
	 *
	 * @param unknown $id        	
	 */
	private function disabledHash($id) {
		// Mapper
		$ActivationTable = $this->getDbTable ( '\Email\Model\ActivationTable' );
		return $ActivationTable->disabling ( $id );
	}
	
	/**
	 * Retorna o token cryptografado
	 *
	 * @param unknown $id        	
	 * @param unknown $email        	
	 * @param unknown $SHA1        	
	 * @return string
	 */
	public function secure($id, $email, $SHA1) {
		return CryptController::encrypt ( \Zend\Json\Json::encode ( array (
				'id' => $id,
				'email' => $email,
				'secure' => $SHA1 
		) ), true );
	}
	
	/**
	 * Retorna o codigo de ativacao decriptografado
	 *
	 * @param unknown $hash        	
	 * @return Ambigous <string, mixed>
	 */
	public function decode($hash) {
		return \Zend\Json\Json::decode ( CryptController::decrypt ( $hash, true ), \Zend\Json\Json::TYPE_ARRAY );
	}
}