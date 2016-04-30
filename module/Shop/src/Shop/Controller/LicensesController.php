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
	 * @param unknown $id
	 * @param unknown $name
	 * @param unknown $file
	 * @param unknown $company_id
	 * @param unknown $user_id
	 * @param unknown $check_trial
	 * @param unknown $check_desktop
	 * @param unknown $check_app
	 * @param unknown $check_web
	 * @param unknown $check_enabled
	 * @param unknown $currency_dollar
	 * @param unknown $currency_euro
	 * @param unknown $currency_libra
	 * @param unknown $currency_real
	 */
	public function save($id, $name, $file, $company_id, $user_id, $check_trial, $check_desktop, $check_app, $check_web, $check_enabled, $currency_dollar, $currency_euro, $currency_libra, $currency_real){
		return $this->getDbTable ( '\Shop\Model\LicensesTable' )->save($id, $name, $file, $company_id, $user_id, $check_trial, $check_desktop, $check_app, $check_web, $check_enabled, $currency_dollar, $currency_euro, $currency_libra, $currency_real); 
	}
	/**
	 * Busca customizada
	 * @param unknown $search
	 * @param unknown $count
	 * @param unknown $offset
	 * @param unknown $company_id
	 */
	public function filter($search, $count, $offset, $company_id ){
		return $this->getDbTable ( '\Shop\Model\LicensesTable' )->filter($search, $count, $offset, $company_id );
	}
	/**
	 * Retorna a lista de todas as licenas ativas
	 * @param unknown $company_id
	 */
	public function fetchAllActive($company_id){
		return $this->getDbTable ( '\Shop\Model\LicensesTable' )->fetchAllActive($company_id);
	}
	/**
	 * Retorna a lista de todas as licenas ativas
	 * @param unknown $company_id
	 */
	public function fetchAllToShop($company_id, $project_id ){
		return $this->getDbTable ( '\Shop\Model\LicensesTable' )->fetchAllToShop($company_id, $project_id);
	}
	/**
	 * Busca por chaves
	 * @param unknown $id
	 * @param unknown $company_id
	 */
	public function find($id, $company_id){
		return $this->getDbTable ( '\Shop\Model\LicensesTable' )->find($id, $company_id);
	}
	/**
	 * Remocao de um item
	 * @param unknown $id
	 * @param unknown $company_id
	 */
	public function removed( $id, $company_id){
		return $this->getDbTable ( '\Shop\Model\LicensesTable' )->removed( $id, $company_id);
	}
}