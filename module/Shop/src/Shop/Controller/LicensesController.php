<?php

namespace Shop\Controller;

/**
 * Responsavel pelo gerenciamento das licencas
 * @author Claudio
 */
class LicensesController extends \Useful\Controller\ControlController
{
    /**
     * Salva/Atualiza um item
     *
     * @param $id
     * @param $name
     * @param $file
     * @param $company_id
     * @param $user_id
     * @param $check_trial
     * @param $check_desktop
     * @param $check_app
     * @param $check_web
     * @param $check_enabled
     * @param $currency_dollar
     * @param $currency_euro
     * @param $currency_libra
     * @param $currency_real
     * @param $check_fmt_otf
     * @param $check_fmt_ttf
     * @param $check_fmt_eot
     * @param $check_fmt_woff
     * @param $check_fmt_woff2
     * @param $check_fmt_trial
     * @param $check_custom
     * @return mixed
     */
	public function save(
	    $id, $name, $file, $company_id, $user_id, $check_trial,
        $check_desktop, $check_app, $check_web, $check_enabled,
        $currency_dollar, $currency_euro, $currency_libra, $currency_real,
        $check_fmt_otf, $check_fmt_ttf, $check_fmt_eot, $check_fmt_woff,
        $check_fmt_woff2, $check_fmt_trial, $check_custom ){
		return $this->getDbTable ( '\Shop\Model\LicensesTable' )->save(
		    $id, $name, $file, $company_id, $user_id, $check_trial,
            $check_desktop, $check_app, $check_web, $check_enabled,
            $currency_dollar, $currency_euro, $currency_libra, $currency_real,
            $check_fmt_otf, $check_fmt_ttf, $check_fmt_eot, $check_fmt_woff,
            $check_fmt_woff2, $check_fmt_trial, $check_custom
        );
	}

    /**
     * Busca customizada
     *
     * @param $search
     * @param $count
     * @param $offset
     * @param $company_id
     * @param int $check_custom
     * @return mixed
     */
	public function filter($search, $count, $offset, $company_id, $check_custom = 0 ){
		return $this->getDbTable ( '\Shop\Model\LicensesTable' )->filter($search, $count, $offset, $company_id, $check_custom );
	}

    /**
     * Retorna a lista de todas as licenas ativas
     *
     * @param $company_id
     * @return mixed
     */
	public function fetchAllActive($company_id){
		return $this->getDbTable ( '\Shop\Model\LicensesTable' )->fetchAllActive($company_id);
	}

    /**
     * Retorna a lista de todas as licenas ativas
     *
     * @param $company_id
     * @param $project_id
     * @return mixed
     */
	public function fetchAllToShop($company_id, $project_id ){
		return $this->getDbTable ( '\Shop\Model\LicensesTable' )->fetchAllToShop($company_id, $project_id);
	}

    /**
     * Busca por chaves
     *
     * @param $id
     * @param $company_id
     * @return mixed
     */
	public function find($id, $company_id){
		return $this->getDbTable ( '\Shop\Model\LicensesTable' )->find($id, $company_id);
	}

    /**
     * Remocao de um item
     *
     * @param $id
     * @param $company_id
     * @return mixed
     */
	public function removed( $id, $company_id){
		return $this->getDbTable ( '\Shop\Model\LicensesTable' )->removed( $id, $company_id);
	}
}