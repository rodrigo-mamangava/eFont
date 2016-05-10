<?php

namespace Useful\Controller;

/**
 * Compressor de arquivo em Zips
 *
 * @author Claudio
 *        
 */
class CompressController {
	/**
	 * Comprime um arquivo e reforna sua URL para download
	 *
	 * @param unknown $file_list        	
	 */
	public static function getZipArchive($filename, $file_list) {
		// Path
		$folder = 'data/tmp/' . $filename;
		$outputfile = $folder . '/' . $filename . '.zip';
		$files = array ();
		
		if (file_exists ( $folder )) {
			return $outputfile;
		} else { // no file exists with this name
		         // Geramos um novo arquivo
			mkdir ( $folder );
			// Copiando arquivos
			foreach ( $file_list as $file ) {
				if (strlen ( $file ) > 0) {
					if (! filter_var ( $file, FILTER_VALIDATE_URL ) === false) {
						$name = basename ( $file );
						$download = $folder . $name;
						file_put_contents ( $download, fopen ( $file, 'r' ) );
						$file = $download;
					}
					
					if (file_exists ( $file )) {
						$files [] = $file;
					}
				}
			}
			// Gerou arquivos?
			if (count ( $files ) > 0) {
				// Copy files to path
				foreach ( $files as $item ) {
					$dest = $folder . '/' . basename ( $item );
					copy ( $item, $dest );
				}
				// Zipando
				$filter = new \Zend\Filter\Compress ( array (
						'adapter' => 'Zip',
						'options' => array (
								'archive' => $outputfile 
						) 
				) );
				
				$compress = $filter->filter ( $folder );
				return $compress;
			}
		}
	}
}