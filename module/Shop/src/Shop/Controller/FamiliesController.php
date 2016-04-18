<?php

namespace Shop\Controller;

/**
 * Familias vinculada a um projeto
 * @author Claudio
 */
class FamiliesController extends \Useful\Controller\ControlController
{
	/**
	 * Salva /Atualiza
	 * @param unknown $id
	 * @param unknown $family_name
	 * @param unknown $project_id
	 * @param unknown $company_id
	 * @param unknown $user_id
	 */
	public function saved($id, $family_name, $project_id, $company_id, $user_id){
		return $this->getDbTable ( '\Shop\Model\FamiliesTable' )->saved($id, $family_name, $project_id, $company_id, $user_id);
	}
}