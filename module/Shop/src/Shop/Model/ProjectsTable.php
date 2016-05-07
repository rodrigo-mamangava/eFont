<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

/**
 * Mapper Projects
 *
 * @author Claudio
 */
class ProjectsTable extends AbstractTableGateway {
	protected $table = 'project';
	// Nome da tabela no banco
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	/**
	 * Salva/Atualiza um item
	 *
	 * @param unknown $id        	
	 * @param unknown $name        	
	 * @param unknown $company_id        	
	 * @param unknown $user_id        	
	 */
	public function save($id, $name, $company_id, $user_id, $project_ddig, $project_banner) {
		$data = array (
				'company_id' => $company_id,
				'name' => addslashes ( $name ),
				'user_id' => $user_id,
				'dt_update' => date ( 'Y-m-d H:i:s' ),
				'ddig' => $project_ddig,
				'banner' => $project_banner 
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
				'id' => $id,
				'company_id' => $company_id 
		);
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
	 * @param unknown $search        	
	 * @param unknown $count        	
	 * @param unknown $offset        	
	 * @param unknown $company_id        	
	 */
	public function filter($search, $count, $offset, $company_id) {
		// SELECT
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		if (! is_null ( $search ) && strlen ( $search ) > 1) {
			$search = addslashes ( $search );
			$select->where ( "({$this->table}.name LIKE '%$search%')" );
		}
		// WHERE
		$select->where ( "({$this->table}.removed='0' OR {$this->table}.removed IS NULL)" );
		if ($company_id != null && $company_id > 0) {
			$select->where ( "{$this->table}.company_id='{$company_id}'" );
		}
		// ORDER
		$select->order ( "{$this->table}.id DESC" );
		// Executando
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( $count );
		$paginator->setCurrentPageNumber ( $offset );
		
		return $paginator;
	}
	/**
	 * Retorna todos os itens
	 */
	public function fetchAll() {
		// SELECT
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		// WHERE
		$select->where ( "{$this->table}.removed='0' OR {$this->table}.removed IS NULL" );
		// ORDER
		$select->order ( "{$this->table}.name ASC" );
		// Executando
		// var_dump($select->getSqlString()); exit;
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( null );
		$paginator->setCurrentPageNumber ( null );
		
		return $paginator;
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
		$where ['company_id'] = $company_id;
		// Atualizando
		if (! $this->update ( $data, $where )) {
			return false;
		}
		return $data;
	}
}