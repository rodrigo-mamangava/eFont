<?php

namespace Shop\Controller;

/**
 * Relacionamento entre licencas e formatos
 * @author Claudio
 *
 */
class LicenseHasFormatsController extends \Useful\Controller\ControlController
{
	/**
	 * Salva/Atualiza o item
	 * 
	 * @param unknown $id
	 * @param unknown $license_id
	 * @param unknown $license_formats_id
	 * @param unknown $company_id
	 * @param unknown $user_id
	 * @param unknown $parameters
	 * @param unknown $multiplier
	 * @param unknown $sequence
	 */
	public function save($id, $license_id, $license_formats_id, $company_id, $user_id, $parameters, $multiplier, $sequence){
		return  $this->getDbTable ( '\Shop\Model\LicenseHasFormatsTable' )->save($id, $license_id, $license_formats_id, $company_id, $user_id, $parameters, $multiplier, $sequence);
	}
	/**
	 * Retorna todos os itens
	 * @param unknown $license_id
	 * @param unknown $company_id
	 */
	public function fetchAll($license_id, $company_id){
		return  $this->getDbTable ( '\Shop\Model\LicenseHasFormatsTable' )->fetchAll($license_id, $company_id);
	}
	
	/**
	 * Retorna todos os itens pre formatado para a loja
	 * @param unknown $license_id
	 */
	public function fetchAllShop($license_id){
		return  $this->getDbTable ( '\Shop\Model\LicenseHasFormatsTable' )->fetchAllShop($license_id);
	}
	
	/**
	 * Remove pelo id da licenca e empresa
	 * @param unknown $license_id
	 * @param unknown $company_id
	 */
	public function removeByLicense($license_id, $company_id){
		return  $this->getDbTable ( '\Shop\Model\LicenseHasFormatsTable' )->removeByLicense($license_id, $company_id);
	}
}