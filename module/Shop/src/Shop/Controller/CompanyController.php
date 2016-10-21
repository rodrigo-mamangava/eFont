<?php

namespace Shop\Controller;

use \Useful\Controller\ControlController;

/**
 * Responsavel pelo gerenciamento das empresas
 * @author Claudio
 */
class CompanyController extends ControlController {

    /**
     * Salva/Atualiza um item
     *
     * @param $id
     * @param $name
     * @param $phone
     * @param $email
     * @param $contact
     * @param $address
     * @param $address_number
     * @param $address_city
     * @param $address_state
     * @param $address_country
     * @param $address_postal_code
     * @param string $map_lat
     * @param string $map_lng
     * @param int $status
     * @param null $company_id
     * @param null $check_fmt_otf
     * @param null $check_fmt_ttf
     * @param null $check_fmt_eot
     * @param null $check_fmt_woff
     * @param null $check_fmt_woff2
     * @return mixed
     */
	public function save($id, $name, $phone, $email, $contact, $address, $address_number, $address_city, $address_state, $address_country, $address_postal_code, $map_lat = '', $map_lng = '', $status = 0, $company_id = null, $check_fmt_otf = null, $check_fmt_ttf = null, $check_fmt_eot = null, $check_fmt_woff = null, $check_fmt_woff2 = null ) {
		return $this->getDbTable ( '\Shop\Model\CompanyTable' )->save($id, $name, $phone, $email, $contact, $address, $address_number, $address_city, $address_state, $address_country, $address_postal_code, $map_lat, $map_lng, $status, $company_id = null, $check_fmt_otf = null, $check_fmt_ttf = null, $check_fmt_eot = null, $check_fmt_woff = null, $check_fmt_woff2 = null);
	}

    /**
     * Busca pelo chave principal
     *
     * @param $id
     * @param null $company_id
     * @return mixed
     */
	public function find($id, $company_id = null){
		return $this->getDbTable ( '\Shop\Model\CompanyTable' )->find($id, $company_id);
	}

    /**
     * Consulta customizada
     *
     * @param $search
     * @param $count
     * @param $offset
     * @param $company_id
     * @return mixed
     */
	public function filter ( $search, $count, $offset, $company_id){
		return $this->getDbTable ( '\Shop\Model\CompanyTable' )->filter( $search, $count, $offset, $company_id);
	}

    /**
     * Retorna todos os itens
     *
     * @return mixed
     */
	public function fetchAll(){
		return $this->getDbTable ( '\Shop\Model\CompanyTable' )->fetchAll();
	}

    /**
     * Atualizacao individual
     *
     * @param $id
     * @param $data
     * @param $company_id
     * @return mixed
     */
	public function updated($id, $data, $company_id){
		return $this->getDbTable ( '\Shop\Model\CompanyTable' )->updated($id, $data, $company_id);
	}

    /**
     * Remove/Desabilita um item
     *
     * @param $id
     * @param $company_id
     * @return mixed
     */
	public function removed($id, $company_id){
		return $this->getDbTable ( '\Shop\Model\CompanyTable' )->removed($id, $company_id);
	}
}