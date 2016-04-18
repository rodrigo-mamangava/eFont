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
	 */
	public function fetchAll(){
		return  $this->getDbTable ( '\Shop\Model\LicenseFormatsTable' )->fetchAll();
	}
}