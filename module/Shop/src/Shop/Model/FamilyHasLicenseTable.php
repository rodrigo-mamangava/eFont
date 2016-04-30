<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

/**
 * Mapper Family and Licencas
 *
 * @author Claudio
 */
class FamilyHasLicenseTable extends AbstractTableGateway {
	protected $table = 'family_has_license';
	// Nome da tabela no banco
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	/**
	 * Salva/Atualiza um item
	 *
	 * @param unknown $id        	
	 * @param unknown $money_family        	
	 * @param unknown $money_weight        	
	 * @param unknown $check_family        	
	 * @param unknown $check_weight        	
	 * @param unknown $project_id        	
	 * @param unknown $family_id        	
	 * @param unknown $license_id        	
	 * @param unknown $company_id        	
	 * @param unknown $user_id        	
	 */
	public function save($id, $money_family, $money_weight, $check_family, $check_weight, $check_enabled, $project_id, $family_id, $license_id, $company_id, $user_id) {
		$data = array (
				'company_id' => $company_id,
				'user_id' => $user_id,
				'project_id' => $project_id,
				'family_id' => $family_id,
				'license_id' => $license_id,
				'money_family' => addslashes ( $money_family ),
				'money_weight' => addslashes ( $money_weight ),
				'check_family' => ( int ) $check_family,
				'check_weight' => ( int ) $check_weight,
				'check_enabled' => ( int ) $check_enabled,
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
			$select->where ( "({$this->table}.money_family LIKE '%$search%')" );
		}
		// WHERE
		$select->where ( "({$this->table}.removed='0' OR {$this->table}.removed IS NULL)" );
		$select->where ( "{$this->table}.company_id='{$company_id}'" );
		// ORDER
		$select->order ( "{$this->table}.license_id ASC" );
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
	public function fetchAll($company_id, $family_id, $project_id) {
		// SELECT
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		// WHERE
		$select->where ( "{$this->table}.family_id='{$family_id}'" );
		$select->where ( "{$this->table}.project_id='{$project_id}'" );
		$select->where ( "{$this->table}.company_id='{$company_id}'" );
		$select->where ( "{$this->table}.removed='0' OR {$this->table}.removed IS NULL" );
		// ORDER
		$select->order("{$this->table}.license_id ASC");
		// Executando
		//var_dump($select->getSqlString()); 
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( null );
		$paginator->setCurrentPageNumber ( null );
		
		return $paginator;
	}
	/**
	 * Somente os ativos e selecionados
	 * @param unknown $company_id
	 * @param unknown $family_id
	 * @param unknown $project_id
	 */
	public function fetchAllToShop($company_id, $family_id, $project_id){
		// SELECT
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		// WHERE
		$select->where ( "{$this->table}.family_id='{$family_id}'" );
		$select->where ( "{$this->table}.project_id='{$project_id}'" );
		$select->where ( "{$this->table}.company_id='{$company_id}'" );
		$select->where ( "{$this->table}.check_enabled='1'" );
		$select->where ( "{$this->table}.removed='0' OR {$this->table}.removed IS NULL" );
		// ORDER
		$select->order ( "{$this->table}.license_id ASC" );
		// Executando
		// var_dump($select->getSqlString()); exit;
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( null );
		$paginator->setCurrentPageNumber ( null );
		
		return $paginator;		
	}
	/**
	 * Busca das familias pelo projeto
	 *
	 * @param unknown $company_id        	
	 * @param unknown $project_id        	
	 */
	public function fetchAllByProject($company_id, $project_id, $license_id, $license_formats_id) {
		// SELECT
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		// COLS
		$select->columns ( array (
				'id',
				'money_family',
				'money_weight',
				'check_family',
				'check_weight',
				'project_id',
				'family_id',
				'license_id',
				'company_id',
				'user_id',
				'check_enabled' 
		) );
		
		// JOIN
		$select->join ( 'family', "family.id = {$this->table}.family_id", array (
				'f_id' => 'id',
				'f_family_name' => 'family_name' 
		), 'inner' );
		
		$select->join ( 'family_has_formats', new \Zend\Db\Sql\Expression ( "family_has_formats.license_formats_id={$license_formats_id} AND family_has_formats.family_id={$this->table}.family_id AND family_has_formats.project_id={$project_id}" ), array (
				'f_h_f_id'=>'id',
				'f_h_f_family_id'=>'family_id',
				'f_h_f_license_formats_id'=>'license_formats_id',
				'f_h_f_number_files'=>'number_files',
				'f_h_f_company_id'=>'company_id',
				'f_h_f_user_id'=>'user_id',
				'f_h_f_project_id'=>'project_id' 
		), 'inner' );
		
		// WHERE
		$select->where ( "{$this->table}.project_id='{$project_id}'" );
		$select->where ( "{$this->table}.company_id='{$company_id}'" );
		$select->where ( "{$this->table}.license_id='{$license_id}'" );
		
		// $select->where ( "{$this->table}.check_enabled='1'" );
		$select->where ( "{$this->table}.removed='0' OR {$this->table}.removed IS NULL" );
		$select->where ( "family.removed='0' OR family.removed IS NULL" );
		// ORDER
		$select->order ( "{$this->table}.license_id ASC" );
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