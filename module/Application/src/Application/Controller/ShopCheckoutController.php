<?php

namespace Application\Controller;

use \Validador\Controller\ValidadorController;
use \Useful\Controller\UsefulController;

/**
 * Finalizando a compra
 *
 * @author Claudio
 */
class ShopCheckoutController extends ApplicationController {
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
	 * Checkout
	 */
	public function checkoutAction() {
		// Default
		$data = $this->translate ( "Unknown Error, try again, please." );
		$outcome = $status = false;
		if ($this->getRequest ()->isPost ()) {
			try {
				// PARAMS
				$post = $this->postJsonp ();
				$alright_checkout = false;
				$alright_project = false;
				$alright_families = false;
				$alright_styles = false;
				// checkout
				$checkout = isset ( $post ['checkout'] ) ? $post ['checkout'] : null;
				$payment = isset ( $checkout ['payment'] ) ? $checkout ['payment'] : 1;
				$tnc = isset ( $checkout ['tnc'] ) ? $checkout ['tnc'] : false;
				// Cart
				$yourcart = isset ( $post ['cart'] ) ? $post ['cart'] : null;
				$yourcart_number = is_array ( $yourcart ) ? count ( $yourcart ) : 0;
				$yourcart_data = array ();
				// var_dump ( $yourcart );
				if ($yourcart_number > 0) {
					foreach ( $yourcart as $yc_key => $yc_item ) {
						$item = array ();
						$item ['project_id'] = isset ( $yc_item ['form'] ['id'] ) ? $yc_item ['form'] ['id'] : null;
						$item ['project_ddig'] = isset ( $yc_item ['form'] ['ddig'] ) ? $yc_item ['form'] ['ddig'] : null;
						$item ['project_name'] = isset ( $yc_item ['form'] ['name'] ) ? $yc_item ['form'] ['name'] : null;
						$item ['project_cart'] = isset ( $yc_item ['cart'] ) ? $yc_item ['cart'] : null;
						/**
						 * 2016-05-09 : TRUE POR ENQUANTO, DEPOIS VALIDAR
						 */
						$alright_project = true;
						/**
						 */
						
						$item ['license_id'] = $license_id = isset ( $yc_item ['license'] ) ? $yc_item ['license'] : null;
						$item ['project_license'] = isset ( $yc_item ['licenses'] [$item ['license_id']]['name'] ) ? $yc_item ['licenses'] [$item ['license_id']]['name'] : '';
						
						$item ['format'] = isset ( $yc_item ['format'] [$license_id] ) ? $yc_item ['format'] [$license_id] : null;
						
						$styles = isset ( $yc_item ['styles'] ) ? $yc_item ['styles'] : array ();
						$styles_number = is_array ( $styles ) ? count ( $styles ) : 0;
						$styles_data = array ();
						
						$collection_data = array ();
						
						$families = isset ( $yc_item ['families'] ) ? $yc_item ['families'] : array ();
						$families_number = is_array ( $families ) ? count ( $families ) : 0;
						$families_data = array ();
						
						if ($families_number > 0 && $styles_number > 0) {
							$alright_families = true;
							
							foreach ( $families as $f_key => $f_item ) {
								$check_collection = isset ( $f_item ['check_collection'] ) ? $f_item ['check_collection'] : false;
								
								if ($check_collection == true) { // Todos os arquivos?
									$collection_data [] = $f_key;
									
									$alright_styles = true;
								} else { // Fontes individuais
									
									if (isset ( $styles [$f_key] )) {
										foreach ( $styles [$f_key] as $s_key => $s_item ) {
											$selected = isset ( $s_item ['selected'] ) ? $s_item ['selected'] : false;
											if ($selected == true) {
												$files = isset ( $s_item ['pricing'] ) ? $s_item ['pricing'] : array ();
												foreach ( $files as $f_s_key => $f_s_item ) {
													foreach ( $f_s_item as $f_f_key => $f_f_item ) {
														$styles_data [] = $f_f_key;
													}
													$alright_styles = true;
												}
											}
										}
									}
								}
							}
						}
						
						$item ['collections'] = $collection_data;
						$item ['styles'] = $styles_data;
						// Cart result
						$yourcart_data [$yc_key] = $item;
					}
				} else {
					throw new \Exception ( $this->translate ( 'Please, add one or more items.' ) );
				}
				// Tudo ok?
				if ($alright_families && $alright_project && $alright_styles) {
					$alright_checkout = true;
				}
				// Validacao
				if (! ValidadorController::isValidDigits ( $payment )) {
					throw new \Exception ( $this->translate ( 'Please, select a payment method.' ) );
				} elseif ($tnc != true && $tnc != 'true') {
					throw new \Exception ( $this->translate ( 'In order to use our services, you must agree to Shop\'s Terms of Service.' ) );
				} elseif ($alright_checkout == false) {
					throw new \Exception ( $this->translate ( 'We cannot validate all items in your cart, try again or contact our support.' ) );
				} else {
					// Class
					$CheckoutController = new \Shop\Controller\CheckoutController ( $this->getServiceLocator () );
					// Auxiliares
					$yourorder = uniqid ();
					$download = array ();
					$count = 0;
					// Gerando arquivos
					if (count ( $yourcart_data ) > 0) {
						foreach ( $yourcart_data as $c_key => $c_item ) {
							/**
							 * BEGIN ZIP
							 */
							$compress = $CheckoutController->compress ( $yourorder, $c_item ['project_id'], $c_item ['license_id'], $c_item ['format'], $c_item ['collections'], $c_item ['styles'] );
							if (file_exists ( $compress )) {
								$download [$count] ['download'] = \Cryptography\Controller\CryptController::encrypt ( $compress, true );
								$download [$count] ['completed'] = $c_item; 
								
								$status = true;
								$count++;
							}
						/**
						 * END ZIP
						 */
						}
						/**
						 * Resultado
						 */
						$outcome = $yourorder;
						$data = $download;
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
	 * Completando operacao
	 */
	public function completeAction() {
		return $this->viewModel->setTerminal ( true );
	}
}