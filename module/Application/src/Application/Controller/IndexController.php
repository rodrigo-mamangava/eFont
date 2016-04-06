<?php

namespace Application\Controller;

/**
 * Controle inicial
 * @author Claudio
 */
class IndexController extends ApplicationController {
	/**
	 * (non-PHPdoc)
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		return $this->viewModel;
	}
	/**
	 * Pagina Exemplo
	 * @return \Application\Controller\Zend\View\Model\ViewModel
	 */
	public function blankAction() {
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Exibe XML para sitemap
	 * @return \Zend\View\Model\ViewModel
	 */
	public function sitemapAction() {
		// Explicitly set type to text/xml, otherwise it's text/html
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'text/xml' );
		// Only render the sitemap helper, without any layout
		$this->viewModel->setTerminal ( true );
		return $this->viewModel;
	}
	
	public function customerAction(){
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Politica e privacidade
	 * @return \Zend\View\Model\ViewModel
	 */
	public function privacyAction() {
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Termos do Servico
	 * 
	 * @return \Application\Controller\Zend\View\Model\ViewModel
	 */
	public function termsAction() {
		return $this->viewModel->setTerminal ( true );
	}
}