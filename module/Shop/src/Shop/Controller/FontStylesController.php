<?php

namespace Shop\Controller;

/**
 * Fontes e Styles em um Projeto
 *
 * @author Claudio
 */
class FontStylesController extends \Useful\Controller\ControlController {
	/**
	 * Salva/Atualiza
	 * 
	 * @param unknown $id        	
	 * @param unknown $name        	
	 * @param unknown $user_id        	
	 * @param unknown $company_id        	
	 * @param unknown $uploadkey        	
	 * @param unknown $family_id        	
	 * @param unknown $formats_id        	
	 * @param unknown $project_id        	
	 * @param unknown $linked        	
	 */
	public function save($id, $name, $user_id, $company_id, $uploadkey, $family_id, $formats_id, $project_id, $linked) {
		return $this->getDbTable ( '\Shop\Model\FontStylesTable' )->save ( $id, $name, $user_id, $company_id, $uploadkey, $family_id, $formats_id, $project_id, $linked );
	}
	/**
	 * Atualizando itens
	 * 
	 * @param unknown $id        	
	 * @param unknown $company_id        	
	 * @param unknown $user_id        	
	 * @param unknown $data        	
	 */
	public function updated($id, $company_id, $user_id, $data) {
		return $this->getDbTable ( '\Shop\Model\FontStylesTable' )->updated ( $id, $company_id, $user_id, $data );
	}
	/**
	 * Retorna todos os itens
	 * @param unknown $company_id
	 * @param unknown $project_id
	 * @param unknown $family_id
	 * @param unknown $family_has_formats_id
	 * @param unknown $formats_id
	 */
	public function fetchAll($company_id, $project_id, $family_id, $family_has_formats_id, $formats_id) {
		return $this->getDbTable ( '\Shop\Model\FontStylesTable' )->fetchAll ($company_id, $project_id, $family_id, $family_has_formats_id, $formats_id);
	}
	/**
	 * Retorna todos os itens pre formatados
	 * @param unknown $company_id
	 * @param unknown $project_id
	 */
	public function fetchAllShop($company_id, $project_id) {
		return $this->getDbTable ( '\Shop\Model\FontStylesTable' )->fetchAllShop ($company_id, $project_id);
	}
	/**
	 * Limpando pelas chaves principais
	 *
	 * @param unknown $company_id        	
	 * @param unknown $project_id        	
	 */
	public function cleanup($company_id, $project_id) {
		return $this->getDbTable ( '\Shop\Model\FontStylesTable' )->cleanup ( $company_id, $project_id );
	}
}