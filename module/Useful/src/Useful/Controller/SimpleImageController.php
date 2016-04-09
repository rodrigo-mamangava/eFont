<?php
/**
 *
 * File: SimpleImage.php
 * Author: Simon Jarvis
 * Copyright: 2006 Simon Jarvis
 * Date: 08/11/06
 * Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details:
 * http://www.gnu.org/licenses/gpl.html
 *
 */
namespace Useful\Controller;

use Useful\Controller\ControlController;
use Validador\Controller\ValidadorController;

class SimpleImageController extends ControlController
{
	var $image;
	var $image_type;
	/**
	 * Carrega uma imagem
	 *
	 * @param unknown $filename
	 */
	function load($filename) {
		$image_info = getimagesize ( $filename );
		$this->image_type = $image_info [2];
		if ($this->image_type == IMAGETYPE_JPEG) {
			$this->image = imagecreatefromjpeg ( $filename );
		} elseif ($this->image_type == IMAGETYPE_GIF) {
			$this->image = imagecreatefromgif ( $filename );
		} elseif ($this->image_type == IMAGETYPE_PNG) {
			$this->image = imagecreatefrompng ( $filename );
		}
	}
	/**
	 * Salva/Cria a imagem
	 *
	 * @param unknown $filename
	 * @param string $image_type
	 * @param number $compression
	 * @param string $permissions
	 */
	function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null) {
		if ($image_type == IMAGETYPE_JPEG) {
			imagejpeg ( $this->image, $filename, $compression );
		} elseif ($image_type == IMAGETYPE_GIF) {
			imagegif ( $this->image, $filename );
		} elseif ($image_type == IMAGETYPE_PNG) {
			imagepng ( $this->image, $filename );
		}
		if ($permissions != null) {
			chmod ( $filename, $permissions );
		}
	}
	/**
	 * Destroi a imagem
	 */
	function destroy(){
		return imagedestroy($this->image);
	}
	/**
	 * Retorna a sava
	 *
	 * @param string $image_type
	 */
	function output($image_type = IMAGETYPE_JPEG) {
		if ($image_type == IMAGETYPE_JPEG) {
			imagejpeg ( $this->image );
		} elseif ($image_type == IMAGETYPE_GIF) {
			imagegif ( $this->image );
		} elseif ($image_type == IMAGETYPE_PNG) {
			imagepng ( $this->image );
		}
	}
	/**
	 * Retorna a largura da imagem
	 */
	function getWidth() {
		return imagesx ( $this->image );
	}
	/**
	 * Retorna a altura da imagem
	 */
	function getHeight() {
		return imagesy ( $this->image );
	}
	/**
	 * Retorna o tipo de imagem
	 */
	function getType() {
		if ($this->image_type == IMAGETYPE_JPEG) {
			return 'image/jpeg';
		} elseif ($this->image_type == IMAGETYPE_GIF) {
			return 'image/gif';
		} elseif ($this->image_type == IMAGETYPE_PNG) {
			return 'image/png';
		}
		return 'image/jpeg';
	}
	/**
	 * Retorna a imagem
	 */
	function getImage() {
		ob_start(); //Stdout --> buffer
		self::output();
	
		$stdout = ob_get_contents(); //store stdout in $stout
		ob_end_clean(); //clear buffer
	
		self::destroy(); //destroy img
	
		return $stdout;
	}
	/**
	 * Altera a altura
	 *
	 * @param unknown $height
	 */
	function resizeToHeight($height) {
		$ratio = $height / $this->getHeight ();
		$width = $this->getWidth () * $ratio;
		$this->resize ( $width, $height );
	}
	/**
	 * Altera a largura
	 *
	 * @param unknown $width
	 */
	function resizeToWidth($width) {
		$ratio = $width / $this->getWidth ();
		$height = $this->getheight () * $ratio;
		$this->resize ( $width, $height );
	}
	/**
	 * Escala da imagem
	 *
	 * @param unknown $scale
	 */
	function scale($scale) {
		$width = $this->getWidth () * $scale / 100;
		$height = $this->getheight () * $scale / 100;
		$this->resize ( $width, $height );
	}
	/**
	 * Redireciona a imagem
	 *
	 * @param unknown $width
	 * @param unknown $height
	 */
	function resize($width, $height) {
		$new_image = imagecreatetruecolor ( $width, $height );
		imagecopyresampled ( $new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth (), $this->getHeight () );
		$this->image = $new_image;
	}
	/**
	 * Procede com o upload do arquivo e conversao para o PostGRES
	 *
	 * @param unknown $FILE
	 * @throws Exception
	 * @return unknown
	 */
	public function uploadToBase64($FILE) {
		$allowedExts = array (
				"gif",
				"jpeg",
				"jpg",
				"png"
		);
		$temp = explode ( ".", $FILE ["file"] ["name"] );
		$extension = end ( $temp );
	
		if ((($FILE ["file"] ["type"] == "image/gif") || ($FILE ["file"] ["type"] == "image/jpeg") || ($FILE ["file"] ["type"] == "image/jpg") || ($FILE ["file"] ["type"] == "image/pjpeg") || ($FILE ["file"] ["type"] == "image/x-png") || ($FILE ["file"] ["type"] == "image/png"))) {
			if (($FILE ["file"] ["size"] < 1048576) && in_array ( $extension, $allowedExts )) {
				if ($FILE ["file"] ["error"] > 0) {
					throw new \Exception ( "Return Code: " . $FILE ["file"] ["error"] . "<br>" );
				} else {
					// header('Content-Type: image/jpeg');
					//$SimpleImage = new App_Image_SimpleImage ();
					self::load ( $FILE ["file"] ["tmp_name"] );
					self::resize ( 385, 317 );
					$data = self::getImage ();
					$type = self::getType ();
						
					//$bytea = pg_escape_bytea ( $data );//Otimizando, ja que nao vamos usar
					$bytea = null;
					$base64 = 'data:' . $type . ';base64,' . base64_encode ( $data );
					return array (
							'bytea' => $bytea,
							'base64' => $base64
					);
				}
			} else {
				throw new Exception ( "Arquivo maior do que o permitido" );
			}
		} else {
			throw new Exception ( "Invalid file" );
		}
	}
}

