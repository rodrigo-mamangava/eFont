<?php

namespace Shop\Controller;

/**
 * Projetos
 * @author Claudio
 */
class ProjectsController extends \Useful\Controller\ControlController
{
	
	/**
	 * Salva /Atualiza
	 * @param unknown $id
	 * @param unknown $name
	 * @param unknown $company_id
	 * @param unknown $user_id
	 */
	public function save($id, $name, $company_id, $user_id, $project_ddig, $project_banner){
		return $this->getDbTable ( '\Shop\Model\ProjectsTable' )->save($id, $name, $company_id, $user_id, $project_ddig, $project_banner);
	}
	/**
	 * Retorna um item
	 * @param unknown $id
	 * @param unknown $company_id
	 */
	public function find ( $id, $company_id ){
		return $this->getDbTable ( '\Shop\Model\ProjectsTable' )->find ( $id, $company_id );
	}
	/**
	 * Consulta customizada
	 * @param unknown $search
	 * @param unknown $count
	 * @param unknown $offset
	 * @param unknown $company_id
	 */
	public function filter ( $search, $count, $offset, $company_id ){
		return $this->getDbTable ( '\Shop\Model\ProjectsTable' )->filter ( $search, $count, $offset, $company_id );
	}
	/**
	 * Atualizando um item
	 * @param unknown $id
	 * @param unknown $data
	 * @param unknown $company_id
	 */
	public function updated($id, $data, $company_id){
		return $this->getDbTable ( '\Shop\Model\ProjectsTable' )->updated($id, $data, $company_id); 
	}
	/**
	 * Removendo
	 * @param unknown $id
	 * @param unknown $company_id
	 */
	public function removed ( $id, $company_id ){
		return $this->getDbTable ( '\Shop\Model\ProjectsTable' )->removed ( $id, $company_id );
	}
}