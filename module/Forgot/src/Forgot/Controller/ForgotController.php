<?php

namespace Forgot\Controller;

use Cryptography\Controller\CryptController;
use Useful\Controller\ControlController;
use Validador\Controller\ValidadorController;
use Zend\Json\Json;
use Zend\Math\Rand;

/**
 * Controller dos lembretes de senha
 *
 * @author Claudio
 */
class ForgotController extends ControlController {
	
	/**
	 * Inicia o processo de geracao do hash para alteracao da senha
	 *
	 * @param unknown $user_id        	
	 * @param unknown $username        	
	 * @param unknown $remote        	
	 * @throws \Exception
	 */
	public function recover($role, $user_id, $username, $remote, $company_id = 0) {
		// Mapper
		$ForgotTable = $this->getDbTable ( '\Forgot\Model\ForgotTable' );
		$Forgot = $ForgotTable->findByUserId ( $user_id, $role );
		$hash = null;
		// Mailer
		$Mailer = new \Email\Controller\MailerController ( $this->getServiceLocator () );
		// Verificao se ja contem hash gerado
		if (is_a ( $Forgot, '\Forgot\Model\Entity\Forgot' )) {
			if ($Forgot->getAttempts () < 3) {
				$ForgotTable->plusAttempts ( $Forgot->getId () );
				// Send mail
				return $Mailer->sendMailResetPassword ( $username, $Forgot->getHash () );
			} else {
				throw new \Exception ( $this->getTranslate ()->translate ( 'You have exceeded the number of requests allowed for password reset.' ) );
			}
		} else {
			$Bounce = new \Email\Controller\BounceController ( $this->getServiceLocator () );
			if ($Bounce->prevent ( $username, false, false )) {
				// Gerando chave e hash para futura validacao
				$key = Rand::getString ( 16 );
				$hash = CryptController::grantCryptToken ( $user_id, uniqid (), $company_id, $role, $remote, $this->getTranslate ()->getLocale () );
				// Montando array para salvamento
				$data ['key'] = $key;
				$data ['user_id'] = $user_id;
				// New Object
				$newForgot = new \Forgot\Model\Entity\Forgot ();
				$newForgot->setUserId ( $user_id );
				$newForgot->setStatus ( 0 );
				$newForgot->setKey ( $key );
				$newForgot->setRole ( $role );
				$newForgot->setRemote ( $remote );
				$newForgot->setHash ( base64_encode ( $hash ) );
				// Save
				$ForgotTable->save ( $newForgot );
				// Send mail
				sleep ( 1 );
				return $Mailer->sendMailResetPassword ( $username, $newForgot->getHash () );
			}
		}
		return false;
	}
	
	/**
	 * resetando a senha
	 *
	 * @param unknown $User        	
	 * @return boolean
	 */
	public function reset($User) {
		// Desabilitando o Hash
		if (self::disabledHash ( $User ['user_id'], $User ['role'] )) {
			// Nova senha
			$new_password = ValidadorController::createPassword ();
			if ($User ['role'] == 'USERS') {
				$UserSystemController = new \Quiz\Controller\UserSystemController ( $this->getServiceLocator () );
				$alter = $UserSystemController->updatePassword ( $User ['user_id'], $new_password );
				if ($alter) {
					$rs = $UserSystemController->find ( $User ['user_id'] );
					// Mailer
					$Mailer = new \Email\Controller\MailerController ( $this->getServiceLocator () );
					$Mailer->sendMailNewPassword ( $rs ['email'], $new_password );
					// Redirecionando para a pagina de entrada
					return true;
				}
			} elseif ($User ['role'] == 'CUSTOMER') {
				$data = array();
				$data ['uuid'] = new \Zend\Db\Sql\Expression ( 'UUID()' );
				$data ['password'] = md5 ( $new_password );
				
				$UserController = new \Quiz\Controller\UserController ( $this->getServiceLocator () );
				$alter = $UserController->updated ( $User ['user_id'], $User ['company_id'], $data);
				if ($alter) {
					$rs = $UserController->find ( $User ['user_id'], $User ['company_id'] );
					// Mailer
					$Mailer = new \Email\Controller\MailerController ( $this->getServiceLocator () );
					$Mailer->sendMailNewPassword ( $rs ['email'], $new_password );
					// Redirecionando para a pagina de entrada
					return true;
				}
			}
		}
		return true;
	}
	
	/**
	 * Procede com a desabilitacao de todos os Hash para um usuario
	 */
	public function disabledHash($user_id, $role) {
		$ForgotTable = $this->getDbTable ( '\Forgot\Model\ForgotTable' );
		return $ForgotTable->disabledHash ( $user_id, $role );
	}
	
	/**
	 * Processa o recebimento do hash para atualizacao da senha
	 *
	 * @param unknown $hash        	
	 * @throws \Exception
	 * @return Ambigous <\Cryptography\Controller\Ambigous, string, boolean>
	 */
	public function receivedProcessForgot($hash) {
		if (! is_null ( $hash ) && strlen ( $hash ) > 15) {
			try {
				return CryptController::decrypt ( $hash );
			} catch ( \Exception $e ) {
				throw new \Exception ( 'Reset code was not accepted. [4]' );
			}
		}
		throw new \Exception ( 'Reset code was not accepted. [0]' );
	}
	
	/**
	 * Retorna o codigo de nova senha decriptografado
	 *
	 * @param unknown $hash        	
	 * @return Ambigous <string, mixed>
	 */
	public function decode($hash) {
		return \Zend\Json\Json::decode ( CryptController::decrypt ( $hash, true ), \Zend\Json\Json::TYPE_ARRAY );
	}
}