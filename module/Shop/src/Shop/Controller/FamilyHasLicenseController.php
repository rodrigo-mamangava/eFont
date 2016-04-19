<?php

namespace Shop\Controller;

/**
 * Familias e Licencas em um Projeto
 * 
 * @author Claudio
 */
class FamilyHasLicenseController extends \Useful\Controller\ControlController {
	/**
	 * Salva/Atualiza
	 *
	 * @param unknown $id        	
	 * @param unknown $money_family        	
	 * @param unknown $money_weight        	
	 * @param unknown $check_family        	
	 * @param unknown $check_weight        	
	 * @param unknown $project_id        	
	 * @param unknown $family_id        	
	 * @param unknown $license_id        	
	 * @param unknown $company_id        	
	 * @param unknown $user_id        	
	 */
	public function save($id, $money_family, $money_weight, $check_family, $check_weight, $project_id, $family_id, $license_id, $company_id, $user_id) {
		return $this->getDbTable ( '\Shop\Model\FamilyHasLicenseTable' )->save ( $id, $money_family, $money_weight, $check_family, $check_weight, $project_id, $family_id, $license_id, $company_id, $user_id );
	}
	/**
	 * Retorna todas as familias
	 * @param unknown $company_id
	 * @param unknown $family_id
	 * @param unknown $project_id
	 */
	public function fetchAll ( $company_id, $family_id, $project_id ){
		return $this->getDbTable ( '\Shop\Model\FamilyHasLicenseTable' )->fetchAll ( $company_id, $family_id, $project_id );
	}
	
	/**
	 * Limpando pelas chaves principais
	 * @param unknown $company_id
	 * @param unknown $project_id
	 */
	public function cleanup($company_id, $project_id){
		return $this->getDbTable ( '\Shop\Model\FamilyHasLicenseTable' )->cleanup($company_id, $project_id);
	}	
}