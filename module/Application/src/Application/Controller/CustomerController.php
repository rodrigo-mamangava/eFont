<?php

namespace Application\Controller;

/**
 * Controle do usuarios
 * @author Claudio
 */
class CustomerController extends ApplicationController
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
	 * Profile
	 */
	public function profileAction(){
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Order History
	 */
	public function historyAction(){
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Account
	 */
	public function accountAction(){
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Bem vindo
	 */
	public function welcomeAction(){
		//$this->setLayoutVariable('CHANGETEMPLATEURL', '/shop-customer/index');
		$this->setLayoutVariable('CHANGETEMPLATEURL', '/ef-products/form');
		return $this->viewModel;
	}
}