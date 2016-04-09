<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

/**
 * Usuários do SISTEMA
 * 
 * @author Claudio
 *        
 */
class UserSystemTable extends AbstractTableGateway {
	protected $table = 'user_system';
	// Nome da tabela no banco
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	/**
	 * Salva/Atualiza um item
	 *
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
		$data = array (
				'company_id' => $company_id,
				'username' => addslashes ( $username ),				
				'name' => addslashes ( $firstname . ' ' . $lastname ),
				'firstname' => addslashes ( $firstname ),
				'lastname' => addslashes ( $lastname ),
				'email' => addslashes ( $email ),
				'roll' => ( int ) $privilege_type_id,
				'privilege_type_id' => ( int ) $privilege_type_id,
				'status' => ( int ) $status,
				'image' => addslashes ( $image ),
				'phone' => addslashes ( $phone ),
				'address' => addslashes ( $address ),
				'address_complement' => addslashes ( $address_complement ),
				'address_city' => addslashes ( $address_city ),
				'address_country' => addslashes ( $address_country ),
				'address_postcode' => addslashes ( $address_postcode ),
				'dt_update' => date ( 'Y-m-d H:i:s' ) 
		);
		
		$id = ( int ) $id;
		if ($id == 0) {
			unset ( $data ['id'] );
			$data ['dt_creation'] = $data ['dt_update'];
			$data ['password'] = md5 ( $password );
			$data ['uuid'] = new \Zend\Db\Sql\Expression ( 'UUID()' );
			// Inserindo
			if (! $this->insert ( $data )) {
				return false;
			}
			return $this->getLastInsertValue ();
		} else {
			if (strlen ( $password ) > 7) {
				$data ['password'] = md5 ( $password );
			}
			// Atualizando
			if (! $this->update ( $data, array (
					'id' => $id 
			) )) {
				return false;
			}
		}
		return $id;
	}
	/**
	 * Atualizacao individual
	 *
	 * @param unknown $id        	
	 * @param unknown $data        	
	 */
	public function updated($id, $data, $company_id) {
		$data ['dt_update'] = date ( 'Y-m-d H:i:s' );
		
		$where = array (
				'id' => $id 
		);
		if ($company_id != null && $company_id > 0) {
			$where ['company_id'] = $company_id;
		}
		// Atualizando
		if (! $this->update ( $data, $where )) {
			return false;
		}
		return $data;
	}
	/**
	 * Busca pela chave principal
	 *
	 * @param unknown $id        	
	 * @throws Exception
	 * @return boolean|multitype:
	 */
	public function find($id, $company_id = null) {
		try {
			// SELECT
			$select = new Select ();
			// JOIN
			$select->join ( 'company', "company.id = {$this->table}.company_id", array (
					'company_name' => 'name' 
			), 'left' );
			
			$select->join ( 'privilege_type', "privilege_type.id = {$this->table}.privilege_type_id", array (
					'privilege_name' => 'name' 
			), 'left' );
			// FROM
			$select->from ( $this->table );
			// WHERE
			$select->where ( "$this->table.id = '{$id}'" );
			
			if ($company_id != null && $company_id > 0) {
				$select->where ( "{$this->table}.company_id='{$company_id}'" );
			}
			
			// ORDER
			$resultSet = $this->selectWith ( $select );
			if (! $resultSet) {
				return false;
			}
			$row = $resultSet->current ();
			if (! $row) {
				return false;
			}
			
			return $row;
		} catch ( Exception $e ) {
			throw new \Exception ( 'ERROR : ' . $e->getMessage () );
		}
	}
	/**
	 * Busca pelo username
	 *
	 * @param unknown $username        	
	 */
	public function findByUsername($username) {
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		// WHERE
		$username = addslashes ( $username );
		$select->where ( "username='{$username}'" );
		// LIMIT
		$select->limit ( 1 );
		$resultSet = $this->selectWith ( $select );
		if (! $resultSet) {
			return false;
		}
		$row = $resultSet->current ();
		if (! $row) {
			return false;
		}
		
		return $row;
	}
	/**
	 * Busca pelo email
	 *
	 * @param unknown $email        	
	 */
	public function findByEmail($email) {
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		// WHERE
		$email = addslashes ( $email );
		$select->where ( "email='{$email}'" );
		// LIMIT
		$select->limit ( 1 );
		$resultSet = $this->selectWith ( $select );
		if (! $resultSet) {
			return false;
		}
		$row = $resultSet->current ();
		if (! $row) {
			return false;
		}
		
		return $row;
	}
	
