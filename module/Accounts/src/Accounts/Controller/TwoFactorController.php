<?php

namespace Accounts\Controller;

use \Validador\Controller\ValidadorController;

/**
 * Controller dos codigos do TwoStep
 * 
 * @author Claudio
 *        
 */
class TwoFactorController extends \Application\Controller\ApplicationController {
	public function indexAction() {
		return $this->viewModel;
	}
	
	/**
	 * Salva/Atualiza
	 *
	 * @return \Application\Controller\Zend\View\Model\ViewModel
	 */
	public function saveAction() {
		// action body
		$Request = $this->getRequest ();
		// POST
		if ($Request->isPost ()) {
			// PARAMS
			$post = $Request->getPost ();
			// Values
			$two_step_pin = isset ( $post ['two_step_pin'] ) ? $post ['two_step_pin'] : 0;
			// System
			$user_id = $this->getUserSession ()->getId ();
			$company_id = $this->get_company_id ();
			// Validate
			if (! ValidadorController::isValidDigits ( $company_id )) {
				$this->viewModel->setVariables ( array (
						'FAILURE' => $this->translate ( 'Company Id. You can\'t leave this empty.' )
				) );
			} elseif (! ValidadorController::isValidDigits ( $two_step_pin )) {
				$this->viewModel->setVariables ( array (
						'FAILURE' => $this->translate ( 'Wrong code. Try again.' )
				) );
			} else {
				if ($two_step_pin) {
					// Controller
					$GoogleAuthenticator = new \Accounts\Controller\GoogleAuthenticatorController ();
					// verify code and output result
					$checkResult = $GoogleAuthenticator->verifyCode ( $this->viewModel->getVariable ( 'USER_TWO_FACTOR_SECRET'), $two_step_pin, 2 ); // 2 = 2*30sec clock tolerance
					if ($checkResult) {
						$this->setTwoStepVerification(true);
						return $this->redirect ()->toRoute ( 'abc-timeline' );
					} else {
						$this->viewModel->setVariables ( array (
								'FAILURE' => $this->translate ( 'Wrong code. Try again.' )
						) );
					}
				}
			}
		}
		// Return
		return $this->viewModel;
	}
	/**
	 * Limpar a sessao e vai para o formulario
	 */
	public function problemsAction(){
		//Clear session
		$auth = \Accounts\Controller\AuthenticatorController::getInstance ();
		$auth->clearIdentity ();
		//Two Step
		$this->unsetTwoStepVerification();
		//Render
		return $this->redirect ()->toRoute ( 'contacts' );
	}
}