<?php

namespace Shop\Controller;

/**
 * Formatos vinculados a uma familia
 * @author Claudio
 *
 */
class FamilyHasFormatsController extends \Useful\Controller\ControlController
{
	/**
	 * Salva/Atualiza
	 * @param unknown $family_id
	 * @param unknown $license_formats_id
	 * @param unknown $media_url
	 * @param unknown $number_files
	 * @param unknown $collapsed
	 * @param unknown $company_id
	 * @param unknown $user_id
	 * @param unknown $project_id
	 */
	public function save($id, $family_id, $license_formats_id, $media_url, $number_files, $collapsed, $company_id, $user_id, $project_id){
		return $this->getDbTable ( '\Shop\Model\FamilyHasFormatsTable' )->save($id, $family_id, $license_formats_id, $media_url, $number_files, $collapsed, $company_id, $user_id, $project_id);
	}
	/**
	 * Retorna todos os itens
	 * @param unknown $company_id
	 * @param unknown $family_id
	 * @param unknown $project_id
	 */
	public function fetchAll ( $company_id, $family_id, $project_id ){
		return $this->getDbTable ( '\Shop\Model\FamilyHasFormatsTable' )->fetchAll ( $company_id, $family_id, $project_id );
	}
	/**
	 * Todos os itens por projeto
	 * @param unknown $company_id
	 * @param unknown $license_formats_id
	 * @param unknown $project_id
	 */
	public function fetchAllByProject($company_id, $license_formats_id, $project_id){
		return $this->getDbTable ( '\Shop\Model\FamilyHasFormatsTable' )->fetchAllByProject($company_id, $license_formats_id, $project_id);
	}
	/**
	 * Limpando pelas chaves principais
	 * @param unknown $company_id
	 * @param unknown $project_id
	 */
	public function cleanup($company_id, $project_id){
		return $this->getDbTable ( '\Shop\Model\FamilyHasFormatsTable' )->cleanup($company_id, $project_id);
	}
	
}
