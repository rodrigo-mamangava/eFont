<?php

namespace Application\Controller;

use Useful\Controller\UsefulController;
use \Validador\Controller\ValidadorController;

/**
 * Controller Licencas
 *
 * @author Claudio
 *        
 */
class LicensesController extends ApplicationController {
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
        return $this->viewModel->setTerminal ( true );
        //return $this->viewModel;
	}
	/**
	 * Formulario
	 */
	public function formAction() {
		return $this->viewModel->setTerminal ( true );
        //return $this->viewModel;
	}

    /**
     * Formulario Customizado
     */
    public function customFormAction() {
        return $this->viewModel->setTerminal ( true );
        //return $this->viewModel;
    }
	
	/**
	 * Ativas licenas
	 */
	public function activeAction(){
		// Default
		$data = $this->translate ( "Could not load licenses, please register one or more licenses." );
		$outcome = $status = false;
		// System
		$user_id = $this->get_user_id ();
		$company_id = $this->get_company_id ();
		// Query
		$LicensesController = new \Shop\Controller\LicensesController ( $this->getMyServiceLocator () );
		$Paginator = $LicensesController->fetchAllActive ($company_id);
		
		if ($Paginator->count () > 0) {
			$arr = iterator_to_array ( $Paginator->getCurrentItems () );
			$items = array();
			foreach($arr as $i){
				$items[$i->id] = $i;
			}
			
			$data = array ();
			$data ['items'] = $items;
			$data ['total'] = $Paginator->getTotalItemCount ();
			$data ['count'] = $Paginator->getTotalItemCount ();
			$data ['offset'] = 0;
				
			$outcome = $status = true;
		}
		// Response
		self::showResponse ( $status, $data, $outcome, true );
		die ();		
	}
	/**
	 * Busca
	 */
	public function searchAction() {
		// Default
		$data = $this->translate ( "Unknown Error, try again, please." );
		$outcome = $status = false;
		// System
		$user_id = $this->get_user_id ();
		$company_id = $this->get_company_id ();
		// GET
		$Params = $this->params ();
		$count = $Params->fromQuery ( 'count', 10 );
		$offset = $Params->fromQuery ( 'offset', 0 );
		$search = $Params->fromQuery ( 'search', null );
        $check_custom = $Params->fromQuery ( 'check_custom', 0 );
		// Query
		$LicensesController = new \Shop\Controller\LicensesController ( $this->getMyServiceLocator () );
		$Paginator = $LicensesController->filter ( $search, $count, $offset, $company_id, $check_custom );

        if ($Paginator->count () > 0) {
		    $arr = iterator_to_array ( $Paginator->getCurrentItems () );
            foreach ($arr as $k=>$item){
                $arr[$k] = UsefulController::getStripslashes($item);
            }
			$data = array ();
			$data ['items'] = $arr;
			$data ['total'] = $Paginator->getTotalItemCount ();
			$data ['count'] = $count;
			$data ['offset'] = $offset;

            //Dados da Empresa
            $data ['company'] = $this->getCompanyProfile();

            //Basic Licenses
            $data [ 'custom_basic_licenses' ] = $this->listBasicLicenses ( $check_custom, $company_id );
			
			$outcome = $status = true;
		}
		// Response
		self::showResponse ( $status, $data, $outcome, true );
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
		$user_id = $this->get_user_id ();
		$company_id = $this->get_company_id ();
		
		if ($this->getRequest ()->isPost ()) {
			try {
				// PARAMS
				$post = $this->postJsonp ();

				$id = isset ( $post ['id'] ) ? $post ['id'] : 0;
				$name = isset ( $post ['name'] ) ? $post ['name'] : null;
				$file = isset ( $post ['media_url'] ) ? $post ['media_url'] : null;
				
				$check_trial = isset ( $post ['check_trial'] ) ? $post ['check_trial'] : false;
				$check_desktop = isset ( $post ['check_desktop'] ) ? $post ['check_desktop'] : false;
				$check_web = isset ( $post ['check_web'] ) ? $post ['check_web'] : false;
				$check_app = isset ( $post ['check_app'] ) ? $post ['check_app'] : false;
				$check_enabled = isset ( $post ['check_enabled'] ) ? $post ['check_enabled'] : false;
				
				$currency_dollar = isset ( $post ['currency_dollar'] ) ? $post ['currency_dollar'] : '';
				$currency_euro = isset ( $post ['currency_euro'] ) ? $post ['currency_euro'] : '';
				$currency_libra = isset ( $post ['currency_libra'] ) ? $post ['currency_libra'] : '';
				$currency_real = isset ( $post ['currency_real'] ) ? $post ['currency_real'] : '';
				// Formatos
				$formats = isset ( $post ['formats'] ) ? $post ['formats'] : null;
                if ( is_array( $formats ) ){
                    $formats = array_filter( $formats );
                }
				$formats_number = is_array ( $formats ) ? count ( $formats ) : 0;
				$formats_data = array ();

                $check_fmt_otf = isset ( $post ['check_fmt_otf'] ) ? $post ['check_fmt_otf'] : false;
                $check_fmt_ttf = isset ( $post ['check_fmt_ttf'] ) ? $post ['check_fmt_ttf'] : false;
                $check_fmt_eot = isset ( $post ['check_fmt_eot'] ) ? $post ['check_fmt_eot'] : false;
                $check_fmt_woff = isset ( $post ['check_fmt_trial'] ) ? $post ['check_fmt_woff'] : false;
                $check_fmt_woff2 = isset ( $post ['check_fmt_woff2'] ) ? $post ['check_fmt_woff2'] : false;
                $check_fmt_trial = isset ( $post ['check_fmt_trial'] ) ? $post ['check_fmt_trial'] : false;

                $check_custom = isset ( $post ['check_custom'] ) ? $post ['check_custom'] : false;

//                echo "<pre>"; print_r($post['basic_licenses']); echo "</pre>";

				if ($formats_number > 0) {
					foreach ( $formats as $f_key => $f_item ) {
                        $font = array ();
                        foreach ( $f_item as $w_key => $w_item ) {
                            $font [$w_key] ['id'] = isset ( $w_item ['id'] ) ? $w_item ['id'] : null;
                            $font [$w_key] ['license_basic_id'] = isset ( $w_item ['license_basic_id'] ) ? $w_item ['license_basic_id'] : null;
                            $font [$w_key] ['parameters'] = isset ( $w_item ['parameters'] ) ? $w_item ['parameters'] : null;
                            $font [$w_key] ['multiplier'] = isset ( $w_item ['multiplier'] ) ? $w_item ['multiplier'] : null;
                            $font [$w_key] ['sequence'] = $w_key;
                        }

                        $formats_data [$f_key] = $font;
                        unset ( $font );
					}
				}

                // Basic licenses
                $basic_licenses = isset ( $post ['basic_licenses'] ) ? $post ['basic_licenses'] : null;
                $basic_licenses_number = is_array ( $basic_licenses ) ? count ( $basic_licenses ) : 0;

				// Validate
				if (! ValidadorController::isValidDigits ( $id )) {
					$data = $this->translate ( "Id." ) . ' ' . $this->translate ( "You can't leave this empty." );
				} elseif (! ValidadorController::isValidDigits ( $company_id )) {
					$data = $this->translate ( "Licenses." ) . ' ' . $this->translate ( "You can't leave this empty." );
				} elseif (! ValidadorController::isValidStringLength ( $name, 1, 100 )) {
					$data = $this->translate ( "Name." ) . ' ' . $this->translate ( "You can't leave this empty or exceeded the number of characters." );
				} else {
					// Mapper
					$LicensesController = new \Shop\Controller\LicensesController ( $this->getMyServiceLocator () );
					$rs = $LicensesController->save (
					    $id, $name, $file, $company_id, $user_id, $check_trial,
                        $check_desktop, $check_app, $check_web, $check_enabled,
                        $currency_dollar, $currency_euro, $currency_libra, $currency_real,
                        $check_fmt_otf, $check_fmt_ttf, $check_fmt_eot, $check_fmt_woff,
                        $check_fmt_woff2, $check_fmt_trial, $check_custom
                    );
					if ($rs) {
						
						$LicenseHasFormats = new \Shop\Controller\LicenseHasFormatsController ( $this->getMyServiceLocator () );
						$LicenseHasFormats->removeByLicense ( $rs, $company_id ); // Removemos para depois ativar, assim evita bug quando remove uma opcao ou adicionar

                        //Se for licenca custom, trata os relacionamentos com as licensas basicas que irao compor
                        // a licenca custom/combo.
                        if ( $check_custom ) {
                            $CustomLicenseHasBasicLicenses = new \Shop\Controller\CustomLicenseHasBasicLicensesController( $this->getMyServiceLocator () );
                            $CustomLicenseHasBasicLicenses->removeByCustomLicense( $rs ); // Removemos para depois incluir toda a nova lista

                            if ( is_array( $basic_licenses ) && $basic_licenses_number > 0 ) {
                                foreach ( $basic_licenses as $basic_license ) {
                                    if ( $basic_license[ 'check_enabled' ] ){
                                        $CustomLicenseHasBasicLicenses->save( $rs, $basic_license['license_basic_id'] );
                                    }
                                }
                            }
                        }
                        sleep(1);
						
						//var_dump($formats_data);
						foreach ( $formats_data as $f_key => $f_item ) {
							foreach ( $f_item as $w_key => $w_item ) {
                                if ( ( $check_custom ) && ( $w_item ['parameters'] == '' ) ){
                                    continue; // pula para o proximo
                                }
								$LicenseHasFormats->save (
								    $w_item ['id'], $rs, $f_key, $company_id,
                                    $user_id, $w_item ['parameters'], $w_item ['multiplier'],
                                    $w_item ['sequence'], $w_item ['license_basic_id']
                                );
							}
						}
					}
					// Response
					$status = true;
					$outcome = $rs;
                    $data = $this->translate ( "Data have been saved!" );
				}
			} catch ( \Exception $e ) {
				$data = $e->getMessage ();
			}
		}
		// Response
		self::showResponse ( $status, $data, $outcome, true );
		die ();
	}
	/**
	 * Edicao
	 */
	public function editAction() {
		// Default
		$data = $this->translate ( "Unknown Error, try again, please." );
		$outcome = $status = false;
		// System
		$user_id = $this->get_user_id ();
		$company_id = $this->get_company_id ();
		// GET
		$Params = $this->params ();
		$id = $Params->fromQuery ( 'id', null );
		if (! ValidadorController::isValidDigits ( $id )) {
			$data = $this->translate ( 'Invalid Id' );
		} else {
			// Edit
			$Licenses = new \Shop\Controller\LicensesController ( $this->getServiceLocator () );
			$LicensesHasFormats = new \Shop\Controller\LicenseHasFormatsController ( $this->getServiceLocator () );
			
			$rs = $Licenses->find ( $id, $company_id );
			// Exists?
			if ($rs) {
				$data = \Useful\Controller\UsefulController::getStripslashes ( $rs );
				
				$data ['check_trial'] = $data ['check_trial'] == 1 ? true : false;
				$data ['check_desktop'] = $data ['check_desktop'] == 1 ? true : false;
				$data ['check_app'] = $data ['check_app'] == 1 ? true : false;
				$data ['check_web'] = $data ['check_web'] == 1 ? true : false;
				$data ['check_enabled'] = $data ['check_enabled'] == 1 ? true : false;
                $data ['check_custom'] = $data ['check_custom'] == 1 ? true : false;
				
				$data ['formats'] = array (
//						array (),
//						array (),
//						array (),
//						array ()
				);
				$formats = $LicensesHasFormats->fetchAll ( $id, $company_id );
				if ($formats->count () > 0) {
					$arr = iterator_to_array ( $formats->getCurrentItems () );
					foreach ( $arr as $w_key => $w_item ) {
						$font = array ();
						$font ['id'] = isset ( $w_item ['id'] ) ? $w_item ['id'] : null;
                        $font ['license_basic_id'] = isset ( $w_item ['license_basic_id'] ) ? $w_item ['license_basic_id'] : null;
						$font ['parameters'] = isset ( $w_item ['parameters'] ) ? $w_item ['parameters'] : null;
						$font ['multiplier'] = isset ( $w_item ['multiplier'] ) ? $w_item ['multiplier'] : 1;
						$seq = $font ['sequence'] = isset ( $w_item ['sequence'] ) ? $w_item ['sequence'] : 0;
						
						$f_key = isset ( $w_item ['license_formats_id'] ) ? $w_item ['license_formats_id'] : 0;
						$data ['formats'] [$f_key] [$seq] = $font;
					}
				}

				if ( $data ['check_custom'] ) {
                    $CustomLicenseHasBasicLicenses = new \Shop\Controller\CustomLicenseHasBasicLicensesController( $this->getMyServiceLocator () );
                    $basic_licenses = $CustomLicenseHasBasicLicenses->fetchAll( $id );
                    if ($basic_licenses->count () > 0) {
                        $basic_licenses_arr = iterator_to_array ( $basic_licenses->getCurrentItems () );
                        foreach ( $basic_licenses_arr as $w_key => $w_item ) {
                            $b_license = [];
                            $b_license['license_basic_id'] = isset ( $w_item ['license_basic_id'] ) ? $w_item ['license_basic_id'] : null;
                            $b_license['name'] = isset ( $w_item ['name'] ) ? $w_item ['name'] : null;
                            $b_license['check_enabled'] = true;
                            $data [ 'basic_licenses' ] [ $b_license['license_basic_id'] ] = $b_license;
                            //USAR UM VETOR ESPELHADO
                            $data [ 'basic_licenses_suport' ] [ ] = $b_license;
                        }
                    }
                }

				$outcome = $status = true;
			} else {
				$data = $this->translate ( 'Invalid Id' );
			}
		}
		// Response
		self::showResponse ( $status, $data, $outcome, true );
		die ();
	}
	/**
	 * Remocao
	 */
	public function removeAction() {
		// Default
		$data = $this->translate ( "Unknown Error, try again, please." );
		$outcome = $status = false;
		// System
		$user_id = $this->get_user_id ();
		$company_id = $this->get_company_id ();
		// GET
		$Params = $this->params ();
		$id = $Params->fromQuery ( 'id', null );
		$id = abs ( $id );
		if (! ValidadorController::isValidDigits ( $id )) {
			$data = 'Permission denied';
		} else {
			// Remove
			$Licenses = new \Shop\Controller\LicensesController ( $this->getServiceLocator () );
			$rs = $Licenses->removed ( $id, $company_id );
			if ($rs) {
				// Success
				$outcome = $status = true;
			} else {
				$data = $rs;
			}
		}
		self::showResponse ( $status, $data, $outcome, true );
		die ();
	}

    /**
     * Ativacao
     */
	public function activateAction(){
        // Default
        $data = $this->translate ( "Unknown Error, try again, please." );
        $outcome = $status = false;
        // System
        $user_id = $this->get_user_id ();
        $company_id = $this->get_company_id ();

        if ($this->getRequest ()->isPost ()) {
            try {
                // PARAMS
                $post = $this->postJsonp ();
                $id = isset ( $post ['id'] ) ? $post ['id'] : 0;
                $name = isset ( $post ['name'] ) ? $post ['name'] : null;
                $file = isset ( $post ['media_url'] ) ? $post ['media_url'] : null;

                $check_trial = isset ( $post ['check_trial'] ) ? $post ['check_trial'] : false;
                $check_desktop = isset ( $post ['check_desktop'] ) ? $post ['check_desktop'] : false;
                $check_web = isset ( $post ['check_web'] ) ? $post ['check_web'] : false;
                $check_app = isset ( $post ['check_app'] ) ? $post ['check_app'] : false;

                $currency_dollar = isset ( $post ['currency_dollar'] ) ? $post ['currency_dollar'] : '';
                $currency_euro = isset ( $post ['currency_euro'] ) ? $post ['currency_euro'] : '';
                $currency_libra = isset ( $post ['currency_libra'] ) ? $post ['currency_libra'] : '';
                $currency_real = isset ( $post ['currency_real'] ) ? $post ['currency_real'] : '';

                $check_fmt_otf = isset ( $post ['check_fmt_otf'] ) ? $post ['check_fmt_otf'] : false;
                $check_fmt_ttf = isset ( $post ['check_fmt_ttf'] ) ? $post ['check_fmt_ttf'] : false;
                $check_fmt_eot = isset ( $post ['check_fmt_eot'] ) ? $post ['check_fmt_eot'] : false;
                $check_fmt_woff = isset ( $post ['check_fmt_trial'] ) ? $post ['check_fmt_woff'] : false;
                $check_fmt_woff2 = isset ( $post ['check_fmt_woff2'] ) ? $post ['check_fmt_woff2'] : false;
                $check_fmt_trial = isset ( $post ['check_fmt_trial'] ) ? $post ['check_fmt_trial'] : false;

                $check_custom = isset ( $post ['check_custom'] ) ? $post ['check_custom'] : false;

                $check_enabled = isset ( $post ['check_enabled'] ) ? $post ['check_enabled'] : false;

                // Mapper
                $LicensesController = new \Shop\Controller\LicensesController ( $this->getMyServiceLocator () );

                $rs = $LicensesController->save (
                        $id, $name, $file, $company_id, $user_id, $check_trial,
                        $check_desktop, $check_app, $check_web, $check_enabled,
                        $currency_dollar, $currency_euro, $currency_libra, $currency_real,
                        $check_fmt_otf, $check_fmt_ttf, $check_fmt_eot, $check_fmt_woff,
                        $check_fmt_woff2, $check_fmt_trial, $check_custom
                    );
                // Response
                $status = true;
                $outcome = $rs;

                $data = ( $check_enabled )
                        ? $this->translate ( "The license has been activated." )
                        : $this->translate ( "The license has been deactivated." );
            } catch ( \Exception $e ) {
                $data = $e->getMessage ();
            }
        }
        // Response
        self::showResponse ( $status, $data, $outcome, true );
        die ();
    }

    /**
     * @param $check_custom
     * @param $company_id
     * @return array|null
     */
    protected function listBasicLicenses( $check_custom, $company_id ){
        if ( $check_custom == 0 ) {
            return null;
        }
        $CustomLicenseHasBasicLicenseController = new \Shop\Controller\CustomLicenseHasBasicLicensesController( $this->getMyServiceLocator () );
        $PaginatorBasicLicenses = $CustomLicenseHasBasicLicenseController->fetchAllByCompanyId( $company_id );
        if ( $PaginatorBasicLicenses->count () <= 0 ) {
            return null;
        }
        $arr_basic_licenses = iterator_to_array ( $PaginatorBasicLicenses->getCurrentItems () );
        foreach ( $arr_basic_licenses as $k=>$item ){
            $arr_basic_licenses[$k] = UsefulController::getStripslashes($item);
        }
        $basic_licenses = [];
        foreach ( $arr_basic_licenses as $k=>$i ) {
            $basic_licenses[ $i['license_custom_id'] ][] = [
                'license_basic_id'  => $i['license_basic_id'] ,
                'name'              => $i['name']
            ];
        }
        return $basic_licenses;
    }
}