<?php

namespace Application\Controller;

/**
 * Lista de produtos
 * @author Claudio
 */
class ShopProductListController extends ApplicationController
{
	/**
	 * {@inheritDoc}
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction()
	{
		return $this->viewModel->setTerminal ( true );
	}
	/**
	 * Lista de produtos para consulta no shopping
	 */
	public function searchAction(){
		// Default
		$data = $this->translate ( "Unknown Error, try again, please." );
		$outcome = $status = false;
		// System
		$user_id = $this->get_user_id ();
		// GET
		$Params = $this->params ();
		$count = $Params->fromQuery ( 'count', 10 );
		$offset = $Params->fromQuery ( 'offset', 0 );
		$search = $Params->fromQuery ( 'search', null );
		// Query
		$ProductsController = new \Shop\Controller\ProjectsController( $this->getMyServiceLocator () );
		$Family = new \Shop\Controller\FamiliesController($this->getServiceLocator());
		
		$Paginator = $ProductsController->filter ( $search, $count, $offset, null );
		
		if ($Paginator->count () > 0) {
			$arr = iterator_to_array ( $Paginator->getCurrentItems () );
			foreach($arr as $p_key=>$p_item){
				$arr[$p_key]['families'] =  \Useful\Controller\UsefulController::paginatorToArray($Family->fetchAll($p_item->company_id, $p_item->id));
				$arr[$p_key]['number_families'] = count($arr[$p_key]['families']);
			}
			
			$data = array ();
			$data ['items'] = $arr;
			$data ['total'] = $Paginator->getTotalItemCount ();
			$data ['count'] = $count;
			$data ['offset'] = $offset;
				
			$outcome = $status = true;
		}
		// Response
		self::showResponse ( $status, $data, $outcome, true );
		die ();
	}
}