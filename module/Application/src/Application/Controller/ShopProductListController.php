<?php

namespace Application\Controller;

/**
 * Lista de produtos
 * @author Claudio
 */
class ShopProductListController extends ApplicationController
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