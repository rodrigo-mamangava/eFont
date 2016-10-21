<?php

namespace Application\Controller;

/**
 * Controller Profile
 * @author Claudio
 */
class CompanyProfileController extends ApplicationController
{
	/**
	 * (non-PHPdoc)
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
        //return $this->viewModel->setTerminal ( true );
        //Nada
	}

    /**
     * Busca
     */
    public function searchAction() {
        // Default

        // GET

        // Query

        //Data
        $data = array ();
        $data ['company'] = $this-> getCompanyProfile();

        // Response
        self::showResponse ( true, $data, true, true );
        die ();
    }
}