<?php

namespace Application\Controller;

/**
 * Lista de produtos
 * 
 * @author Claudio
 */
class ShopProductListController extends ApplicationController {
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
	 * Lista de produtos para consulta no shopping
	 */
	public function searchAction() {
		// Default
		$data = $this->translate ( "Unknown Error, try again, please." );
		$outcome = $status = false;
		// GET
		$Params = $this->params ();
		$count = $Params->fromQuery ( 'count', 10 );
		$offset = $Params->fromQuery ( 'offset', 0 );
		$search = $Params->fromQuery ( 'search', null );
		// Query
		$ProductsController = new \Shop\Controller\ProjectsController ( $this->getMyServiceLocator () );
		$Paginator = $ProductsController->filter ( $search, $count, $offset, null );
		if ($Paginator->count () > 0) {
			$rs = iterator_to_array ( $Paginator->getCurrentItems () );
			
			foreach ( $rs as $key => $item ) {
				if (strlen ( $item ['ddig'] ) > 5) {
					$item ['ddig'] = \Cryptography\Controller\CryptController::encrypt ( $item ['ddig'], true );
				}else{
					$item ['ddig'] = \Cryptography\Controller\CryptController::encrypt ( 'data/tmp/mplus-1c-medium.ttf', true );
				}
				$rs [$key] = $item;
			}
			$data = array ();
			$data ['items'] = $rs;
			$data ['total'] = $Paginator->getTotalItemCount ();
			$data ['count'] = $count;
			$data ['offset'] = $offset;
			
			$outcome = $status = true;
		}
		// // Response
		self::showResponse ( $status, $data, $outcome, true );
		die ();
	}
}