	/**
	 * Busca por emails
	 * 
	 * @param unknown $email        	
	 */
	public function fetchByEmail($email) {
		$select = new Select ();
		$select->from ( $this->table );
		
		$email = addslashes ( $email );
		$select->where ( "email='{$email}'" );
		
		$resultSet = $this->selectWith ( $select );
		if (! $resultSet) {
			return false;
		}
		
		$rs = array ();
		foreach ( $resultSet as $row ) {
			$rs [] = iterator_to_array ( $row );
		}
		
		return $rs;
	}
	
	/**
	 * Busca por id, email e senha
	 * 
	 * @param unknown $company_id        	
	 * @param unknown $username        	
	 * @param unknown $password        	
	 */
	public function findByUsernamePassword($company_id, $username, $password) {
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		// WHERE
		$username = addslashes ( $username );
		$select->where ( "{$this->table}.username='{$username}'" );
		$select->where ( "{$this->table}.company_id='{$company_id}'" );
		$select->where ( "{$this->table}.password='{$password}'" );
		// LIMIT
		$select->limit ( 1 );
		$resultSet = $this->selectWith ( $select );
		if (! $resultSet) {
			return false;
		}
		$row = $resultSet->current ();
		if (! $row) {
			return false;
		}
		
		return $row;
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
		// SELECT
		$select = new Select ();
		// JOIN
		$select->join ( 'company', "company.id = {$this->table}.company_id", array (
				'company_name' => 'name' 
		), 'left' );
		// FROM
		$select->from ( $this->table );
		if (! is_null ( $search ) && strlen ( $search ) > 1) {
			$search = addslashes ( $search );
			$select->where ( "({$this->table}.name LIKE '%$search%' OR {$this->table}.email LIKE '%$search%' OR {$this->table}.username LIKE '%$search%')" );
		}
		// WHERE
		$select->where ( "({$this->table}.removed='0' OR {$this->table}.removed IS NULL)" );
		
		if ($company_id != null && $company_id > 0) {
			$select->where ( "{$this->table}.company_id='{$company_id}'" );
		}
		
		// ORDER
		$select->order ( "{$this->table}.name ASC" );
		// Executando
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( $count );
		$paginator->setCurrentPageNumber ( $offset );
		
		return $paginator;
	}
	
	/**
	 * Atualiza somente o password
	 *
	 * @param unknown $user_id        	
	 * @param unknown $password        	
	 * @param string $current,
	 *        	senha atual, se passada, e feita a verificacao
	 */
	public function updatePassword($user_id, $password, $current = null) {
		// Interno
		$rs = self::find ( $user_id );
		// UPDATE
		if ($rs) {
			$data = array ();
			$data ['password'] = md5 ( $password );
			$data ['dt_update'] = date ( 'Y-m-d H:i:s' );
			// WHERE
			$where = array (
					'id' => $user_id 
			);
			if (! is_null ( $current )) {
				$where ['password'] = md5 ( $current );
			}
			// Atualizando
			if (! $this->update ( $data, $where )) {
				return false;
			}
			return $user_id;
		}
		return false;
	}
	/**
	 * Remove um item
	 *
	 * @param unknown $id        	
	 */
	public function removed($id, $company_id) {
		// Update
		$data = array ();
		$data ['removed'] = '1';
		// Where
		$where = array ();
		$where ['id'] = $id;
		if ($company_id != null && $company_id > 0) {
			$where ['company_id'] = $company_id;
		}
		// Atualizando
		if (! $this->update ( $data, $where )) {
			return false;
		}
		return $data;
	}
}