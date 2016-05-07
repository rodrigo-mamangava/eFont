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
		$Projects = new \Shop\Controller\ProjectsController ( $this->getMyServiceLocator () );
		$Paginator = $Projects->filter ( $search, $count, $offset, $company_id );
		
		if ($Paginator->count () > 0) {
			
			$ProjectHasFamily = new \Shop\Controller\ProjectHasFamilyController ( $this->getServiceLocator () );
			$projects = iterator_to_array ( $Paginator->getCurrentItems () );
			foreach ( $projects as $key => $item ) {
				$projects [$key] ['families'] = UsefulController::paginatorToArray ( $ProjectHasFamily->fetchAll ( $company_id, $item ['id'] ) );
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
				// Para controle
				// Chegando se enviou tudo certo
				$alright_project = true;
				
				$alright_family = true;
				$alright_formats = false;
				$alright_fonts = true;
				$alright_files = false;
				$alright_license = false;
				$alright_families = array ();
				// Projeto/Produto
				$projects = isset ( $post ['project'] ) ? $post ['project'] : null;
				$project_id = isset ( $projects ['id'] ) ? $projects ['id'] : 0;
				$project_name = isset ( $projects ['name'] ) ? $projects ['name'] : null;
				$project_ddig = isset ( $projects ['ddig'] ) ? $projects ['ddig'] : null;
				// Licencas
				$alright_license = false;
				$licenses = isset ( $projects ['licenses'] ) ? $projects ['licenses'] : null;
				$licenses_number = is_array ( $licenses ) ? count ( $licenses ) : 0;
				$licenses_data = array ();
				if ($licenses_number > 0) {
					foreach ( $licenses as $lc_key => $lc_item ) {
						// Licensa
						$license = array ();
						$license ['license_id'] = isset ( $lc_item ['license_id'] ) ? $lc_item ['license_id'] : null;
						$license ['id'] = isset ( $lc_item ['id'] ) ? $lc_item ['id'] : null;
						$license ['check_family'] = isset ( $lc_item ['check_family'] ) ? $lc_item ['check_family'] : null;
						$license ['check_weight'] = isset ( $lc_item ['check_weight'] ) ? $lc_item ['check_weight'] : null;
						$license ['check_enabled'] = isset ( $lc_item ['check_enabled'] ) ? $lc_item ['check_enabled'] : false;
						$license ['money_family'] = isset ( $lc_item ['money_family'] ) ? $lc_item ['money_family'] : null;
						$license ['money_weight'] = isset ( $lc_item ['money_weight'] ) ? $lc_item ['money_weight'] : null;
						$license ['sequence'] = isset ( $lc_item ['sequence'] ) ? $lc_item ['sequence'] : $lc_key;
						
						if (! ValidadorController::isValidDigits ( $license ['license_id'] ) || $license ['check_enabled'] == false) {
							continue;
						}
						// Set/Unset
						$licenses_data [$lc_key] = $license;
						unset ( $license );
						$alright_license = true;
					}
				} else {
					throw new \Exception ( $this->translate ( 'Please, add one or more License.' ) );
				}
				// Familias
				$alright_family = true;
				$families = isset ( $post ['families'] ) ? $post ['families'] : null;
				$families_number = is_array ( $families ) ? count ( $families ) : 0;
				$families_data = array ();
				if ($families_number > 0) {
					foreach ( $families as $f_key => $f_item ) {
						// Conferencia
						$alright_families [$f_key] = array (
								'alright_family' => $alright_family,
								'alright_formats' => $alright_formats,
								'alright_fonts' => $alright_fonts,
								'alright_files' => $alright_files,
								'alright_license' => $alright_license 
						);
						// BEGIN FAMILY
						// Familia
						$family = array ();
						$family ['id'] = isset ( $f_item ['id'] ) ? $f_item ['id'] : null;
						$family ['family_name'] = isset ( $f_item ['family_name'] ) ? $f_item ['family_name'] : null;
						$family ['check_weight'] = isset ( $f_item ['check_weight'] ) ? $f_item ['check_weight'] : null;
						$family ['check_enabled'] = isset ( $f_item ['check_enabled'] ) ? $f_item ['check_enabled'] : false;
						$family ['check_family'] = isset ( $f_item ['check_family'] ) ? $f_item ['check_family'] : false;
						$family ['money_family'] = isset ( $f_item ['money_family'] ) ? $f_item ['money_family'] : null;
						$family ['money_weight'] = isset ( $f_item ['money_weight'] ) ? $f_item ['money_weight'] : null;
						$family ['sequence'] = isset ( $f_item ['sequence'] ) ? $f_item ['sequence'] : $f_key;
						
						if (! ValidadorController::isValidStringLength ( $family ['family_name'], 1, 150 )) {
							$alright_families [$f_key] ['alright_family'] = false;
							break;
						}
						
						if (! ValidadorController::isValidNotEmpty ( $family ['money_weight'] )) {
							$alright_families [$f_key] ['alright_family'] = false;
							break;
						}
						// Formatos
						$formats = isset ( $f_item ['formats'] ) ? $f_item ['formats'] : null;
						$formats_number = is_array ( $formats ) ? count ( $formats ) : 0;
						if ($formats_number > 0) {
							
							$alright_families [$f_key] ['alright_formats'] = false; // Vamos trocar depois se tiver tudo certo
							
							foreach ( $formats as $t_key => $t_item ) {
								// BEGIN FORMAT
								// Formato
								$formats_data = array ();
								$formats_data ['id'] = isset ( $t_item ['id'] ) ? $t_item ['id'] : null;
								$formats_data ['media_url'] = isset ( $t_item ['media_url'] ) ? $t_item ['media_url'] : null;
								$formats_data ['number_files'] = isset ( $t_item ['number_files'] ) ? $t_item ['number_files'] : null;
								$formats_data ['collapsed'] = isset ( $t_item ['collapsed'] ) ? $t_item ['collapsed'] : null;
								$formats_data ['format_id'] = isset ( $t_item ['format_id'] ) ? $t_item ['format_id'] : null;
								
								if ($formats_data ['number_files'] != null && ValidadorController::isValidDigits ( $formats_data ['number_files'] ) && $formats_data ['number_files'] > 0) {
									$alright_families [$f_key] ['alright_formats'] = true;
								}
								
								if (! ValidadorController::isValidDigits ( $formats_data ['number_files'] )) {
									$formats_data ['number_files'] = 0;
								}
								// BEGIN FILES/STYLES
								// Arquivos/Fontes
								$files = isset ( $t_item ['files'] ) ? $t_item ['files'] : null;
								$files_number = is_array ( $files ) ? count ( $files ) : 0;
								$files_data = array ();
								if ($files_number > 0) {
									
									$alright_families [$f_key] ['alright_files'] = true;
									
									foreach ( $files as $file_key => $file_item ) {
										// Fonte
										$file = array ();
										$file ['id'] = isset ( $file_item ['id'] ) ? $file_item ['id'] : null;
										$file ['font_subfamily'] = isset ( $file_item ['font_subfamily'] ) ? $file_item ['font_subfamily'] : null;
										$file ['font_file'] = isset ( $file_item ['font_file'] ) ? $file_item ['font_file'] : null;
										$file ['font_price'] = isset ( $file_item ['font_price'] ) ? $file_item ['font_price'] : null;
										$file ['check_price'] = isset ( $file_item ['check_price'] ) ? $file_item ['check_price'] : null;
										if (! ValidadorController::isValidNotEmpty ( $file ['font_price'] )) {
											$alright_families [$f_key] ['alright_fonts'] = false;
										}
										// Set/Unset
										$formats_data ['files'] [$file_key] = $file;
										unset ( $file );
									}
								}
								// Set/Unset
								$family ['formats'] [$t_key] = $formats_data;
								unset ( $formats_data );
								// END FILES/STYLES
								// END FORMAT
							}
						} else {
							throw new \Exception ( $this->translate ( 'Please, add one or more Format.' ) );
						}
						// END FAMILY
						// Set/Unset
						$families_data [$f_key] = $family;
						unset ( $family );
					}
				} else {
					throw new \Exception ( $this->translate ( 'Please, add one or more Family.' ) );
				}
				
				// Pendencias?
				$issues = array ();
				foreach ( $alright_families as $alright_itens ) {
					foreach ( $alright_itens as $alright_key => $alright_item ) {
						if (! $alright_item) {
							$alright_project = false;
							if ($alright_key == 'alright_family') {
								array_unshift ( $issues, $this->translate ( 'Check if family name was filled correctly and and prices (WEIGHT AND FAMILY) were filled.' ) );
							} elseif ($alright_key == 'alright_formats') {
								array_unshift ( $issues, $this->translate ( 'Check if at least one file has been uploaded.' ) );
							} elseif ($alright_key == 'alright_fonts') {
								array_unshift ( $issues, $this->translate ( 'Check if all fonts with price.' ) );
							} elseif ($alright_key == 'alright_files') {
								array_unshift ( $issues, $this->translate ( 'Check if all uploaded files have at least one valid file.' ) );
							} elseif ($alright_key == 'alright_license') {
								array_unshift ( $issues, $this->translate ( 'Check if there is at least one active license and has set the price of the family.' ) );
							}
						}
					}
				}
				// Tudo ok por aqui?
				if ($alright_project == false) {
					$issues = array_unique ( $issues );
					$data = $this->translate ( 'You cannot publish your project because there are pending:' );
					foreach ( $issues as $issue ) {
						$data .= '<br/>' . $issue;
					}
					throw new \Exception ( $data );
				}
				
				//exit ();
				// Validando
				if (! ValidadorController::isValidDigits ( $project_id )) {
					$data = $this->translate ( "Id." ) . ' ' . $this->translate ( "You can't leave this empty." );
				} elseif (! ValidadorController::isValidStringLength ( $project_name, 1, 100 )) {
					$data = $this->translate ( "Project Name" ) . ' ' . $this->translate ( "You can't leave this empty or exceeded the number of characters." );
				} else {
					// Controller
					$Projects = new \Shop\Controller\ProjectsController ( $this->getServiceLocator () );
					$ProjectHasLicense = new \Shop\Controller\ProjectHasLicenseController ( $this->getServiceLocator () );
					$ProjectHasFamily = new \Shop\Controller\ProjectHasFamilyController ( $this->getServiceLocator () );
					$FamilyHasFormats = new \Shop\Controller\FamilyHasFormatsController ( $this->getServiceLocator () );
					$FontStyles = new \Shop\Controller\FontStylesController ( $this->getServiceLocator () );
					$FontFiles = new \Shop\Controller\FontFilesController ( $this->getServiceLocator () );
					// Chegando de salvou tudo
					$ok_project = false;
					$ok_family = false;
					$ok_license = false;
					$ok_formats = false;
					$ok_fonts = false;
					// Auxiliares
					$count = 0;
					$ok_logo = false;
					$project_banner = '';
					// BANNER
					try {
						$path_parts = pathinfo ( $project_ddig );
						if ($ok_logo == false && $path_parts ['extension'] == 'ttf') {
							$banner = \Useful\Controller\FontImageController::banner ( $project_ddig, $project_name );
							if ($banner) {
								$ok_logo = true;
								$Image = new \AWS\Controller\UploadController ( $this->getServiceLocator () );
								$img = $Image->uploadPathFile ( $banner );
								$project_banner = isset ( $img ['url'] ) ? $img ['url'] : '';
							}
						}
					} catch ( Exception $e ) {
					}
					// PROJETOS
					$id = $Projects->save ( $project_id, $project_name, $company_id, $user_id, $project_ddig, $project_banner );
					if ($id) {
						$ok_project = true;
						$project_id = $id;
						// LIMPEZA PARA DEPOIS ATUALIZAR
						$ProjectHasLicense->cleanup ( $company_id, $project_id );
						$ProjectHasFamily->cleanup ( $company_id, $project_id );
						$FamilyHasFormats->cleanup ( $company_id, $project_id );
						$FontStyles->cleanup ( $company_id, $project_id );
						$FontFiles->cleanup ( $company_id, $project_id );
						// LICENCAS
						foreach ( $licenses_data as $lc_item ) {
							$lc_id = $ProjectHasLicense->save ( $lc_item ['id'], $lc_item ['money_family'], $lc_item ['money_weight'], $lc_item ['check_family'], $lc_item ['check_weight'], $lc_item ['license_id'], $company_id, $user_id, $lc_item ['check_enabled'], $project_id, $lc_item ['sequence'] );
							if ($lc_id) {
								$ok_license = true;
							}
						}
						// BEGIN FAMILY
						// FAMILIAS
						foreach ( $families_data as $f_item ) {
							$family_id = $ProjectHasFamily->save ( $f_item ['id'], $f_item ['family_name'], $f_item ['money_family'], $f_item ['money_weight'], $f_item ['check_family'], $f_item ['check_weight'], $f_item ['sequence'], $company_id, $user_id, $project_id );
							if ($family_id) {
								$ok_family = true;
								// BEGIN FORMAT
								// FORMATOS
								$formats = $f_item ['formats'];
								foreach ( $formats as $t_item ) {
									$family_has_formats_id = $FamilyHasFormats->save ( $t_item ['id'], $family_id, $t_item ['format_id'], $t_item ['media_url'], $t_item ['number_files'], $t_item ['collapsed'], $company_id, $user_id, $project_id );
									if ($family_has_formats_id) {
										$ok_formats = true;
										// BEGIN FILES/STYLES
										// ARQUIVOS
										$files = isset ( $t_item ['files'] ) ? $t_item ['files'] : array ();
										foreach ( $files as $fs_key => $fs_item ) {
											$fs_id = $fs_item ['id'];
											try {
												$FontStyles->updated ( $fs_id, $company_id, $user_id, array (
														'project_id' => $project_id,
														'family_id' => $family_id,
														'family_has_formats_id' => $family_has_formats_id,
														'linked' => 1,
														'removed' => 0,
														'font_subfamily' => $fs_item ['font_subfamily'],
														'font_price' => $fs_item ['font_price'],
														'check_price' => $fs_item ['check_price'] 
												) );
												
												$FontFiles->synchronize ( $fs_id, $company_id, $user_id, array (
														'project_id' => $project_id,
														'family_id' => $family_id,
														'removed' => 0 
												) );
												
												$ok_fonts = true;
											} catch ( \Exception $e ) {
												// var_dump ( $font_id );
											}
											$count ++;
										}
										// END FILES/STYLES
									}
								}
								// END FORMAT
							}
						}
						// END FAMILY
						// Salvou tudo?
						if ($ok_project && $ok_family && $ok_formats && $ok_fonts && $ok_license) {
							$status = true;
							$outcome = $project_id;
						}
					}
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
			$Projects = new \Shop\Controller\ProjectsController ( $this->getServiceLocator () );
			$ProjectHasFamily = new \Shop\Controller\ProjectHasFamilyController ( $this->getServiceLocator () );
			$ProjetHasLicense = new \Shop\Controller\ProjectHasLicenseController ( $this->getServiceLocator () );
			$FamilyHasFormats = new \Shop\Controller\FamilyHasFormatsController ( $this->getServiceLocator () );
			$FontStyles = new \Shop\Controller\FontStylesController ( $this->getServiceLocator () );
			$Licences = new \Shop\Controller\LicensesController ( $this->getServiceLocator () );
			
			$rs = $Projects->find ( $id, $company_id );
			$rs_families = array ();
			$rs_projects = array ();
			$rs_license = array ();
			$rs_formats = array ();
			$rs_files = array ();
			// Exists?
			if ($rs) {
				// Projeto
				$rs_projects = UsefulController::getStripslashes ( $rs );
				// Licencas
				$licenses = UsefulController::paginatorToArray ( $ProjetHasLicense->fetchAll ( $company_id, $rs_projects->id ) );
				$rs_license = array ();
				if (count ( $licenses ) > 0) {
					foreach ( $licenses as $lc_key => $lc_item ) {
						if (! isset ( $rs_license [$lc_item->license_id] )) {
							$rs_license [$lc_item->license_id] = UsefulController::getStripslashes ( $lc_item );
						}
					}
				}
				// Outras Licencas
				$Paginator = $Licences->fetchAllActive ( $company_id );
				if ($Paginator->count () > 0) {
					$arr = iterator_to_array ( $Paginator->getCurrentItems () );
					foreach ( $arr as $i ) {
						if (! isset ( $rs_license [$i->id] )) {
							$rs_license [$i->id] = array (
									'license_id' => $i->id,
									'check_enabled' => false 
							);
						}
					}
				}
				
				$rs_projects ['licenses'] = $rs_license;
				
				// Familia
				$families = UsefulController::paginatorToArray ( $ProjectHasFamily->fetchAll ( $company_id, $rs_projects->id ) );
				if (count ( $families ) > 0) {
					foreach ( $families as $f_key => $f_item ) {
						$rs_families [$f_key] = UsefulController::getStripslashes ( $f_item );
						// Formatos
						$formats = UsefulController::paginatorToArray ( $FamilyHasFormats->fetchAll ( $company_id, $f_item->id, $rs_projects->id ) );
						$rs_formats = array ();
						if (count ( $formats ) > 0) {
							foreach ( $formats as $t_key => $t_item ) {
								$rs_formats [$t_key] = UsefulController::getStripslashes ( $t_item );
								// Fontes
								$files = UsefulController::paginatorToArray ( $FontStyles->fetchAll ( $company_id, $rs_projects->id, $f_item->id, $t_item->id, $t_item->license_formats_id ) );
								if (count ( $files )) {
									foreach ( $files as $fs_key => $fs_item ) {
										$rs_files [$fs_key] = UsefulController::getStripslashes ( $fs_item );
									}
								} else {
									$rs_files = array ();
								}
								
								$rs_formats [$t_key] ['files'] = $rs_files;
							}
						}
						$rs_families [$f_key] ['formats'] = $rs_formats;
					}
				}
				
				$outcome = $status = true;
				$data = array (
						'project' => $rs_projects,
						'families' => $rs_families 
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
			$Projects = new \Shop\Controller\ProjectsController ( $this->getServiceLocator () );
			$rs = $Projects->removed ( $id, $company_id );
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

