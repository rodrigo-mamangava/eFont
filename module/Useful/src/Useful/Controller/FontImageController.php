<?php

namespace Useful\Controller;

use \Intervention\Image\ImageManagerStatic as Image;

/**
 * Criando imagens e Banners
 *
 * @author Claudio
 */
class FontImageController {
	/**
	 * Gera um banner com um text de uma font
	 * 
	 * @param unknown $font_path        	
	 * @param unknown $text        	
	 */
	public static function banner($font_path, $text) {
		$path = dirname ( $font_path ) . '/';
		$filename = $path . uniqid () . '.png';
		try {
			
			$image = new \PHPixie\Image ();
			$img = $image->create ( 700, 100 );
			$img->text ( $text, 72, $font_path, 10, 70, 0x000000, 1 );
			$img->save ( $filename );
			// // create a new empty image resource with transparent background
			// $img = Image::make ( $filename);
			// // write text
			// $img->text ( $text, 350, 50, function ($font) {
			// global $font_path;
			// $font->file ( $font_path);
			// $font->size ( 72 );
			// $font->align ( 'center' );
			// $font->valign ( 'center' );
			// } );
			// $img->save ( $filename );
			// var_dump($filename);
		} catch ( \Exception $e ) {
			// var_dump ( $e->getMessage () );
			return false;
		}
		
		return $filename;
	}
}