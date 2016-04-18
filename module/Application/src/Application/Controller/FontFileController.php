<?php

namespace Application\Controller;

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
		// Default
		$data = $this->translate ( "Unknown Error, try again, please." );
		$outcome = $status = false;
		$files = array ();
		$count = 0;
		// System
		$user_id = $this->get_user_id ();
		$company_id = $this->get_company_id ();
		
		if ($this->getRequest ()->isPost ()) {
			try {
				// PARAMS
				$post = $this->postJsonp ();
				$uploaded = isset ( $post ['uploaded'] ) ? $post ['uploaded'] : null;
				/**
				 * Download file
				 */
				$uploadDirectory = 'data/tmp/';
				$destination = uniqid ();
				$folder = $uploadDirectory . $destination;
				$outputfile = $uploadDirectory . $destination . '.zip';
				file_put_contents ( $outputfile, fopen ( $uploaded, 'r' ) );
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
						$FontInfo = new \Useful\Controller\FontFileController ();
						
						while ( false !== ($entry = readdir ( $handle )) ) {
							if ($entry != "." && $entry != "..") {
								$font ['font_file'] = $entry;
								$font ['font_path'] = $folder . '/' . $entry;
								// Infos
								$FontInfo->setFontFile ( $font ['font_path'] );
								$font ['font_name'] = $FontInfo->getFullFontName ();
								$font ['font_id'] = $FontInfo->getFontId ();
								$font ['font_subfamily'] = $FontInfo->getFontSubFamily ();
								$font ['font_family'] = $FontInfo->getFontFamily ();
								$font ['font_copyright'] = $FontInfo->getCopyright ();
								
								$files [] = $font;
								$count++;
							}
						}
						closedir ( $handle );
						// Contem registros
						if (count ( $files ) > 0) {
							$data = array (
									'files' => $files,
									'total' => $count 
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