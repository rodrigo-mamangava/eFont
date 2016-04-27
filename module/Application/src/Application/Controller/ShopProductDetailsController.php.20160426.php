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
			
			$rs = $Products->find ( $id, null );
			// Exists?
			if ($rs) {
				// Projeto
				$project = UsefulController::getStripslashes ( $rs );
				$project ['collection'] = '0.00';
				$licenses = array ();
				$families = array ();
				$styles = array ();
				
				// LICENCAS
				// Licenses
				$licenses = UsefulController::paginatorToArray ( $LicenseUser->fetchAllActive ( $project->company_id ) );
				if (count ( $licenses ) > 0) {
					foreach ( $licenses as $lu_key => $lu_item ) {
						$licenses [$lu_key] = UsefulController::getStripslashes ( $lu_item );
						$licenses [$lu_key] ['formats'] = array ();
						
						$lu_formats = $LicenseHasFormats->fetchAll ( $lu_item->id, $project->company_id );
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
								// FAMILIAS
								$family_has_license = $FamilyHasLicense->fetchAllByProject ( $project->company_id, $project->id, $lu_item->id, $license_formats_id );
								if ($family_has_license->count () > 0) {
									foreach ( $family_has_license as $f_h_l_key => $f_h_l__item ) {
										$family = UsefulController::getStripslashes ( $f_h_l__item );
										
										if ($license_formats_id != 0) {
											$font ['collection'] = floatval ( $font ['collection'] ) + floatval ( $family ['money_family'] );
										}
										// ESTILOS
										$family_files = $FamilyFiles->fetchAllByProject ( $project->company_id, $project->id, $family->f_id, $family->f_h_f_id, $license_formats_id );
										$family ['styles'] = array ();
										if ($family_files->count () > 0) {
											foreach ( $family_files as $f_s_key => $f_s_item ) {
												$style = UsefulController::getStripslashes ( $f_s_item );
												$style ['font_price'] = isset ( $style ['font_price'] ) ? $style ['font_price'] : 0;
												$style ['font_weight'] = $style ['font_price'] > 0 ? $style ['font_price'] * $font ['multiplier'] : 0;
												
												$family ['styles'] [$f_s_item->id] = $style;
											}
										}
										
										$font ['families'] [$f_h_l_key] = $family;
									}
								}
								unset ( $collection );
								// RESULTADO
								$licenses [$lu_key] ['formats'] [$license_formats_id] [$w_item->id] = $font;
								$styles [$w_item->id] = $font;
							}
						}
					}
				}
				
				// Retorno
				$outcome = $status = true;
				$data = array (
						'project' => $project,
						'licenses' => $licenses 
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