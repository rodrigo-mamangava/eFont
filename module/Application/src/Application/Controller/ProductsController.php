<?php

namespace Application\Controller;

use \Validador\Controller\ValidadorController;
use Useful\Controller\UsefulController;

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
		// GET
		$Params = $this->params ();
		$count = $Params->fromQuery ( 'count', 10 );
		$offset = $Params->fromQuery ( 'offset', 0 );
		$search = $Params->fromQuery ( 'search', null );
		// Query
		$Products = new \Shop\Controller\ProjectsController ( $this->getMyServiceLocator () );
		$Paginator = $Products->filter ( $search, $count, $offset, $company_id );
		
		if ($Paginator->count () > 0) {
			
			$Family = new \Shop\Controller\FamiliesController ( $this->getServiceLocator () );
			$projects = iterator_to_array ( $Paginator->getCurrentItems () );
			foreach ( $projects as $key => $item ) {
				$projects [$key] ['families'] = UsefulController::paginatorToArray ( $Family->fetchAll ( $company_id, $item ['id'] ) );
			}
			
			$data = array ();
			$data ['items'] = $projects;
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
										$family_files ['id'] = isset ( $file_item ['id'] ) ? $file_item ['id'] : null;
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
										$formats_data ['files'] [$file_key] = $family_files;
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
								$license ['check_enabled'] = isset ( $lc_item ['check_enabled'] ) ? $lc_item ['check_enabled'] : false;
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
				
				// echo json_encode($families_data); exit;
				// var_dump($families_data); exit;
				if (! ValidadorController::isValidDigits ( $project_id )) {
					$data = $this->translate ( "Id." ) . ' ' . $this->translate ( "You can't leave this empty." );
				} elseif (! ValidadorController::isValidStringLength ( $project_name, 1, 100 )) {
					$data = $this->translate ( "Project Name" ) . ' ' . $this->translate ( "You can't leave this empty or exceeded the number of characters." );
				} else {
					// Controller
					$Products = new \Shop\Controller\ProjectsController ( $this->getServiceLocator () );
					$Family = new \Shop\Controller\FamiliesController ( $this->getServiceLocator () );
					$Formats = new \Shop\Controller\FamilyHasFormatsController ( $this->getServiceLocator () );
					$Fonts = new \Shop\Controller\FamilyFilesController ( $this->getServiceLocator () );
					$License = new \Shop\Controller\FamilyHasLicenseController ( $this->getServiceLocator () );
					// Chegando de salvou tudo
					$ok_project = false;
					$ok_family = false;
					$ok_formats = false;
					$ok_fonts = false;
					$ok_license = false;
					// Auxiliares
					$count = 0;
					$ok_logo = false;
					// PROJETOS
					$id = $Products->save ( $project_id, $project_name, $company_id, $user_id );
					if ($id) {
						
						$ok_project = true;
						$project_id = $id;
						// Clean
						$Family->cleanup ( $company_id, $project_id );
						$Formats->cleanup ( $company_id, $project_id );
						$Fonts->cleanup ( $company_id, $project_id );
						$License->cleanup ( $company_id, $project_id );
						// FAMILIAS
						foreach ( $families_data as $f_item ) {
							// FAMILIA
							$family_id = $Family->saved ( $f_item ['id'], $f_item ['family_name'], $project_id, $company_id, $user_id );
							if ($family_id) {
								
								$ok_family = true;
								// FORMATOS
								$formats = $f_item ['formats'];
								foreach ( $formats as $t_item ) {
									$family_has_formats_id = $Formats->save ( $t_item ['id'], $family_id, $t_item ['format_id'], $t_item ['media_url'], $t_item ['number_files'], $t_item ['collapsed'], $company_id, $user_id, $project_id );
									// var_dump($t_item ['format_id']);
									// FONTS
									// var_dump($family_has_formats_id);
									if ($family_has_formats_id) {
										
										$ok_formats = true;
										$files = isset($t_item ['files'])?$t_item ['files']:array();
										foreach ( $files as $fs_item ) {
											try {
												$font_id = $Fonts->save ( $fs_item ['id'], $fs_item ['font_name'], $fs_item ['font_id'], $fs_item ['font_subfamily'], $fs_item ['font_family'], $fs_item ['font_copyright'], $fs_item ['font_file'], $fs_item ['font_path'], $fs_item ['font_price'], $fs_item ['check_price'], $company_id, $user_id, $project_id, $family_id, $family_has_formats_id, $t_item ['format_id'] );
												
												if ($font_id) {
													$ok_fonts = true;
													
													try {
														$path_parts = pathinfo ( $fs_item ['font_file'] );
														if ($ok_logo == false && $path_parts ['extension'] == 'ttf') {
															$banner = \Useful\Controller\FontImageController::banner ( $fs_item ['font_path'], $f_item ['family_name'] );
															
															if ($banner) {
																
																$ok_logo = true;
																$Image = new \AWS\Controller\UploadController ( $this->getServiceLocator () );
																$img = $Image->uploadPathFile ( $banner );
																
																if (isset ( $img ['url'] )) {
																	$Products->updated ( $id, array (
																			'banner' => $img ['url'],
																			'ddig'=>$fs_item ['font_path']
																	), $company_id );
																}
															}
														}
													} catch ( Exception $e ) {
													}
												}
											} catch ( \Exception $e ) {
												// var_dump ( $font_id );
											}
											$count ++;
										}
									}
								}
								// LICENCAS
								$licenses = $f_item ['licenses'];
								foreach ( $licenses as $lc_item ) {
									// var_dump($lc_item);
									$lc_id = $License->save ( $lc_item ['id'], $lc_item ['money_family'], $lc_item ['money_weight'], $lc_item ['check_family'], $lc_item ['check_weight'], $lc_item ['check_enabled'], $project_id, $family_id, $lc_item ['lincese_id'], $company_id, $user_id );
									
									if ($lc_id) {
										$ok_license = true;
									}
								}
							}
						}
						// Salvou tudo?
						if ($ok_project && $ok_family && $ok_formats && $ok_fonts && $ok_license) {
							$status = true;
							$outcome = $project_id;
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
		// GET
		$Params = $this->params ();
		$id = $Params->fromQuery ( 'id', null );
		if (! ValidadorController::isValidDigits ( $id )) {
			$data = $this->translate ( 'Invalid Id' );
		} else {
			// Edit
			$Products = new \Shop\Controller\ProjectsController ( $this->getServiceLocator () );
			$Family = new \Shop\Controller\FamiliesController ( $this->getServiceLocator () );
			$Licenses = new \Shop\Controller\FamilyHasLicenseController ( $this->getServiceLocator () );
			$Formats = new \Shop\Controller\FamilyHasFormatsController ( $this->getServiceLocator () );
			$Files = new \Shop\Controller\FamilyFilesController ( $this->getServiceLocator () );
			
			$rs = $Products->find ( $id, $company_id );
			// Exists?
			if ($rs) {
				// Projeto
				$projects = UsefulController::getStripslashes ( $rs );
				// Familia
				$families = UsefulController::paginatorToArray ( $Family->fetchAll ( $company_id, $projects->id ) );
				if (count ( $families ) > 0) {
					foreach ( $families as $f_key => $f_item ) {
						$families [$f_key] = UsefulController::getStripslashes ( $f_item );
						
						// Licenas
						$licenses = UsefulController::paginatorToArray ( $Licenses->fetchAll ( $company_id, $f_item->id, $projects->id ) );
						if (count ( $licenses ) > 0) {
							foreach ( $licenses as $lc_key => $lc_item ) {
								$licenses [$lc_key] = UsefulController::getStripslashes ( $lc_item );
							}
						} else {
							$licenses = array ();
						}
						
						$families [$f_key] ['licenses'] = $licenses;
						
						// Formatos
						$formats = UsefulController::paginatorToArray ( $Formats->fetchAll ( $company_id, $f_item->id, $projects->id ) );
						if (count ( $formats ) > 0) {
							foreach ( $formats as $t_key => $t_item ) {
								$formats [$t_key] = UsefulController::getStripslashes ( $t_item );
								// Fontes
								$files = UsefulController::paginatorToArray ( $Files->fetchAll ( $company_id, $projects->id, $f_item->id, $t_item->id, $t_item->license_formats_id ) );
								if (count ( $files )) {
									foreach ( $files as $fs_key => $fs_item ) {
										$files [$fs_key] = UsefulController::getStripslashes ( $fs_item );
									}
								} else {
									$files = array ();
								}
								
								$formats [$t_key] ['files'] = $files;
							}
						} else {
							$formats = array ();
						}
						
						$families [$f_key] ['formats'] = $formats;
					}
				}
				$outcome = $status = true;
				$data = array (
						'project' => $projects,
						'families' => $families 
				);
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
			$Products = new \Shop\Controller\ProjectsController ( $this->getServiceLocator () );
			$rs = $Products->removed ( $id, $company_id );
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

