<?php

namespace Shop\Controller;

/**
 * Fontes e Arquivos em um Projeto
 * 
 * @author Claudio
 */
class FontFilesController extends \Useful\Controller\ControlController {
	/**
	 * Salva/Atualiza
	 * @param unknown $id
	 * @param unknown $uploadkey
	 * @param unknown $font_name
	 * @param unknown $font_id
	 * @param unknown $font_subfamily
	 * @param unknown $font_family
	 * @param unknown $font_copyright
	 * @param unknown $font_file
	 * @param unknown $font_path
	 * @param unknown $company_id
	 * @param unknown $user_id
	 * @param unknown $linked
	 * @param unknown $formats_id
	 * @param unknown $family_id
	 * @param unknown $project_id
	 */
	public function save($id, $uploadkey, $font_name, $font_id, $font_subfamily, $font_family, $font_copyright, $font_file, $font_path, $company_id, $user_id, $linked, $formats_id) {
		return $this->getDbTable ( '\Shop\Model\FontFilesTable' )->save ($id, $uploadkey, $font_name, $font_id, $font_subfamily, $font_family, $font_copyright, $font_file, $font_path, $company_id, $user_id, $linked, $formats_id);
	}
	/**
	 * Retorna todos os itens
	 * 
	 * @param unknown $company_id        	
	 * @param unknown $project_id        	
	 */
	public function fetchAll($company_id, $project_id) {
		return $this->getDbTable ( '\Shop\Model\FontFilesTable' )->fetchAll ( $company_id, $project_id );
	}
	/**
	 * Atualizando itens 
	 * @param unknown $id
	 * @param unknown $company_id
	 * @param unknown $user_id
	 * @param unknown $data
	 */
	public function updated($id, $company_id, $user_id, $data){
		return $this->getDbTable ( '\Shop\Model\FontFilesTable' )->updated($id, $company_id, $user_id, $data);
	}
	/**
	 * Sincronizando/Atualizando pelo font styles
	 * @param unknown $font_styles_id
	 * @param unknown $company_id
	 * @param unknown $user_id
	 * @param unknown $data
	 */
	public function synchronize($font_styles_id, $company_id, $user_id, $data){
		return $this->getDbTable ( '\Shop\Model\FontFilesTable' )->synchronize($font_styles_id, $company_id, $user_id, $data);
	}
	/**
	 * Limpando pelas chaves principais
	 * 
	 * @param unknown $company_id        	
	 * @param unknown $project_id        	
	 */
	public function cleanup($company_id, $project_id) {
		return $this->getDbTable ( '\Shop\Model\FontFilesTable' )->cleanup ( $company_id, $project_id );
	}
}