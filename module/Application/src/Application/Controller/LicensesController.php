<?php

namespace Application\Controller;

/**
 * Controller Licencas
 * @author Claudio
 *
 */
class LicensesController extends ApplicationController
{
	/**
	 * {@inheritDoc}
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Formulario
	 */
	public function formAction(){
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Busca
	 */
	public function searchAction(){
		// Default
		$data = $this->translate ( "Unknown Error, try again, please." );
		$outcome = $status = false;		
		// Response
		self::showResponse ( $status, $data, $outcome, true );
		die ();		
	}
	/**
	 * Edicao
	 */
	public function editAction(){
		// Default
		$data = $this->translate ( "Unknown Error, try again, please." );
		$outcome = $status = false;
		// Response
		self::showResponse ( $status, $data, $outcome, true );
		die ();		
	}
	/**
	 * Remocao
	 */
	public function removedAction(){
		// Default
		$data = $this->translate ( "Unknown Error, try again, please." );
		$outcome = $status = false;
		// Response
		self::showResponse ( $status, $data, $outcome, true );
		die ();		
	}	
}