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
	public function save($id, $name, $company_id, $user_id){
		return $this->getDbTable ( '\Shop\Model\ProjectsTable' )->save($id, $name, $company_id, $user_id);
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
	 * Removendo
	 * @param unknown $id
	 * @param unknown $company_id
	 */
	public function removed ( $id, $company_id ){
		return $this->getDbTable ( '\Shop\Model\ProjectsTable' )->removed ( $id, $company_id );
	}
}