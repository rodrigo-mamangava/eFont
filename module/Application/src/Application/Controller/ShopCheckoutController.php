<?php

namespace Application\Controller;

/**
 * Finalizando a compra
 * @author Claudio
 */

class ShopCheckoutController extends ApplicationController
{
	/**
	 * {@inheritDoc}
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction()
	{
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Completando operacao
	 */
	public function completeAction(){
		return $this->viewModel->setTerminal ( true );
	}
}