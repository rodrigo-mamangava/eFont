<?php

namespace Application\Controller;

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
		$size = $Params->fromQuery ( 'size', '1200x200' );
		$background = $Params->fromQuery ( 'background', 'fff' );
		$foreground = $Params->fromQuery ( 'foreground', '000' );
		$format = $Params->fromQuery ( 'format', 'png' );
		$font = $Params->fromQuery ( 'font', 'data/tmp/mplus-1c-medium.ttf' );
		// Set variables
		$this->viewModel->setVariable ( 'text', $text );
		$this->viewModel->setVariable ( 'size', $size );
		$this->viewModel->setVariable ( 'background', $background );
		$this->viewModel->setVariable ( 'foreground', $foreground );
		$this->viewModel->setVariable ( 'format', $format );
		$this->viewModel->setVariable ( 'font', $font );
		// Response
		return $this->viewModel->setTerminal ( true );
	}
}