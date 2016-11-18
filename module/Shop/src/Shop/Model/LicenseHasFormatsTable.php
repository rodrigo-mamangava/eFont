<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

class LicenseHasFormatsTable extends AbstractTableGateway {
	protected $table = 'license_has_formats';
	// Nome da tabela no banco
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}

    /**
     * Salva/Atualiza um item
     *
     * @param $id
     * @param $license_id
     * @param $license_formats_id
     * @param $company_id
     * @param $user_id
     * @param $parameters
     * @param $multiplier
     * @param $sequence
     * @param $license_basic_id
     * @return bool|int
     */
	public function save(
	    $id, $license_id, $license_formats_id,
        $company_id, $user_id, $parameters,
        $multiplier, $sequence, $license_basic_id ) {
		$data = array (
				'company_id' => $company_id,
				'user_id' => $user_id,
				'license_id' => $license_id,
                'license_basic_id' => $license_basic_id,
				'license_formats_id' => $license_formats_id,
				'parameters' => addslashes ( $parameters ),
				'multiplier' => addslashes ( $multiplier ),
				'sequence' => ( int ) $sequence,
				'dt_update' => date ( 'Y-m-d H:i:s' ),
				'removed' => 0 
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
     * Todos os items
     *
     * @param $license_id
     * @param $company_id
     * @return \Zend\Paginator\Paginator
     */
	public function fetchAll($license_id, $company_id) {
		// SELECT
		$select = new Select ();
		$select->from ( $this->table );
		// WHERE
		$select->where ( "{$this->table}.company_id='{$company_id}'" );
		$select->where ( "{$this->table}.license_id = {$license_id}" );
		$select->where ( "({$this->table}.removed='0' OR {$this->table}.removed IS NULL)" );
		// ORDER
		$select->order ( "{$this->table}.sequence ASC" );
		// Executando
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( null );
		$paginator->setCurrentPageNumber ( null );
		
		return ($paginator);
	}

    /**
     * Todos os items
     *
     * @param $license_id
     * @return \Zend\Paginator\Paginator
     */
	public function fetchAllShop($license_id) {
		// SELECT
		$select = new Select ();
		// COLS
		$select->columns ( array (
				'id',
				'license_id',
				'license_formats_id',
				'parameters',
				'multiplier',
				'sequence' 
		) );
		// FROM
		$select->from ( $this->table );
		// WHERE
		$select->where ( "({$this->table}.license_id='{$license_id}')" );
		$select->where ( "({$this->table}.removed='0' OR {$this->table}.removed IS NULL)" );
		// ORDER
		$select->order ( "{$this->table}.sequence ASC" );
		// var_dump($select->getSqlString());
		// Executando
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( null );
		$paginator->setCurrentPageNumber ( null );
		
		return ($paginator);
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
		$data ['dt_update'] = date ( 'Y-m-d H:i:s' );
		// Where
		$where = array ();
		$where ['id'] = $id;
		$where ['company_id'] = $company_id;
		// Atualizando
		if (! $this->update ( $data, $where )) {
			return false;
		}
		return $data;
	}

    /**
     * Remove pela chave da licenca principal
     *
     * @param $license_id
     * @param $company_id
     * @return array|bool
     */
	public function removeByLicense($license_id, $company_id) {
		// Update
		$data = array ();
		$data ['removed'] = '1';
		$data ['dt_update'] = date ( 'Y-m-d H:i:s' );
		// Where
		$where = array ();
		$where ['license_id'] = $license_id;
		$where ['company_id'] = $company_id;
		// Atualizando
		if (! $this->update ( $data, $where )) {
			return false;
		}
		return $data;
	}
}