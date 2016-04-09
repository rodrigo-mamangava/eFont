<?php

namespace AWS\Controller;

use Useful\Controller\ControlController;
use Zend\Math\Rand;

class UploadController extends ControlController {
	/**
	 * Salva o arquivo na S3 na pasta de conteudos
	 * @param unknown $FILES
	 * @param string $contests
	 * @throws \Exception
	 */
	public function uploadImgOrVideo($FILES, $contests = false) {
		if (! isset ( $FILES ['file'] ['tmp_name'] )) {
			throw new \Exception ( 'Invalid file temp.' );
		} else {
			// Validate the file type
			$fileTypes = array (
					'jpg',
					'jpeg',
					'gif',
					'png',
					'mp4'
			); // File extensions
			$fileParts = pathinfo ( $FILES ['file'] ['name'] );
				
			if (in_array ( $fileParts ['extension'], $fileTypes )) {
				// pegando as configuracoes
				$config = $this->getConfig ();
				// pegando a imagem para fazer upload
				$tempFile = $FILES ['file'] ['tmp_name'];
				// Setando o destino do arquivo dentro do bucket
				$filename = Rand::getString ( 32, 'abcdefghijklmnopqrstuvwxyz123456789', true ) . '.' . $fileParts ['extension'];
				$targetFile = $config ["AwsS3"] ["contents"] . $filename;
				$url = $config ["AwsS3"] ["url"] . $filename;
	
				if($contests === true){//Se for um concurso, sobrescreve
					$targetFile = $config ["AwsS3"] ["contests"] . $filename;
					$url = $config ["AwsS3"] ["contests_url"] . $filename;
				}
				// setando o nome do bucket
				$bucket = $config ["AwsS3"] ["bucket"];
				// Enviando a arquivo para o s3
				$s3 = new S3Controller ( $config ["AwsS3"] ["key"], $config ["AwsS3"] ["secret"] );
				// $s3->putBucket ( $bucket, S3Controller::ACL_PUBLIC_READ );
				if ($s3->putObjectFile ( $tempFile, $bucket, $targetFile, S3Controller::ACL_PUBLIC_READ )) {
					// Se chegar aqui é porque o upload foi um sucesso
					return array (
							'filename' => $filename,
							'url' => $url
					);
				} else {
					// Se chegar aqui é porque ocorreu um erro ao fazer o upload
					throw new \Exception ( 'Exceeded filesize limit.' );
				}
			} else {
				throw new \Exception ( 'Invalid file type.' );
			}
		}
		throw new \Exception ( 'Invalid upload file in the system, check permission administrator.' );
	}	
	/**
	 * Salva um documento de texto na S3
	 * @param unknown $FILES
	 * @param string $easy_name
	 * @throws \Exception
	 */
	public function uploadDocs($FILES, $easy_name = false) {
		if (! isset ( $FILES ['file'] ['tmp_name'] )) {
			throw new \Exception ( 'Invalid file temp.' );
		} else {
			$fileParts = pathinfo ( $FILES ['file'] ['name'] );
			
			// pegando as configuracoes
			$config = $this->getConfig ();
			// pegando a imagem para fazer upload
			$tempFile = $FILES ['file'] ['tmp_name'];
			// Setando o destino do arquivo dentro do bucket
			if ($easy_name == false) {
				$filename = Rand::getString ( 32, 'abcdefghijklmnopqrstuvwxyz123456789', true ) . '.' . $fileParts ['extension'];
			} else {
				$filename = str_replace ( array (
						'\s',
						' ',
						'\t' 
				), '-', $fileParts ['filename'] ) . '-' . date ( 'YmdHis' ) . '.' . $fileParts ['extension'];
			}
			$targetFile = $config ["AwsS3"] ["contents"] . $filename;
			// setando o nome do bucket
			$bucket = $config ["AwsS3"] ["bucket"];
			// Enviando a imagem para o s3
			$s3 = new S3Controller ( $config ["AwsS3"] ["key"], $config ["AwsS3"] ["secret"] );
			// $s3->putBucket ( $bucket, S3Controller::ACL_PUBLIC_READ );
			if ($s3->putObjectFile ( $tempFile, $bucket, $targetFile, S3Controller::ACL_PUBLIC_READ )) {
				// Se chegar aqui ï¿½ porque o upload foi um sucesso
				$url = $config ["AwsS3"] ["url"] . $filename;
				return array (
						'filename' => $filename,
						'url' => $url,
						'dirname' => str_replace ( 'thumb/', '', $url ) 
				);
			} else {
				// Se chegar aqui ï¿½ porque ocorreu um erro ao fazer o upload
				throw new \Exception ( 'Ocorreu um erro ao fazer o upload para a AWS' );
			}
		}
		throw new \Exception ( 'Invalid upload file in the system, check permission administrator.' );
	}
	/**
	 * Carrega um arquivo de imagem para a S3
	 * @param unknown $FILES
	 * @throws \Exception
	 */
	public function uploadFile($FILES) {
		if (! isset ( $FILES ['file'] ['tmp_name'] )) {
			throw new \Exception ( 'Invalid file temp.' );
		} else {
			
			// $targetPath = $_SERVER ['DOCUMENT_ROOT'] . $targetFolder;
			// $targetFile = rtrim ( $targetPath, '/' ) . '/' . $FILES ['file'] ['name'];
			
			// Validate the file type
			$fileTypes = array (
					'jpg',
					'jpeg',
					'gif',
					'png',
					'JPG',
					'JPEG',
					'GIF',
					'PNG' 
			); // File extensions
			$fileParts = pathinfo ( $FILES ['file'] ['name'] );
			
			if (in_array ( $fileParts ['extension'], $fileTypes )) {
				// pegando as configuracoes
				$config = $this->getConfig ();
				// pegando a imagem para fazer upload
				$tempFile = $FILES ['file'] ['tmp_name'];
				// Setando o destino do arquivo dentro do bucket
				$filename = Rand::getString ( 32, 'abcdefghijklmnopqrstuvwxyz123456789', true ) . '.' . $fileParts ['extension'];
				$targetFile = $config ["AwsS3"] ["contents"] . $filename;
				$thumb = $config ["AwsS3"] ["thumb"] . $filename;
				// setando o nome do bucket
				$bucket = $config ["AwsS3"] ["bucket"];
				// Gerando a miniatura e enviando para o S3
				//$s3Thumb = new S3ThumbController ( $config, $bucket );
				//$s3Thumb->putThumb ( $FILES ['file'], $thumb, 'public-read' );
				// Enviando a imagem para o s3
				$s3 = new S3Controller ( $config ["AwsS3"] ["key"], $config ["AwsS3"] ["secret"] );
				// $s3->putBucket ( $bucket, S3Controller::ACL_PUBLIC_READ );
				if ($s3->putObjectFile ( $tempFile, $bucket, $targetFile, S3Controller::ACL_PUBLIC_READ )) {
					// Se chegar aqui ï¿½ porque o upload foi um sucesso
					$url = $config ["AwsS3"] ["url"] . $filename;
					return array (
							'filename' => $filename,
							'url' => $url,
							'thumb' => $thumb,
							'uuid' => uniqid () 
					);
				} else {
					// Se chegar aqui ï¿½ porque ocorreu um erro ao fazer o upload
					throw new \Exception ( 'Ocorreu um erro ao fazer o upload para a AWS' );
				}
			} else {
				throw new \Exception ( 'Invalid file type.' );
			}
		}
		throw new \Exception ( 'Invalid upload file in the system, check permission administrator.' );
	}
	
