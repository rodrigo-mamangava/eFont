<?php

namespace Application\Controller;

use \Validador\Controller\ValidadorController;

/**
 * Controller dos produtos/projetos
 *
 * @author Claudio
 */
class ProductsController extends ApplicationController {
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
	 * Busca
	 */
	public function searchAction() {
		// Default
		$data = $this->translate ( "Unknown Error, try again, please." );
		$outcome = $status = false;
		// System
		$user_id = $this->get_user_id ();
		$company_id = $this->get_company_id ();
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
				
				// Projeto/Produto
				$projects = isset ( $post ['project'] ) ? $post ['project'] : null;
				$project_id = isset ( $projects ['id'] ) ? $projects ['id'] : 0;
				$project_name = isset ( $projects ['name'] ) ? $projects ['name'] : null;
				// var_dump($projects, $project_id, $project_name );
				
				// Familias
				$families = isset ( $post ['families'] ) ? $post ['families'] : null;
				$families_number = is_array ( $families ) ? count ( $families ) : 0;
				$families_data = array ();
				if ($families_number > 0) {
					// Familia
					foreach ( $families as $f_key => $f_item ) {
						$family = array ();
						$family ['id'] = isset ( $f_item ['id'] ) ? $f_item ['id'] : null;
						$family ['family_name'] = isset ( $f_item ['family_name'] ) ? $f_item ['family_name'] : null;
						
						// Formatos
						$formats = isset ( $f_item ['formats'] ) ? $f_item ['formats'] : null;
						$formats_number = is_array ( $formats ) ? count ( $formats ) : 0;
						if ($formats_number > 0) {
							
							foreach ( $formats as $t_key => $t_item ) {
								// Formato
								$formats_data = array ();
								$formats_data ['id'] = isset ( $t_item ['id'] ) ? $t_item ['id'] : null;
								$formats_data ['media_url'] = isset ( $t_item ['media_url'] ) ? $t_item ['media_url'] : null;
								$formats_data ['number_files'] = isset ( $t_item ['number_files'] ) ? $t_item ['number_files'] : null;
								$formats_data ['collapsed'] = isset ( $t_item ['collapsed'] ) ? $t_item ['collapsed'] : null;
								$formats_data ['format_id'] = isset ( $t_item ['format_id'] ) ? $t_item ['format_id'] : null;
								
								// Arquivos/Fontes
								$files = isset ( $t_item ['files'] ) ? $t_item ['files'] : null;
								$files_number = is_array ( $files ) ? count ( $files ) : 0;
								$files_data = array ();
								if ($files_number > 0) {
									
									foreach ( $files as $file_key => $file_item ) {
										// Fonte
										$family_files = array ();
										$family_files ['font_name'] = isset ( $file_item ['font_name'] ) ? $file_item ['font_name'] : null;
										$family_files ['font_id'] = isset ( $file_item ['font_id'] ) ? $file_item ['font_id'] : null;
										$family_files ['font_subfamily'] = isset ( $file_item ['font_subfamily'] ) ? $file_item ['font_subfamily'] : null;
										$family_files ['font_family'] = isset ( $file_item ['font_family'] ) ? $file_item ['font_family'] : null;
										$family_files ['font_copyright'] = isset ( $file_item ['font_copyright'] ) ? $file_item ['font_copyright'] : null;
										$family_files ['font_file'] = isset ( $file_item ['font_file'] ) ? $file_item ['font_file'] : null;
										$family_files ['font_path'] = isset ( $file_item ['font_path'] ) ? $file_item ['font_path'] : null;
										$family_files ['font_price'] = isset ( $file_item ['font_price'] ) ? $file_item ['font_price'] : null;
										$family_files ['check_price'] = isset ( $file_item ['check_price'] ) ? $file_item ['check_price'] : null;
										// DB
										$family_files ['formats_id'] = isset ( $file_item ['formats_id'] ) ? $file_item ['formats_id'] : null;
										$family_files ['family_id'] = isset ( $file_item ['family_id'] ) ? $file_item ['family_id'] : null;
										// Set/Unset
										$formats_data['files'] [$file_key] = $family_files;
										unset ( $family_files );
									}
								}
								// Set/Unset
								$family ['formats'] [$t_key] = $formats_data;
								unset ( $formats_data );
							}
						} else {
							throw new \Exception ( $this->translate ( 'Please, add one or more Format.' ) );
						}
						
						// Licencas
						$licenses = isset ( $f_item ['licenses'] ) ? $f_item ['licenses'] : null;
						$licenses_number = is_array ( $licenses ) ? count ( $licenses ) : 0;
						if ($licenses_number > 0) {
							
							foreach ( $licenses as $lc_key => $lc_item ) {
								// Licensa
								$license = array ();
								$license ['id'] = isset ( $lc_item ['id'] ) ? $lc_item ['id'] : null;
								$license ['lincese_id'] = isset ( $lc_item ['lincese_id'] ) ? $lc_item ['lincese_id'] : null;
								$license ['family_id'] = isset ( $lc_item ['family_id'] ) ? $lc_item ['family_id'] : null;
								$license ['check_family'] = isset ( $lc_item ['check_family'] ) ? $lc_item ['check_family'] : null;
								$license ['check_weight'] = isset ( $lc_item ['check_weight'] ) ? $lc_item ['check_weight'] : null;
								$license ['money_family'] = isset ( $lc_item ['money_family'] ) ? $lc_item ['money_family'] : null;
								$license ['money_weight'] = isset ( $lc_item ['money_weight'] ) ? $lc_item ['money_weight'] : null;
								// Set/Unset
								$family ['licenses'] [$lc_key] = $license;
								unset ( $license );
							}
						} else {
							throw new \Exception ( $this->translate ( 'Please, add one or more License.' ) );
						}
						
						// Set/Unset
						$families_data [] = $family;
						unset ( $family );
					}
				} else {
					throw new \Exception ( $this->translate ( 'Please, add one or more Family.' ) );
				}
				
				// var_dump($families_data); exit;
				if (! ValidadorController::isValidDigits ( $project_id )) {
					$data = $this->translate ( "Id." ) . ' ' . $this->translate ( "You can't leave this empty." );
				} elseif (! ValidadorController::isValidStringLength ( $project_name, 1, 100 )) {
					$data = $this->translate ( "Project Name" ) . ' ' . $this->translate ( "You can't leave this empty or exceeded the number of characters." );
				} else {
					// Controller
					$Products = new \Shop\controller\ProjectsController ( $this->getServiceLocator () );
					$Family = new \Shop\Controller\FamiliesController ( $this->getServiceLocator () );
					$Formats = new \Shop\Controller\FamilyHasFormatsController ( $this->getServiceLocator () );
					// PROJETOS
					$id = $Products->save ( $project_id, $project_name, $company_id, $user_id );
					if ($id) {
						// FAMILIAS
						foreach ( $families_data as $f_item ) {
							// FAMILIA
							$family_id = $Family->saved ( $f_item ['id'], $f_item ['family_name'], $id, $company_id, $user_id );
							if ($family_id) {
								// FORMATOS
								$formats = $f_item ['formats'];
								foreach ( $formats as $t_item ) {
									$format_id = $Formats->save ($t_item ['id'], $family_id, $t_item ['format_id'], $t_item ['media_url'], $t_item ['number_files'], $t_item, $t_item ['collapsed'], $company_id, $user_id, $id );
										// FONTS
									if ($format_id) {
										$files = $t_item ['files'];
										foreach ( $files as $file ) {
											var_dump ( $file );
										}
									}
								}
							}
						}
					}
				}
			} catch ( \Exception $e ) {
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
		// Response
		self::showResponse ( $status, $data, $outcome, true );
		die ();
	}
}

