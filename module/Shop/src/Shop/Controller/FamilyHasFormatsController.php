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
}
