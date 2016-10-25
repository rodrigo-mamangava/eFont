<?php

namespace Shop\Controller;

/**
 * Formatos dos arquivos para download e vinculo com as licencas
 * @author Claudio
 *
 */
class LicenseFormatsController extends \Useful\Controller\ControlController
{
    /**
     * Retorna todos os items
     *
     * @return mixed
     */
	public function fetchAll(){
		return  $this->getDbTable ( '\Shop\Model\LicenseFormatsTable' )->fetchAll();
	}

    /**
     * Retorna todos os items
     *
     * @return mixed
     */
    public function fetchAllWithoutDefault(){
        return  $this->getDbTable ( '\Shop\Model\LicenseFormatsTable' )->fetchAllWithoutDefault();
    }


}