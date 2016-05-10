<?php

namespace Shop\Controller;

/**
 * Gerando Arquivo
 *
 * @author Claudio
 */
class CheckoutController extends \Useful\Controller\ControlController {
	/**
	 * Zipando arquivo
	 *
	 * @param unknown $yourcart        	
	 */
	public function compress($yourorder, $project_id, $license_id, $format, $collections, $styles) {
		// Controllers
		$License = new \Shop\Controller\LicensesController ( $this->getServiceLocator () );
		$FontFiles = new \Shop\Controller\FontFilesController ( $this->getServiceLocator () );
		
		$file_list = array ();
		// Arquivo da Licenca
		$rs_license = $License->find ( $license_id, null );
		if ($rs_license) {
			$file = isset ( $rs_license ['media_url'] ) ? $rs_license ['media_url'] : null;
			array_push ( $file_list, $file );
		}
		// Arquivo de Collections
		if (count ( $collections ) > 0) {
			foreach ( $collections as $c_key => $c_item ) {
				
				$Paginator = $FontFiles->fetchAll ( null, $project_id, $c_item );
				if ($Paginator->count () > 0) {
					$rs = iterator_to_array ( $Paginator->getCurrentItems () );
					foreach ( $rs as $font ) {
						array_push ( $file_list, $font->font_path );
					}
				}
			}
		}
		// Styles
		if (count ( $styles ) > 0) {
			foreach ( $styles as $s_key => $s_item ) {
				$Paginator = $FontFiles->fetchAll ( null, $project_id, null, $s_item );
				if ($Paginator->count () > 0) {
					$rs = iterator_to_array ( $Paginator->getCurrentItems () );
					foreach ( $rs as $font ) {
						array_push ( $file_list, $font->font_path );
					}
				}
			}
		}
		
		$compresskey = uniqid('', true);  
		if (count ( $file_list ) > 0) {
			return \Useful\Controller\CompressController::getZipArchive($compresskey, $file_list);
		}
		return false;
	}
}