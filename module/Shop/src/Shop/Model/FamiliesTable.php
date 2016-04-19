<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

/**
 * Mapper Families
 *
 * @author Claudio
 */
class FamiliesTable extends AbstractTableGateway {
	protected $table = 'family';
	// Nome da tabela no banco
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	/**
	 * Salva/Atualiza um item
	 *
	 * @param unknown $id        	
	 * @param unknown $family_name        	
	 * @param unknown $project_id        	
	 * @param unknown $company_id        	
	 * @param unknown $user_id        	
	 */
	public function saved($id, $family_name, $project_id, $company_id, $user_id) {
		$data = array (
				'company_id' => $company_id,
				'user_id' => $user_id,
				'project_id' => $project_id,
				'family_name' => addslashes ( $family_name ),
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
	 * Retorna todos os items
	 * 
	 * @param unknown $company_id        	
	 * @param unknown $project_id        	
	 */
	public function fetchAll($company_id, $project_id) {
		// SELECT
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		// WHERE
		$select->where ( "({$this->table}.removed='0' OR {$this->table}.removed IS NULL)" );
		$select->where ( "{$this->table}.company_id='{$company_id}'" );
		$select->where ( "{$this->table}.project_id='{$project_id}'" );
		// ORDER
		$select->order ( "{$this->table}.family_name ASC" );
		// Executando
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( null );
		$paginator->setCurrentPageNumber ( 0 );
		
		return $paginator;
	}
	/**
	 * Removendo pelo project id
	 * 
	 * @param unknown $company_id        	
	 * @param unknown $project_id        	
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