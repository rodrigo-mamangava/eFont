<?php

namespace Application\Controller;

use \Validador\Controller\ValidadorController;
/**
 * Controller Profile
 * @author Claudio
 */
class ProfileController extends ApplicationController
{
	/**
	 * (non-PHPdoc)
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		return $this->viewModel->setTerminal(true);
	}
	/**
	 * Salva/Atualiza
	 */
	public function saveAction() {
		// Default
		$data = $this->translate ( "Unknown Error, try again, please." );
		$outcome = $status = false;
		if ($this->getRequest ()->isPost ()) {
			try {
				// PARAMS
				$post = $this->postJsonp ();
				$firstname = isset ( $post ['firstname'] ) ? $post ['firstname'] : null;
				$lastname = isset ( $post ['lastname'] ) ? $post ['lastname'] : null;
				$fullname = isset ( $post ['fullname'] ) ? $post ['fullname'] : $firstname . ' ' . $lastname;
				$phone = isset ( $post ['tel'] ) ? $post ['tel'] : null;
				
				$email = isset ( $post ['email'] ) ? strtolower ( ValidadorController::removeBlank ( $post ['email'] ) ) : null;
				$username = $email;
				$password = isset ( $post ['password'] ) ? $post ['password'] : null;
				$rpassword = isset ( $post ['rpassword'] ) ? $post ['rpassword'] : null;
				
				$address = isset ( $post ['address'] ) ? $post ['address'] : '';
				$address_complement = isset ( $post ['address_complement'] ) ? $post ['address_complement'] : '';
				$address_state = '';
				$address_city = isset ( $post ['address_city'] ) ? $post ['address_city'] : '';
				$address_country = isset ( $post ['address_country'] ) ? $post ['address_country'] : '';
				$address_postcode = isset ( $post ['address_postcode'] ) ? $post ['address_postcode'] : '';
				$image = isset ( $post ['image'] ) ? $post ['image'] : null;
				// Default, para uso futuro
				$privilege_type_id = 3;
				$provider= 'register';
				$id = $this->get_user_id();
				$company_id = $this->get_company_id();
				// UserSystem id
				if ($this->get_privilege_type () == 2) {
					$company_id = isset ( $post ['company_id'] ) ? $post ['company_id'] : $this->get_company_id ();
				}
				// Validate
				if (! ValidadorController::isValidDigits ( $id )) {
					$data = $this->translate ( "Users Id." ) . ' ' . $this->translate ( "You can't leave this empty." );
				} elseif (! ValidadorController::isValidDigits ( $company_id )) {
					$data = $this->translate ( "Company." ) . ' ' . $this->translate ( "You can't leave this empty." );
				} elseif (! ValidadorController::isValidStringLength ( $fullname, 1, 100 )) {
					$data = $this->translate ( "Name." ) . ' ' . $this->translate ( "You can't leave this empty or exceeded the number of characters." );
				} elseif( ! ValidadorController::isValidEmail ( $email )) {
					$data = $this->translate ( "Email." ) . ' ' . $this->translate ( "Please use only letters (a-z), numbers and full stops." );
					// Password
				} elseif ($id == 0 && ! ValidadorController::isValidSenha ( $password )) {
					$data = $this->translate ( 'Password: Short passwords are easy to guess. Try one with at least 8 characters. <br/> Use at least 8 characters. Don\'t use a password from another site or something too obvious like your pet\'s name.' );
				} elseif (ValidadorController::isValidNotEmpty ( $password ) && ! ValidadorController::isValidSenha ( $password )) {
					$data = $this->translate ( 'Password: Short passwords are easy to guess. Try one with at least 8 characters. <br/> Use at least 8 characters. Don\'t use a password from another site or something too obvious like your pet\'s name.' );
				} elseif (ValidadorController::isValidNotEmpty ( $password ) && ! ValidadorController::ifSafeStringComparison ( $password, $rpassword )) {
					$data = $this->translate ( 'Confirm your password: These passwords don\'t match. Try again?.' );
				} else {
					// Mapper
					$UserSystemController = new \Shop\Controller\UserSystemController ( $this->getMyServiceLocator () );
					$rs = $UserSystemController->findByEmail( $username );
					if ($rs !== false && is_object( $rs )) {
						$rs = $rs->id;
					}
						
					if ($rs != false && $id != $rs) {
						$data = $this->translate ( 'Someone already has that username. Try another?' );
					} else {
						$rs = $UserSystemController->save($id, $username, $password, $email, $phone, $privilege_type_id, $company_id, 1, $firstname, $lastname, $address, $address_city, $address_complement, $address_country, $address_postcode);
						if ($rs) {
							$status = true;
							$outcome = $rs;
						}
					}
				}
			} catch ( \Exception $e ) {
				$data = $e->getMessage ();
			}
		}
		// Response
		self::showResponse ( $status, $data, $outcome, true );
		die ();
	}	
	/**
	 * Edit
	 */
	public function editAction(){
		$data = $this->translate ( "Unknown Error, try again, please." );
		$outcome = $status = false;
		// UserSystem id
		$company_id = $this->get_company_id ();
		$id = $this->get_user_id();
		// GET
		if (! ValidadorController::isValidDigits ( $id )) {
			$data = $this->translate ( 'Invalid Id' );
		} else {
			// Remove
			$UserSystem = new \Shop\Controller\UserSystemController ( $this->getServiceLocator () );
			$rs = $UserSystem->find ( $id, $company_id );
			// Exists?
			if ($rs) {
				$data =  \Useful\Controller\UsefulController::getStripslashes($rs);
				$outcome = $status = true;
			} else {
				$data = $this->translate ( 'Invalid Id' );
			}
		}
		// Response
		self::showResponse ( $status, $data, $outcome, true );
		die ();		
	}
}