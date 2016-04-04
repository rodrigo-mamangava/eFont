<?php

namespace Application\Controller;

/**
 * Produtos do usuario
 * @author Claudio
 */
class ShopProductUsersController extends ApplicationController
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