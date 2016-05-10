<?php

namespace Application\Controller;

use Validador\Controller\ValidadorController;

/**
 * Obtem informacoes de uma fonte
 *
 * @author Claudio
 */
class FontFileController extends ApplicationController {
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
	 * Extra as informacoes de um arquivo zipado
	 */
	public function uncompressAction() {
		set_time_limit ( 180 );
		// Default
		$data = $this->translate ( "No files have been processed, please check if the file name is within the expected pattern." );
		$outcome = $status = false;
		$files = array ();
		$styles = array ();
		$count = 0;
		// System
		$user_id = $this->get_user_id ();
		$company_id = $this->get_company_id ();
		// Arquivos permitidos
		$fileTypes = array (
				'otf',
				'ttf',
				'eot',
				'woff',
				'svg',
				/*'html'*/ 
		); // File extensions
		$ddigTypes = array (
				'ttf' 
		);
		
		if ($this->getRequest ()->isPost ()) {
			try {
				// PARAMS
				$post = $this->postJsonp ();
				$uploaded = isset ( $post ['uploaded'] ) ? $post ['uploaded'] : null;
				$price = isset ( $post ['price'] ) ? $post ['price'] : 0;
				$format = isset ( $post ['format_id'] ) ? $post ['format_id'] : 0;
				/**
				 * Download file
				 */
				$uploadDirectory = 'data/tmp/';
				$destination = uniqid ();
				$folder = $uploadDirectory . $destination;
				$outputfile = $uploadDirectory . $destination . '.zip';
				file_put_contents ( $outputfile, fopen ( $uploaded, 'r' ) );
				$default = 'data/tmp/mplus-1c-medium.ttf';
				$ok_ddig = false;
				/**
				 * Descompactando o arquivo
				 */
				$decompress = false;
				$zip = new \ZipArchive ();
				if ($zip->open ( $outputfile ) === TRUE) {
					$zip->extractTo ( $folder );
					$zip->close ();
					$decompress = true;
					
					// Font Info
					if ($handle = opendir ( $folder )) {
						$FontFile = new \Shop\Controller\FontFilesController ( $this->getServiceLocator () );
						
						while ( false !== ($entry = readdir ( $handle )) ) {
							if ($entry != "." && $entry != "..") {
								
								$path = $folder . '/' . $entry;
								$path_parts = pathinfo ( $path );
								
								$filename = $path_parts ['filename'];
								$ext = $path_parts ['extension'];
								
								if (in_array ( $ext, $fileTypes )) {
									// Infos
									$pieces = explode ( '-', $filename );
									$font_family = isset ( $pieces [0] ) ? $pieces [0] : null;
									$font_subfamily = isset ( $pieces [1] ) ? $pieces [1] : null;
									
									if (ValidadorController::isValidNotEmpty ( $font_family ) && ValidadorController::isValidNotEmpty ( $font_subfamily )) {
										$font_family = ltrim ( preg_replace ( '/[A-Z]/', ' $0', $font_family ) );
										$font_subfamily = ltrim ( preg_replace ( '/[A-Z]/', ' $0', $font_subfamily ) );
										
										$font ['font_name'] = $filename;
										$font ['font_id'] = '';
										$font ['font_subfamily'] = $font_subfamily;
										$font ['font_family'] = $font_family;
										$font ['font_copyright'] = $this->getUserSession ()->getEmail ();
									} else {
										continue;
									}
									
									$font ['font_file'] = $entry;
									$font ['font_path'] = $path;
									$font ['font_folder'] = $folder;
									$font ['uploadkey'] = $destination;
									
									$font ['company_id'] = $company_id;
									$font ['user_id'] = $user_id;
									$font ['family_id'] = null;
									$font ['formats_id'] = $format;
									$font ['project_id'] = 0;
									$font ['linked'] = 0;
									$id = $font ['id'] = $FontFile->save ( null, $font ['uploadkey'], $font ['font_name'], $font ['font_id'], $font ['font_subfamily'], $font ['font_family'], $font ['font_copyright'], $font ['font_file'], $font ['font_path'], $font ['company_id'], $font ['user_id'], $font ['linked'], $font ['formats_id'] );
									if ($id) {
										$files [$font_family . ' ' . $font_subfamily] [$id] = $font;
										
										if ($ok_ddig == false && in_array ( $ext, $ddigTypes )) {
											$default = $path;
											$ok_ddig = true;
										}
										
										$count ++;
									}
								}
							}
						}
						closedir ( $handle );
						// Contem registros
						if (count ( $files ) > 0) {
							// Styles
							$FontStyles = new \Shop\Controller\FontStylesController ( $this->getServiceLocator () );
							foreach ( $files as $s_key => $s_item ) {
								$id = $FontStyles->save ( null, $s_key, $user_id, $company_id, $destination, 0, $format, 0, 0 );
								if ($id) {
									foreach ( $s_item as $f_key => $f_item ) {
										$FontFile->updated ( $f_item ['id'], $company_id, $user_id, array (
												'linked' => 1,
												'font_styles_id' => $id 
										) );
									}
									// Resultado
									$styles [$id] = array (
											'id' => $id,
											'font_file' => $s_key,
											'font_subfamily' => $s_key,
											'check_price' => false,
											'font_price' => 0.00,
											'uploadkey' => $destination 
									);
								}
							}
							
							$data = array (
									'files' => $styles,
									'total' => $count,
									'ddig' => $default 
							);
							$status = $outcome = true;
						}
					}
				}
				// Output
				@unlink ( $outputfile );
				@unlink ( $folder );
			} catch ( \Exception $e ) {
				$data = $e->getMessage ();
			}
		}
		// Response
		self::showResponse ( $status, $data, $outcome, true );
		die ();
	}
}