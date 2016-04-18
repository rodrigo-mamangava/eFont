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
}