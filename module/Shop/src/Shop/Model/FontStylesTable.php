<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

/**
 * Mapper Fonts e Styles
 *
 * @author Claudio
 */
class FontStylesTable extends AbstractTableGateway {
	protected $table = 'font_styles';
	// Nome da tabela no banco
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	/**
	 * Salva/Atualiza um item
	 *
	 * @param unknown $id        	
	 * @param unknown $name        	
	 * @param unknown $user_id        	
	 * @param unknown $company_id        	
	 * @param unknown $uploadkey        	
	 * @param unknown $family_id        	
	 * @param unknown $formats_id        	
	 * @param unknown $project_id        	
	 * @param unknown $linked        	
	 */
	public function save($id, $name, $user_id, $company_id, $uploadkey, $family_id, $formats_id, $project_id, $linked) {
		$data = array (
				'company_id' => $company_id,
				'user_id' => $user_id,
				'project_id' => $project_id,
				'family_id' => $family_id,
				'formats_id' => $formats_id,
				
				'uploadkey' => $uploadkey,
				'font_file' => addslashes ( $name ),
				'font_subfamily' => addslashes ( $name ),
				'linked' => ( int ) $linked,
				'dt_update' => date ( 'Y-m-d H:i:s' ),
				'removed' => 0,
				'check_price' => 0 
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
	 * Atualizando itens pelo array do data
	 * 
	 * @param unknown $id        	
	 * @param unknown $company_id        	
	 * @param unknown $data        	
	 */
	public function updated($id, $company_id, $user_id, $data) {
		// Update
		$data ['dt_update'] = date ( 'Y-m-d H:i:s' );
		// Where
		$where = array ();
		$where ['id'] = $id;
		$where ['company_id'] = $company_id;
		$where ['user_id'] = $user_id;
		// Atualizando
		if (! $this->update ( $data, $where )) {
			return false;
		}
		return $id;
	}
	/**
	 * Retorna todos os itens
	 * 
	 * @param unknown $company_id        	
	 * @param unknown $project_id        	
	 * @param unknown $family_id        	
	 * @param unknown $family_has_formats_id        	
	 * @param unknown $formats_id        	
	 */
	public function fetchAll($company_id, $project_id, $family_id, $family_has_formats_id, $formats_id) {
		// SELECT
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		// WHERE
		$select->where ( "{$this->table}.family_id='{$family_id}'" );
		$select->where ( "{$this->table}.family_has_formats_id='{$family_has_formats_id}'" );
		$select->where ( "{$this->table}.formats_id='{$formats_id}'" );
		
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
	 * Retorna todos os itens pre formatados
	 * 
	 * @param unknown $company_id        	
	 * @param unknown $project_id        	
	 */
	public function fetchAllShop($company_id, $project_id) {
		// SELECT
		$select = new Select ();
		// COLS
		$select->columns ( array (
				'id',
				'font_file',
				'font_subfamily',
				'font_price',
				'sequence',
				'uploadkey',
				'formats_id',
				'linked',
				'family_id',
				'family_has_formats_id' 
		) );
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