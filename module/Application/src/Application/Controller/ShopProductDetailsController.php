<?php

namespace Application\Controller;

/**
 * Detalhes do produto
 * @author Claudio
 *
 */
class ShopProductDetailsController extends ApplicationController
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