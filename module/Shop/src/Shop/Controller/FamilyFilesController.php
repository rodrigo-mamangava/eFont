<?php

namespace Shop\Controller;

/**
 * Relacao entre familia e arquivos
 * @author Claudio
 */
class FamilyFilesController extends \Useful\Controller\ControlController
{
	/**
	 * Salva /Atualiza
	 * @param unknown $id
	 * @param unknown $font_name
	 * @param unknown $font_id
	 * @param unknown $font_subfamily
	 * @param unknown $font_family
	 * @param unknown $font_copyright
	 * @param unknown $font_file
	 * @param unknown $font_path
	 * @param unknown $font_price
	 * @param unknown $check_price
	 * @param unknown $company_id
	 * @param unknown $user_id
	 * @param unknown $project_id
	 * @param unknown $family_id
	 * @param unknown $family_has_formats_id
	 * @param unknown $license_formats_id
	 */
	public function save($id, $font_name, $font_id, $font_subfamily, $font_family, $font_copyright, $font_file, $font_path, $font_price, $check_price, $company_id, $user_id, $project_id, $family_id, $family_has_formats_id, $license_formats_id){
		return $this->getDbTable ( '\Shop\Model\FamilyFilesTable' )->save($id, $font_name, $font_id, $font_subfamily, $font_family, $font_copyright, $font_file, $font_path, $font_price, $check_price, $company_id, $user_id, $project_id, $family_id, $family_has_formats_id, $license_formats_id);
	}
	/**
	 * Retorna todos os items
	 * @param unknown $company_id
	 * @param unknown $project_id
	 * @param unknown $family_id
	 * @param unknown $family_has_formats_id
	 * @param unknown $license_formats_id
	 */
	public function fetchAll($company_id, $project_id, $family_id, $family_has_formats_id, $license_formats_id){
		return $this->getDbTable ( '\Shop\Model\FamilyFilesTable' )->fetchAll($company_id, $project_id, $family_id, $family_has_formats_id, $license_formats_id);
	}
	/**
	 * Consulta por familia
	 * @param unknown $company_id
	 * @param unknown $project_id
	 * @param unknown $family_id
	 */
	public function fetchAllFamily($company_id, $project_id, $family_id, $license_formats_id){
		return $this->getDbTable ( '\Shop\Model\FamilyFilesTable' )->fetchAllFamily($company_id, $project_id, $family_id, $license_formats_id);
	}
	/**
	 * Retorna todos os itens customizado
	 * @param unknown $company_id
	 * @param unknown $project_id
	 * @param unknown $family_id
	 * @param unknown $family_has_formats_id
	 * @param unknown $license_formats_id
	 */
	public function fetchAllByProject($company_id, $project_id, $family_id, $family_has_formats_id, $license_formats_id){
		return $this->getDbTable ( '\Shop\Model\FamilyFilesTable' )->fetchAllByProject($company_id, $project_id, $family_id, $family_has_formats_id, $license_formats_id);
	}
	/**
	 * Limpando pelas chaves principais
	 * @param unknown $company_id
	 * @param unknown $project_id
	 */
	public function cleanup($company_id, $project_id){
		return $this->getDbTable ( '\Shop\Model\FamilyFilesTable' )->cleanup($company_id, $project_id);
	}
}