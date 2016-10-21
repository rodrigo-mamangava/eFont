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

    /**
     * Salva/Atualiza
     */
    public function saveAction() {
        // Default
        $data = $this->translate ( "Unknown Error, try again, please." );
        $outcome = $status = false;
        // System

        if ($this->getRequest ()->isPost ()) {
            try {
                // PARAMS
                $post = $this->postJsonp ();

                $data = [];

                $data['currency_dollar'] = isset ( $post ['currency_dollar'] ) ? $post ['currency_dollar'] : '';
                $data['currency_euro'] = isset ( $post ['currency_euro'] ) ? $post ['currency_euro'] : '';
                $data['currency_libra'] = isset ( $post ['currency_libra'] ) ? $post ['currency_libra'] : '';
                $data['currency_real'] = isset ( $post ['currency_real'] ) ? $post ['currency_real'] : '';

                // Formatos
                $data['check_fmt_otf'] = isset ( $post ['check_fmt_otf'] ) ? $post ['check_fmt_otf'] : false;
                $data['check_fmt_ttf'] = isset ( $post ['check_fmt_ttf'] ) ? $post ['check_fmt_ttf'] : false;
                $data['check_fmt_eot'] = isset ( $post ['check_fmt_eot'] ) ? $post ['check_fmt_eot'] : false;
                $data['check_fmt_woff'] = isset ( $post ['check_fmt_trial'] ) ? $post ['check_fmt_woff'] : false;
                $data['check_fmt_woff2'] = isset ( $post ['check_fmt_woff2'] ) ? $post ['check_fmt_woff2'] : false;
                $data['check_fmt_trial'] = isset ( $post ['check_fmt_trial'] ) ? $post ['check_fmt_trial'] : false;

                $CompanyController = new \Shop\Controller\CompanyController( $this->getMyServiceLocator () );

                $rs = $CompanyController->updated( $this->get_company_id (), $data, null );

                // Response
                $status = true;
                $outcome = $rs;

                $data = $this->translate ( "Company Profile updated." );

            } catch ( \Exception $e ) {
                $data = $e->getMessage ();
            }
        }
        // Response
        self::showResponse ( $status, $data, $outcome, true );
        die ();
    }
}