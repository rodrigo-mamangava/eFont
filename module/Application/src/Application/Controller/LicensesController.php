<?php

namespace Application\Controller;

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
	}
	/**
	 * Formulario
	 */
	public function formAction() {
		return $this->viewModel->setTerminal ( true );
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
			$data = array ();
			$data ['items'] = iterator_to_array ( $Paginator->getCurrentItems () );
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
		// Query
		$LicensesController = new \Shop\Controller\LicensesController ( $this->getMyServiceLocator () );
		$Paginator = $LicensesController->filter ( $search, $count, $offset, $company_id );
		
		if ($Paginator->count () > 0) {
			$data = array ();
			$data ['items'] = iterator_to_array ( $Paginator->getCurrentItems () );
			$data ['total'] = $Paginator->getTotalItemCount ();
			$data ['count'] = $count;
			$data ['offset'] = $offset;
			
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
				$formats_number = is_array ( $formats ) ? count ( $formats ) : 0;
				$formats_data = array ();
				if ($formats_number > 0) {
					foreach ( $formats as $f_key => $f_item ) {
						if (($f_key == 1 && $check_desktop == true) || ($f_key == 2 && $check_web == true) || ($f_key == 3 && $check_app == true)) {
							
							$font = array ();
							foreach ( $f_item as $w_key => $w_item ) {
								$font [$w_key] ['id'] = isset ( $w_item ['id'] ) ? $w_item ['id'] : null;
								$font [$w_key] ['parameters'] = isset ( $w_item ['parameters'] ) ? $w_item ['parameters'] : null;
								$font [$w_key] ['multiplier'] = isset ( $w_item ['multiplier'] ) ? $w_item ['multiplier'] : null;
								$font [$w_key] ['sequence'] = $w_key;
							}
							
							$formats_data [$f_key] = $font;
							unset ( $font );
						}
					}
				}
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
					$rs = $LicensesController->save ( $id, $name, $file, $company_id, $user_id, $check_trial, $check_desktop, $check_app, $check_web, $check_enabled, $currency_dollar, $currency_euro, $currency_libra, $currency_real );
					if ($rs) {
						
						$LicenseHasFormats = new \Shop\Controller\LicenseHasFormatsController ( $this->getMyServiceLocator () );
						$LicenseHasFormats->removeByLicense ( $rs, $company_id ); // Removemos para depois ativar, assim evita bug quando remove uma opcao ou adicionar
						sleep(1);
						
						//var_dump($formats_data);
						foreach ( $formats_data as $f_key => $f_item ) {
							foreach ( $f_item as $w_key => $w_item ) {
								$LicenseHasFormats->save ( $w_item ['id'], $rs, $f_key, $company_id, $user_id, $w_item ['parameters'], $w_item ['multiplier'], $w_item ['sequence'] );
							}
						}
					}
					// Response
					$status = true;
					$outcome = $rs;
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
			// Remove
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
				
				$data ['formats'] = array (
						array (),
						array (),
						array (),
						array () 
				);
				$formats = $LicensesHasFormats->fetchAll ( $id, $company_id );
				if ($formats->count () > 0) {
					$arr = iterator_to_array ( $formats->getCurrentItems () );
					foreach ( $arr as $w_key => $w_item ) {
						$font = array ();
						$font ['id'] = isset ( $w_item ['id'] ) ? $w_item ['id'] : null;
						$font ['parameters'] = isset ( $w_item ['parameters'] ) ? $w_item ['parameters'] : null;
						$font ['multiplier'] = isset ( $w_item ['multiplier'] ) ? $w_item ['multiplier'] : null;
						$seq = $font ['sequence'] = isset ( $w_item ['sequence'] ) ? $w_item ['sequence'] : 0;
						
						$f_key = isset ( $w_item ['license_formats_id'] ) ? $w_item ['license_formats_id'] : 0;
						$data ['formats'] [$f_key] [$seq] = $font;
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
}