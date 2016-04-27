<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

/**
 * Mapper Family and Files
 *
 * @author Claudio
 */
class FamilyFilesTable extends AbstractTableGateway {
	protected $table = 'family_files';
	// Nome da tabela no banco
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	/**
	 * Salva/Atualiza um item
	 *
	 * @param unknown $id        	
	 * @param unknown $font_name        	
	 * @param unknown $font_id        	
	 * @param unknown $font_subfamily        	
	 * @param unknown $font_family        	
	 * @param unknown $font_copyright        	
	 * @param unknown $font_file        	
	 * @param unknown $font_path        	
	 * @param unknown $font_price        	
	 * @param unknown $check_price        	
	 * @param unknown $company_id        	
	 * @param unknown $user_id        	
	 * @param unknown $project_id        	
	 * @param unknown $family_id        	
	 * @param unknown $family_has_formats_id        	
	 * @param unknown $license_formats_id        	
	 */
	public function save($id, $font_name, $font_id, $font_subfamily, $font_family, $font_copyright, $font_file, $font_path, $font_price, $check_price, $company_id, $user_id, $project_id, $family_id, $family_has_formats_id, $license_formats_id) {
		$data = array (
				'company_id' => $company_id,
				'user_id' => $user_id,
				'project_id' => $project_id,
				
				'family_id' => $family_id,
				'license_formats_id' => $license_formats_id,
				'family_has_formats_id' => $family_has_formats_id,
				
				'font_name' => addslashes ( $font_name ),
				'font_id' => addslashes ( $font_id ),
				'font_subfamily' => addslashes ( $font_subfamily ),
				'font_family' => addslashes ( $font_family ),
				'font_copyright' => addslashes ( $font_copyright ),
				'font_file' => addslashes ( $font_file ),
				'font_path' => addslashes ( $font_path ),
				'font_price' => addslashes ( $font_price ),
				'check_price' => ( int ) $check_price,
				'dt_update' => date ( 'Y-m-d H:i:s' ),
				'removed' => 0 
		);
		
		// var_dump($data);
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
	public function find($id, $company_id) {
		try {
			// SELECT
			$select = new Select ();
			// FROM
			$select->from ( $this->table );
			// WHERE
			$select->where ( "$this->table.id = '{$id}'" );
			$select->where ( "{$this->table}.company_id='{$company_id}'" );
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
			$select->where ( "({$this->table}.font_name LIKE '%$search%')" );
		}
		// WHERE
		$select->where ( "({$this->table}.removed='0' OR {$this->table}.removed IS NULL)" );
		$select->where ( "{$this->table}.company_id='{$company_id}'" );
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
	 */
	public function fetchAll($company_id, $project_id, $family_id, $family_has_formats_id, $license_formats_id) {
		// SELECT
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		// WHERE
		$select->where ( "{$this->table}.company_id='{$company_id}'" );
		$select->where ( "{$this->table}.project_id='{$project_id}'" );
		$select->where ( "{$this->table}.family_id='{$family_id}'" );
		$select->where ( "{$this->table}.family_has_formats_id='{$family_has_formats_id}'" );
		$select->where ( "{$this->table}.license_formats_id='{$license_formats_id}'" );
		$select->where ( "{$this->table}.removed='0' OR {$this->table}.removed IS NULL" );
		// ORDER
		$select->order ( "{$this->table}.id ASC" );
		// Executando
		// var_dump($select->getSqlString()); exit;
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( null );
		$paginator->setCurrentPageNumber ( null );
		
		return $paginator;
	}
	
	public function fetchAllFamily($company_id, $project_id, $family_id, $license_formats_id){
		// SELECT
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		// WHERE
		$select->where ( "{$this->table}.company_id='{$company_id}'" );
		$select->where ( "{$this->table}.project_id='{$project_id}'" );
		$select->where ( "{$this->table}.family_id='{$family_id}'" );
		$select->where ( "{$this->table}.license_formats_id='{$license_formats_id}'" );
		$select->where ( "{$this->table}.removed='0' OR {$this->table}.removed IS NULL" );
		// ORDER
		$select->order ( "{$this->table}.id ASC" );
		// Executando
		// var_dump($select->getSqlString()); exit;
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( null );
		$paginator->setCurrentPageNumber ( null );
		
		return $paginator;
	}
	/**
	 * Retorna todos os itens
	 */
	public function fetchAllByProject($company_id, $project_id, $family_id, $family_has_formats_id, $license_formats_id) {
		// SELECT
		$select = new Select ();
		// COLS
		$select->columns ( array (
				'id',
				'font_name',
				'font_id',
				'font_subfamily',
				'font_family',
				'font_copyright',
				'font_file',
				'font_price',
				'check_price',
				'company_id',
				'user_id',
				'family_has_formats_id',
				'family_id',
				'license_formats_id',
				'project_id' 
		) );
		// FROM
		$select->from ( $this->table );
		// WHERE
		$select->where ( "{$this->table}.company_id='{$company_id}'" );
		$select->where ( "{$this->table}.project_id='{$project_id}'" );
		$select->where ( "{$this->table}.family_id='{$family_id}'" );
		$select->where ( "{$this->table}.family_has_formats_id='{$family_has_formats_id}'" );
		$select->where ( "{$this->table}.license_formats_id='{$license_formats_id}'" );
		$select->where ( "{$this->table}.removed='0' OR {$this->table}.removed IS NULL" );
		// ORDER
		$select->order ( "{$this->table}.id ASC" );
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
	 * @param unknown $company_id        	
	 * @param unknown $project_id        	
	 * @param unknown $family_id        	
	 */
	public function cleanup($company_id, $project_id) {
		// Update
		$data = array ();
		$data ['removed'] = '1';
		// Where
		$where = array ();
		$where ['project_id'] = $project_id;
		$where ['company_id'] = $company_id;
		// Atualizando
		if (! $this->update ( $data, $where )) {
			return false;
		}
		return $data;
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