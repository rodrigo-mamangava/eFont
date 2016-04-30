<?php

namespace Application\Controller;

use \Validador\Controller\ValidadorController;
use \Useful\Controller\UsefulController;

/**
 * Detalhes do produto
 *
 * @author Claudio
 *        
 */
class ShopProductDetailsController extends ApplicationController {
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		return $this->viewModel->setTerminal ( true );
	}
	public function pricebookAction() {
		// Default
		$data = $this->translate ( "Unknown Error, try again, please." );
		$outcome = $status = false;
		// GET
		$Params = $this->params ();
		$desktop = $Params->fromQuery ( 'desktop', null );
		$app = $Params->fromQuery ( 'app', null );
		$web = $Params->fromQuery ( 'web', null );
		$project = $Params->fromQuery ( 'project', null );
		// Depois validar
		
		// Response
		self::showResponse ( $status, $data, $outcome, true );
		die ();
	}
	/**
	 * Obtem dados de um item
	 */
	public function editAction() {
		// Default
		$data = $this->translate ( "Unknown Error, try again, please." );
		$outcome = $status = false;
		// GET
		$Params = $this->params ();
		$id = $Params->fromQuery ( 'id', null );
		if (! ValidadorController::isValidDigits ( $id )) {
			$data = $this->translate ( 'Invalid Id' );
		} else {
			// Edit
			$Products = new \Shop\Controller\ProjectsController ( $this->getServiceLocator () );
			$LicenseUser = new \Shop\Controller\LicensesController ( $this->getServiceLocator () );
			$FamilyHasLicense = new \Shop\Controller\FamilyHasLicenseController ( $this->getServiceLocator () );
			$LicenseHasFormats = new \Shop\Controller\LicenseHasFormatsController ( $this->getServiceLocator () );
			$FamilyHasFormats = new \Shop\Controller\FamilyHasFormatsController ( $this->getServiceLocator () );
			$FamilyFiles = new \Shop\Controller\FamilyFilesController ( $this->getServiceLocator () );
			$Families = new \Shop\Controller\FamiliesController ( $this->getServiceLocator () );
			$Formats = new \Shop\Controller\LicenseFormatsController ( $this->getServiceLocator () );
			
			$rs = $Products->find ( $id, null );
			// Exists?
			if ($rs) {
				// Projeto
				$project = UsefulController::getStripslashes ( $rs );
				$project ['collection'] = '0.00';
				$licenses = array ();
				$families = array ();
				$styles = array ();
				$formats = array ();
				$preload = array (
						'license' => 0,
						'formats' => array (),
						'multiplier'=>array(),
						'collection'=>array(),
				);
				
				// LICENCAS
				$licenses = UsefulController::paginatorToArray ( $LicenseUser->fetchAllToShop ( $project->company_id, $project->id ));
				if (count ( $licenses ) > 0) {
					foreach ( $licenses as $lu_key => $lu_item ) {
						$licenses [$lu_key] = UsefulController::getStripslashes ( $lu_item );
						$licenses [$lu_key] ['formats'] = array ();
						$licenses [$lu_key] ['types_desktop'] = $licenses [$lu_key]['check_desktop'];
						$licenses [$lu_key] ['types_web'] = $licenses [$lu_key]['check_web'];
						$licenses [$lu_key] ['types_app'] = $licenses [$lu_key]['check_app'];
						// $licenses [$lu_key] ['families'] = array ();
						// Opcoes
						$lu_formats = $LicenseHasFormats->fetchAllToShop ( $lu_item->id, $project->company_id );
						if ($lu_formats->count () > 0) {
							$arr = iterator_to_array ( $lu_formats->getCurrentItems () );
							foreach ( $arr as $w_key => $w_item ) {
								$font = array ();
								$font ['id'] = isset ( $w_item ['id'] ) ? $w_item ['id'] : null;
								$font ['parameters'] = isset ( $w_item ['parameters'] ) ? $w_item ['parameters'] : null;
								$font ['multiplier'] = isset ( $w_item ['multiplier'] ) ? $w_item ['multiplier'] : 1;
								$license_formats_id = isset ( $w_item ['license_formats_id'] ) ? $w_item ['license_formats_id'] : 0;
								$font ['license_formats_id'] = $license_formats_id;
								$font ['collection'] = 0;
								
								if (! isset ( $preload ['formats'] [$lu_key] [$license_formats_id] )) {
									$preload ['formats'] [$lu_key] [$license_formats_id] = $font ['id'];
								}
								$preload ['multiplier'] [$lu_key] [$license_formats_id][$font ['id']] = $font ['multiplier'];
								// RESULTADO
								$licenses [$lu_key] ['formats'] [$license_formats_id] [$w_item->id] = $font;
								$styles [$w_item->id] = $font;
							}
						}
					}
				}
				
				// FORMATOS
				$formats = UsefulController::paginatorToArray ( $Formats->fetchAll () );
				
				// FAMILIAS
				$families_project = $Families->fetchAll ( $project->company_id, $project->id );
				if ($families_project->count () > 0) {
					foreach ( $families_project as $f_key => $f_item ) {
						$family = UsefulController::getStripslashes ( $f_item );
						$family ['collection'] = floatval ( $family ['collection'] ) + floatval ( $family ['money_family'] );
						$family ['check_collection'] = false;
						$family ['collapsed'] = false;
						// Formatos
						foreach ( $formats as $f_t_key => $f_t_item ) {
							if ($f_t_item->id > 0) {
								// ESTILOS
								$family_files = $FamilyFiles->fetchAllFamily ( $project->company_id, $project->id, $f_item->id, $f_t_item->id );
								if ($family_files->count () > 0) {
									foreach ( $family_files as $f_s_key => $f_s_item ) {
										$style = UsefulController::getStripslashes ( $f_s_item );
										$style ['font_price'] = isset ( $style ['font_price'] ) ? $style ['font_price'] : 0;
										$style ['font_weight'] = $style ['font_price'] > 0 ? $style ['font_price'] * 1 : 0;
										$family ['styles'] [$f_t_item->id] [$f_s_item->id] = $style;
										$style ['selected'] = false;
									}
								}
							}
						}
						// Params
						// Pesos/Valor
						$family_has_license = $FamilyHasLicense->fetchAllToShop ( $project->company_id, $f_item->id, $project->id );
						if ($family_has_license->count () > 0) {
							foreach ( $family_has_license as $f_h_l_key => $f_h_l_item ) {
								$family ['licenses'] [$f_h_l_item->license_id] = UsefulController::getStripslashes ( $f_h_l_item );								
								$preload['collection'][$f_item->id][$f_h_l_item->license_id] = $f_h_l_item->money_family;
							}
						}
						
						$families [$f_item->id] = $family;
					}
				}
				// Retorno
				$outcome = $status = true;
				$data = array (
						'project' => $project,
						'licenses' => $licenses,
						'families' => $families,
						'formats' => $formats,
						'preload' => $preload 
				);
			} else {
				$data = $this->translate ( 'Invalid Id' );
			}
		}
		// Response
		self::showResponse ( $status, $data, $outcome, true );
		die ();
	}
}