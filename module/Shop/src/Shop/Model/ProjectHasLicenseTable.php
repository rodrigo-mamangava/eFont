<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

/**
 * Mapper Projetos and Licencas
 *
 * @author Claudio
 */
class ProjectHasLicenseTable extends AbstractTableGateway {
	protected $table = 'project_has_license';
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
	 * @param unknown $license_id        	
	 * @param unknown $company_id        	
	 * @param unknown $user_id        	
	 * @param unknown $check_enabled        	
	 * @param unknown $project_id        	
	 * @param unknown $sequence        	
	 */
	public function save($id, $money_family, $money_weight, $check_family, $check_weight, $license_id, $company_id, $user_id, $check_enabled, $project_id, $sequence) {
		$data = array (
				'company_id' => $company_id,
				'user_id' => $user_id,
				'project_id' => $project_id,
				'license_id' => $license_id,
				'money_family' => addslashes ( $money_family ),
				'money_weight' => addslashes ( $money_weight ),
				'check_family' => ( int ) $check_family,
				'check_weight' => ( int ) $check_weight,
				'check_enabled' => ( int ) $check_enabled,
				'sequence' => ( int ) $sequence,
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
	 * Retorna todos os itens
	 */
	public function fetchAll($company_id, $project_id) {
		// SELECT
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		// WHERE
		$select->where ( "{$this->table}.project_id='{$project_id}'" );
		$select->where ( "{$this->table}.company_id='{$company_id}'" );
		$select->where ( "{$this->table}.removed='0' OR {$this->table}.removed IS NULL" );
		// ORDER
		$select->order ( "{$this->table}.sequence ASC" );
		// Executando
		// var_dump($select->getSqlString());
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( null );
		$paginator->setCurrentPageNumber ( null );
		
		return $paginator;
	}
	
	/**
	 * Retorna todos os itens relacionados com as licencas
	 */
	public function fetchAllShop($project_id) {
		// SELECT
		$select = new Select ();
		// COLS
		$select->columns ( array (
				'money_family',
				'money_weight',
				'license_id',
				'check_enabled',
				'id',
				'sequence' 
		) );
		// FROM
		$select->from ( $this->table );
		// JOIN
		$select->join ( 'license', new \Zend\Db\Sql\Expression ( "license.id = {$this->table}.license_id AND license.check_enabled='1'"), array (
				'name',
				'check_trial',
				'check_desktop',
				'check_app',
				'check_web' 
		), 'inner' );
		// WHERE
		$select->where ( "{$this->table}.project_id='{$project_id}'" );
		$select->where ( "{$this->table}.check_enabled='1'" );
		$select->where ( "{$this->table}.removed='0' OR {$this->table}.removed IS NULL" );
		
		$select->where ( "license.removed='0' OR license.removed IS NULL" );
		// ORDER
		$select->order ( "license.name ASC" );
		// Executando
		// var_dump($select->getSqlString());
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
}