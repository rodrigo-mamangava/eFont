<?php

namespace Application\Controller;

/**
 * Formatos dos arquivos
 * 
 * @author Claudio
 *        
 */
class LicenseFormatsController extends ApplicationController {
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Busca
	 */
	public function searchAction() {
		// Default
		$data = $this->translate ( "Unknown Error, try again, please." );
		$outcome = $status = false;
		// Query
		$LicenseFormatsController = new \Shop\Controller\LicenseFormatsController ( $this->getMyServiceLocator () );
		$Paginator = $LicenseFormatsController->fetchAll();
		if ($Paginator->count () > 0) {
			$data = array ();
			$data ['items'] = iterator_to_array ( $Paginator->getCurrentItems () );
			$data ['total'] = $Paginator->getTotalItemCount ();
			$data ['count'] = $Paginator->getTotalItemCount ();
			$data ['offset'] = 0;
			
			$outcome = $status = true;
		}
		// Response
		self::showResponse ( $status, $data, $outcome, true );
		die ();
	}
}

