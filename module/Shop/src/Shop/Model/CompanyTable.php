<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

class CompanyTable extends AbstractTableGateway {

    protected $table = 'company';
	// Nome da tabela no banco
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}

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
     * @param $map_lat
     * @param $map_lng
     * @param $status
     * @param null $company_id
     * @param null $check_fmt_otf
     * @param null $check_fmt_ttf
     * @param null $check_fmt_eot
     * @param null $check_fmt_woff
     * @param null $check_fmt_woff2
     * @return bool|int
     */
	public function save($id, $name, $phone, $email, $contact, $address, $address_number, $address_city, $address_state, $address_country, $address_postal_code, $map_lat, $map_lng, $status, $company_id = null, $check_fmt_otf = null, $check_fmt_ttf = null, $check_fmt_eot = null, $check_fmt_woff = null, $check_fmt_woff2 = null) {
		$data = array (
				'company_id' => $company_id,
				'name' => addslashes ( $name ),
				'phone' => addslashes ( $phone ),
				'email' => addslashes ( $email ),
				'contact' => addslashes ( $contact ),
				'address' => addslashes ( $address ),
				'address_number' => addslashes ( $address_number ),
				'address_city' => addslashes ( $address_city ),
				'address_state' => addslashes ( $address_state ),
				'address_country' => addslashes ( $address_country ),
				'address_postal_code' => addslashes ( $address_postal_code ),
				'status' => ( int ) $status,
				'map_lng' => addslashes ( $map_lng ),
				'map_lat' => addslashes ( $map_lat ),
				'dt_update' => date ( 'Y-m-d H:i:s' ),
                'check_fmt_otf' => ( int ) $check_fmt_otf,
                'check_fmt_ttf' => ( int ) $check_fmt_ttf,
                'check_fmt_eot' => ( int ) $check_fmt_eot,
                'check_fmt_woff' => ( int ) $check_fmt_woff,
                'check_fmt_woff2' => ( int ) $check_fmt_woff2
		);
		
		$id = ( int ) $id;
		if ($id == 0) {
			unset ( $data ['id'] );
			$data ['dt_creation'] = $data ['dt_update'];
			// Inserindo
			if (! $this->insert ( $data )) {
				return false;
			}
			return $this->getLastInsertValue ();
		} else {
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
     * @param $id
     * @param $data
     * @param $company_id
     * @return bool
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
     * @param $id
     * @param $company_id
     * @return bool
     * @throws \Exception
     */
	public function find($id, $company_id) {
		try {
			// SELECT
			$select = new Select ();
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
     * Consulta customizada
     *
     * @param $search
     * @param $count
     * @param $offset
     * @param $company_id
     * @return \Zend\Paginator\Paginator
     */
	public function filter($search, $count, $offset, $company_id) {
		// SELECT
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		if (! is_null ( $search ) && strlen ( $search ) > 1) {
			$search = addslashes ( $search );
			$select->where ( "({$this->table}.name LIKE '%$search%' OR {$this->table}.email LIKE '%$search%' OR {$this->table}.address LIKE '%$search%')" );
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
     * Retorna todos os itens
     *
     * @return \Zend\Paginator\Paginator
     */
	public function fetchAll() {
		// SELECT
		$select = new Select ();
		// FROM
		$select->from ( $this->table );		
		// WHERE
		$select->where("{$this->table}.removed='0' OR {$this->table}.removed IS NULL");
		// ORDER
		$select->order("{$this->table}.name ASC");
		// Executando
		//var_dump($select->getSqlString()); exit;
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage(null);
		$paginator->setCurrentPageNumber(null);
		
		return $paginator;
	}

    /**
     * Remove um item
     *
     * @param $id
     * @param $company_id
     * @return array|bool
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