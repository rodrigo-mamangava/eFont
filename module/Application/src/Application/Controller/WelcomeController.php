<?php

namespace Application\Controller;

/**
 * Pagina inicial
 * @author Claudio
 */
class WelcomeController extends ApplicationController
{
	/**
	 * {@inheritDoc}
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
    public function indexAction()
    {
        return $this->viewModel->setTerminal ( true );
    }    
}