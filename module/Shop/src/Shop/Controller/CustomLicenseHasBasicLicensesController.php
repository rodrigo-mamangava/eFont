<?php

namespace Shop\Controller;

/**
 * Relacionamento entre licencas custom e licencas basicas
 * @author Claudio
 *
 */
class CustomLicenseHasBasicLicensesController extends \Useful\Controller\ControlController
{
    /**
     * Salva o item
     *
     * @param $license_custom_id
     * @param $license_basic_id
     * @return mixed
     */
	public function save ( $license_custom_id, $license_basic_id ){
        $dbTable = $this->getDbTable ( '\Shop\Model\CustomLicenseHasBasicLicensesTable' );
		return  $dbTable->save( $license_custom_id, $license_basic_id );
	}

    /**
     * Retorna todos os itens de uma licenca custom
     *
     * @param $license_custom_id
     * @return mixed
     */
	public function fetchAll ( $license_custom_id ){
        $dbTable = $this->getDbTable ( '\Shop\Model\CustomLicenseHasBasicLicensesTable' );
		return  $dbTable->fetchAll( $license_custom_id );
	}

    /**
     * Retorna todos os itens de relacionamento de uma companhia
     *
     * @param $company_id
     * @return mixed
     */
    public function fetchAllByCompanyId( $company_id ) {
        $dbTable = $this->getDbTable ( '\Shop\Model\CustomLicenseHasBasicLicensesTable' );
        return  $dbTable->fetchAllByCompanyId( $company_id );
    }

    /**
     * Remove pelo id da licenca custom
     *
     * @param $license_custom_id
     * @return mixed
     */
	public function removeByCustomLicense ( $license_custom_id ) {
        $dbTable = $this->getDbTable ( '\Shop\Model\CustomLicenseHasBasicLicensesTable' );
		return  $dbTable->removeByCustomLicense( $license_custom_id );
	}
}