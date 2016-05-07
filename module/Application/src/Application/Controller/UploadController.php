<?php

namespace Application\Controller;

/**
 * Upload de arquivos
 *
 * @author Claudio
 */
class UploadController extends ApplicationController {
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
	 * Upload para Amazon
	 */
	public function fileAction() {
		set_time_limit(180);
		// action body
		$Request = $this->getRequest ();
		$data = $this->translate ( "Unknown Error, try again, please." );
		$status = false;
		if ($Request->isPost ()) {
			try {
				if (is_uploaded_file ( $_FILES ['file'] ['tmp_name'] )) {
					// Preformatando
					$post = $Request->getPost ();
					$Upload = new \AWS\Controller\UploadController ( $this->getMyServiceLocator () );
					$data = $Upload->uploadDocs ( $_FILES, false );
					if (isset ( $data ['url'] )) {
						$ext = isset ( $data ['extension'] ) ? $data ['extension'] : null;
						if ($ext == 'zip') {
							$data ['short'] = $data ['url'];
						} else {
							$Short = new \Useful\Controller\ShortURLController ( $this->getServiceLocator () );
							$data ['short'] = $Short->shortener ( $data ['url'] );
						}
						$status = true;
					}
				} else {
					$data = $this->translate ( "Failure on file loading, try again.." );
				}
			} catch ( \Exception $e ) {
				$data = $e->getMessage ();
			}
		}
		// Response
		self::showResponse ( $status, $data, $status, true );
		exit ();
	}
}