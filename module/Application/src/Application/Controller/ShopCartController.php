<?php

namespace Application\Controller;

/**
 * Carrinho de compras
 * @author Claudio
 */
class ShopCartController extends ApplicationController
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