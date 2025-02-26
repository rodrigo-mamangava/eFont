<?php

namespace Application\Controller;

use Validador\Controller\ValidadorController;

/**
 * Dynamic Dummy Image Generator
 * 
 * @see http://dummyimage.com/
 * @author Claudio
 */
class DDIGController extends ApplicationController {
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		// Get params
		$Params = $this->params ();
		$text = $Params->fromQuery ( 'text', 'Project Name' );
		$ipsum = $Params->fromQuery ( 'ipsum', false );
		if($ipsum){
			$lipsum = new \joshtronic\LoremIpsum();
			$text = $lipsum->words(5);
		}

		$size = $Params->fromQuery ( 'size', '1200x200' );
		$background = $Params->fromQuery ( 'background', 'fff' );
		$foreground = $Params->fromQuery ( 'foreground', '000' );
		$format = $Params->fromQuery ( 'format', 'png' );
		$font = $Params->fromQuery ( 'font', 'data/tmp/mplus-1c-medium.ttf' );
		if(ValidadorController::isValidRegexp($font, 'base64')){
			$font = \Cryptography\Controller\CryptController::decrypt($font, true);
		}
		$fontsize = $Params->fromQuery ( 'fontsize', null );
		// Set variables
		$this->viewModel->setVariable ( 'text', $text );
		$this->viewModel->setVariable ( 'size', $size );
		$this->viewModel->setVariable ( 'background', $background );
		$this->viewModel->setVariable ( 'foreground', $foreground );
		$this->viewModel->setVariable ( 'fontsize', $fontsize );
		$this->viewModel->setVariable ( 'format', $format );
		$this->viewModel->setVariable ( 'font', $font );
		// Response
		return $this->viewModel->setTerminal ( true );
	}
}