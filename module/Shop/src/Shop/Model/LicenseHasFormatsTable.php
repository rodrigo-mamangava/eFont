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
	 * @param unknown $id        	
	 * @param unknown $license_id        	
	 * @param unknown $license_formats_id        	
	 * @param unknown $company_id        	
	 * @param unknown $user_id        	
	 * @param unknown $parameters        	
	 * @param unknown $multiplier        	
	 * @param unknown $sequence        	
	 */
	public function save($id, $license_id, $license_formats_id, $company_id, $user_id, $parameters, $multiplier, $sequence) {
		$data = array (
				'company_id' => $company_id,
				'user_id' => $user_id,
				'license_id' => $license_id,
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
	 * @param unknown $license_id        	
	 * @param unknown $company_id        	
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
	 * @param unknown $license_id        	
	 * @param unknown $company_id        	
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
	 * @param unknown $id        	
	 * @param unknown $company_id        	
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
	 * @param unknown $license_id        	
	 * @param unknown $company_id        	
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