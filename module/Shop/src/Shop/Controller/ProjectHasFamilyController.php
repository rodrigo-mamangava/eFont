<?php

namespace Shop\Controller;

/**
 * Projetos e Familia em um Projeto
 * 
 * @author Claudio
 */
class ProjectHasFamilyController extends \Useful\Controller\ControlController {
	/**
	 * Salva/Atualiza
	 * 
	 * @param unknown $id        	
	 * @param unknown $family_name        	
	 * @param unknown $money_family        	
	 * @param unknown $money_weight        	
	 * @param unknown $check_family        	
	 * @param unknown $check_weight        	
	 * @param unknown $sequence        	
	 * @param unknown $company_id        	
	 * @param unknown $user_id        	
	 * @param unknown $project_id        	
	 */
	public function save($id, $family_name, $money_family, $money_weight, $check_family, $check_weight, $sequence, $company_id, $user_id, $project_id) {
		return $this->getDbTable ( '\Shop\Model\ProjectHasFamilyTable' )->save ( $id, $family_name, $money_family, $money_weight, $check_family, $check_weight, $sequence, $company_id, $user_id, $project_id );
	}
	/**
	 * Retorna todos os itens
	 * 
	 * @param unknown $company_id        	
	 * @param unknown $project_id        	
	 */
	public function fetchAll($company_id, $project_id) {
		return $this->getDbTable ( '\Shop\Model\ProjectHasFamilyTable' )->fetchAll ( $company_id, $project_id );
	}
	/**
	 * Retorna todos os ids pre formatados
	 * 
	 * @param unknown $project_id        	
	 */
	public function fetchAllShop($project_id) {
		return $this->getDbTable ( '\Shop\Model\ProjectHasFamilyTable' )->fetchAllShop ( $project_id );
	}
	/**
	 * Limpando pelas chaves principais
	 * 
	 * @param unknown $company_id        	
	 * @param unknown $project_id        	
	 */
	public function cleanup($company_id, $project_id) {
		return $this->getDbTable ( '\Shop\Model\ProjectHasFamilyTable' )->cleanup ( $company_id, $project_id );
	}
}