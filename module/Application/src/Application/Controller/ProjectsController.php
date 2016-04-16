<?php

namespace Application\Controller;

/**
 * Controller Projectos
 * 
 * @author Claudio
 */
class ProjectsController extends ApplicationController {
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Projetos/Produtos
	 */
	public function productsAction() {
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Licencas
	 */
	public function licensesAction() {
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Informacoes
	 */
	public function infosAction() {
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Promocoes
	 */
	public function promotionsAction() {
		return $this->viewModel->setTerminal ( true );
	}
}