	/**
	 * Upload com Engyte
	 *
	 * @param unknown $domain        	
	 * @param unknown $folder        	
	 * @param unknown $FILE        	
	 */
	public function egnyte($folder, $FILE, $method = 'upload') {
		// pegando as configuracoes
		$config = $this->getConfig ();
		$domain = $config ["Egnyte"] ["domain"];
		$oauthToken = $config ["Egnyte"] ["access_token"];
		$path = $config ["Egnyte"] ["path"];
		$subfolders = $config ["Egnyte"] ["add_folder"];
		
		try {
			if ($method == 'upload') {
				
				$folder = $path . $folder;
				if (is_uploaded_file ( $FILE ['file'] ['tmp_name'] )) {
					// get the file contents and name from the upload (where the name of the file input posted to the page is 'filedata')
					$fileBinaryContents = file_get_contents ( $FILE ['file'] ['tmp_name'] );
					// $fileName = $FILE ['file'] ['name'];
					$fileParts = pathinfo ( $FILE ['file'] ['name'] );
					$fileName = str_replace ( array (
							'\s',
							' ',
							'\t' 
					), '-', $fileParts ['filename'] ) . '-' . date ( 'YmdHis' ) . '.' . $fileParts ['extension'];
					// instantiate an Egnyte Client with the domain and oAuth token for the user with which the upload will be performed
					$egnyte = new \AWS\Controller\EgnyteClient ( $domain, $oauthToken );
					// perform the upload and get the response from the server
					$response = $egnyte->uploadFile ( $folder, $fileName, $fileBinaryContents );
					// errors are HTTP status codes 400 and greater
					if ($response->isError ()) {
						$data = ('The Egnyte denied access to your account. Make sure the credentials are correct.');
						$data .= ('Error uploading file.  Here\'s the detailed output from the API request:');
						$data .= $response->body;
						throw new \Exception ( $data );
					} else {
						$EgnyteResponse = $egnyte->getFileDetails ( $folder . $fileName )->getDecodedJSON ();
						return array (
								'filename' => $EgnyteResponse->path,
								'url' => $EgnyteResponse->path,
								'thumb' => $EgnyteResponse->path,
								'dirname' => $EgnyteResponse->path,
								'uuid' => uniqid () 
						);
					}
				} else {
					throw new \Exception ( "Failure on file loading, try again.." );
				}
			} elseif ($method == 'download') {
				$Egnyte = new \AWS\Controller\EgnyteClient ( $domain, $oauthToken );
				return $Egnyte->downloadFile ( $folder );
			} elseif ($method == 'create_folder') {
				// Criando pasta
				$Egnyte = new \AWS\Controller\EgnyteClient ( $domain, $oauthToken );
				$response = $Egnyte->createFolder ( $path, $folder );
				if ($response->isError () && $response->statusCode != 403) {
					throw new \Exception ( $response->body );
				} else {
					foreach ( $subfolders as $i ) {
						$Egnyte->createFolder ( $path . '/' . $folder . '/', $i );
					}
				}
			} else {
				throw new \Exception ( 'Opss!' );
			}
		} catch ( \Exception $e ) {
			throw new \Exception ( $e->getMessage () );
		}
		return false;
	}
	/**
	 * Save uma base64 na S3
	 * @param string $base64Image        	
	 */
	public function uploadBase64($base64Image) {
		define ( 'UPLOAD_DIR', sys_get_temp_dir () . '/' );
		$img = $base64Image;
		$img = str_replace ( array (
				'data:image/png;base64,',
				'data:image/jpg;base64,',
				'data:image/jpeg;base64,',
				'data:image/gif;base64,',
				' ',
				'\s',
				'\t',
				'\n' 
		), '', $img );
		
		$data = base64_decode ( $img );
		$filename = Rand::getString ( 32, 'abcdefghijklmnopqrstuvwxyz123456789', true ).uniqid ().'.png';
		$file = UPLOAD_DIR . $filename;
		$success = file_put_contents ( $file, $data );
		$result = $success ? $file : false;
		if ($result) { // Enviando S3
			$config = $this->getConfig ();
			$targetFile = $config ["AwsS3"] ["contents"] . $filename;
			$bucket = $config ["AwsS3"] ["bucket"];
			
			$s3 = new S3Controller ( $config ["AwsS3"] ["key"], $config ["AwsS3"] ["secret"] );
			if ($s3->putObjectFile ( $file, $bucket, $targetFile, S3Controller::ACL_PUBLIC_READ )) {
				// Se chegar aqui Ã© porque o upload foi um sucesso
				$url = $config ["AwsS3"] ["url"] . $filename;
				return array (
						'filename' => $filename,
						'url' => $url 
				);
			}
		}
		return false;
	}
}