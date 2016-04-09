<?php

namespace Shop\Controller;

/**
 * Responsavel pelo gerenciamento das empresas
 * @author Claudio
 */
class CompanyController extends \Useful\Controller\ControlController {
	/**
	 * Salva/Atualiza um item
	 * @param unknown $id
	 * @param unknown $name
	 * @param unknown $phone
	 * @param unknown $email
	 * @param unknown $contact
	 * @param unknown $address
	 * @param unknown $address_number
	 * @param unknown $address_city
	 * @param unknown $address_state
	 * @param unknown $address_country
	 * @param unknown $address_postal_code
	 * @param unknown $map_lat
	 * @param unknown $map_lng
	 * @param unknown $status
	 * @param unknown $company_id
	 */
	public function save($id, $name, $phone, $email, $contact, $address, $address_number, $address_city, $address_state, $address_country, $address_postal_code, $map_lat = '', $map_lng = '', $status = 0, $company_id = null) {
		return $this->getDbTable ( '\Shop\Model\CompanyTable' )->save($id, $name, $phone, $email, $contact, $address, $address_number, $address_city, $address_state, $address_country, $address_postal_code, $map_lat, $map_lng, $status, $company_id = null);
	}
	/**
	 * Busca pelo chave principal
	 * @param unknown $id
	 * @param unknown $company_id
	 */
	public function find($id, $company_id = null){
		return $this->getDbTable ( '\Shop\Model\CompanyTable' )->find($id, $company_id);
	}
	/**
	 * Consulta customizada
	 * @param unknown $search
	 * @param unknown $count
	 * @param unknown $offset
	 * @param unknown $company_id
	 */
	public function filter ( $search, $count, $offset, $company_id){
		return $this->getDbTable ( '\Shop\Model\CompanyTable' )->filter( $search, $count, $offset, $company_id);
	}
	/**
	 * Retorna todos os itens
	 */
	public function fetchAll(){
		return $this->getDbTable ( '\Shop\Model\CompanyTable' )->fetchAll();
	}
	/**
	 * Atualizacao individual
	 * @param unknown $id
	 * @param unknown $data
	 * @param unknown $company_id
	 */
	public function updated($id, $data, $company_id){
		return $this->getDbTable ( '\Shop\Model\CompanyTable' )->updated($id, $data, $company_id);
	}
	/**
	 * Remove/Desabilita um item
	 * @param unknown $id
	 * @param unknown $company_id
	 */
	public function removed($id, $company_id){
		return $this->getDbTable ( '\Shop\Model\CompanyTable' )->removed($id, $company_id);
	}
}