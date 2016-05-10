<?php

namespace Application\Controller;

use \Validador\Controller\ValidadorController;
use \Useful\Controller\UsefulController;

/**
 * Controle dos Download
 *
 * @author Claudio
 *        
 */
class DownloadController extends ApplicationController {
	/**
	 * {@inheritDoc}
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		// Get params
		$Params = $this->params ();
		$downloadkey = $Params->fromQuery ( 'downloadkey', null );
		if (ValidadorController::isValidRegexp ( $downloadkey, 'base64' )) {
			$zip = \Cryptography\Controller\CryptController::decrypt ( $downloadkey, true );
			// Set variables
			$this->viewModel->setVariable ( 'download', $zip );
			return $this->viewModel->setTerminal ( true );
		}
		echo $this->trandslate ( 'Invalid Download Key' );
		die ();
	}
}