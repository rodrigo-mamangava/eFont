<?php

namespace Shop\Controller;

/**
 * Usuarios do sistema
 * @author Claudio
 */
class UserSystemController extends \Useful\Controller\ControlController {
	/**
	 * Salva/Atualiza um item
	 * @param unknown $id
	 * @param unknown $username
	 * @param unknown $password
	 * @param unknown $email
	 * @param unknown $phone
	 * @param unknown $privilege_type_id
	 * @param unknown $company_id
	 * @param unknown $status
	 * @param unknown $firstname
	 * @param unknown $lastname
	 * @param unknown $address
	 * @param unknown $address_city
	 * @param unknown $address_complement
	 * @param unknown $address_country
	 * @param unknown $address_postcode
	 * @param string $image
	 */
	public function save($id, $username, $password, $email, $phone, $privilege_type_id, $company_id, $status, $firstname, $lastname, $address, $address_city, $address_complement, $address_country, $address_postcode, $image = '') {
		return $this->getDbTable ( '\Shop\Model\UserSystemTable' )->save ($id, $username, $password, $email, $phone, $privilege_type_id, $company_id, $status, $firstname, $lastname, $address, $address_city, $address_complement, $address_country, $address_postcode, $image);
	}
	/**
	 * Busca pelo chave principal
	 *
	 * @param unknown $id
	 * @param unknown $company_id
	 */
	public function find($id, $company_id = null) {
		return $this->getDbTable ( '\Shop\Model\UserSystemTable' )->find ( $id, $company_id );
	}
	/**
	 * Busca pelo username
	 *
	 * @param unknown $username
	 */
	public function findByUsername($username) {
		return $this->getDbTable ( '\Shop\Model\UserSystemTable' )->findByUsername ( $username );
	}

	/**
	 * Busca por id, email e senha
	 *
	 * @param unknown $company_id
	 * @param unknown $username
	 * @param unknown $password
	 */
	public function findByUsernamePassword($company_id, $username, $password) {
		$company_id = ( int ) $company_id;
		$username = addslashes ( $username );
		$password = md5 ( addslashes ( $password ) );

		return $this->getDbTable ( '\Shop\Model\UserSystemTable' )->findByUsernamePassword ( $company_id, $username, $password );
	}
	
	/**
	 * Retorna todos os registros de um email
	 * @param unknown $email
	 */
	public function fetchByEmail($email) {
		if ($email != null) {
			// Mapper
			$UserTable = $this->getDbTable ( '\Shop\Model\UserSystemTable' );
			return $UserTable->fetchByEmail ( $email );
		}
		return false;
	}
	/**
	 * Busca de um unico email
	 * @param unknown $email
	 */
	public function findByEmail($email) {
		return $this->getDbTable ( '\Shop\Model\UserSystemTable' )->findByEmail($email);
	}
	/**
	 * Consulta customizada
	 *
	 * @param unknown $search
	 * @param unknown $count
	 * @param unknown $offset
	 * @param unknown $company_id
	 */
	public function filter($search, $count, $offset, $company_id) {
		return $this->getDbTable ( '\Shop\Model\UserSystemTable' )->filter ( $search, $count, $offset, $company_id );
	}
	/**
	 * Atualizacao individual
	 *
	 * @param unknown $id
	 * @param unknown $data
	 * @param unknown $company_id
	 */
	public function updated($id, $data, $company_id) {
		return $this->getDbTable ( '\Shop\Model\UserSystemTable' )->updated ( $id, $data, $company_id );
	}
	/**
	 * Alterando um dado do sistema
	 *
	 * @param unknown $id
	 * @param unknown $password,
	 *        	nova senha
	 * @param string $current,
	 *        	senha atual
	 */
	public function updatePassword($id, $password, $current = null) {
		// Mapper
		$UserTable = $this->getDbTable ( '\Shop\Model\UserSystemTable' );
		return $UserTable->updatePassword ( $id, $password, $current );
	}
	/**
	 * Remove/Desabilita um item
	 *
	 * @param unknown $id
	 * @param unknown $company_id
	 */
	public function removed($id, $company_id) {
		return $this->getDbTable ( '\Shop\Model\UserSystemTable' )->removed ( $id, $company_id );
	}
}