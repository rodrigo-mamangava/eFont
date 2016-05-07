<?php
namespace Shop\Controller;
/**
 * Projetos e Licencas em um Projeto
 * @author Claudio
 */
class ProjectHasLicenseController extends \Useful\Controller\ControlController {
	/**
	 * Salva/Atualiza
	 * @param unknown $id
	 * @param unknown $money_family
	 * @param unknown $money_weight
	 * @param unknown $check_family
	 * @param unknown $check_weight
	 * @param unknown $license_id
	 * @param unknown $company_id
	 * @param unknown $user_id
	 * @param unknown $check_enabled
	 * @param unknown $project_id
	 * @param unknown $sequence
	 */
	public function save($id, $money_family, $money_weight, $check_family, $check_weight, $license_id, $company_id, $user_id, $check_enabled, $project_id, $sequence) {
		return $this->getDbTable ( '\Shop\Model\ProjectHasLicenseTable' )->save ($id, $money_family, $money_weight, $check_family, $check_weight, $license_id, $company_id, $user_id, $check_enabled, $project_id, $sequence);
	}
	/**
	 * Retorna todos os itens
	 * @param unknown $company_id
	 * @param unknown $project_id
	 */
	public function fetchAll ( $company_id, $project_id ){
		return $this->getDbTable ( '\Shop\Model\ProjectHasLicenseTable' )->fetchAll ( $company_id, $project_id );
	}
	/**
	 * Todos os items pre formatados
	 * @param unknown $project_id
	 */
	public function fetchAllShop($project_id){
		return $this->getDbTable ( '\Shop\Model\ProjectHasLicenseTable' )->fetchAllShop($project_id);
	}
	/**
	 * Limpando pelas chaves principais
	 * @param unknown $company_id
	 * @param unknown $project_id
	 */
	public function cleanup($company_id, $project_id){
		return $this->getDbTable ( '\Shop\Model\ProjectHasLicenseTable' )->cleanup($company_id, $project_id);
	}	
}