<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

/**
 * Mapper FamilyHasFormatsTable
 *
 * @author Claudio
 */
class FamilyHasFormatsTable extends AbstractTableGateway {
	protected $table = 'family_has_formats';
	// Nome da tabela no banco
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
    /**
     * Salva/Atualiza um item
     *
     * @param $id
     * @param $family_id
     * @param $license_formats_id
     * @param $media_url
     * @param $number_files
     * @param $collapsed
     * @param $company_id
     * @param $user_id
     * @param $project_id
     * @return bool|int
     */
	public function save($id, $family_id, $license_formats_id, $media_url, $number_files, $collapsed, $company_id, $user_id, $project_id) {
		$data = array (
				'company_id' => $company_id,
				'user_id' => $user_id,
				'project_id' => $project_id,
				
				'license_formats_id' => $license_formats_id,
				'family_id' => $family_id,
				
				'media_url' => addslashes ( $media_url ),
				'number_files' => $number_files,
				'collapsed' => $collapsed,
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
			$select->where ( "({$this->table}.family_name LIKE '%$search%')" );
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
     *
     * @param $company_id
     * @param $family_id
     * @param $project_id
     * @return \Zend\Paginator\Paginator
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
		$select->order ( "{$this->table}.license_formats_id ASC" );
		// Executando
		// var_dump($select->getSqlString()); exit;
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( null );
		$paginator->setCurrentPageNumber ( null );
		
		return $paginator;
	}

    /**
     * Busca por projeto
     *
     * @param $company_id
     * @param $license_formats_id
     * @param $project_id
     * @return \Zend\Paginator\Paginator
     */
	public function fetchAllByProject($company_id, $license_formats_id, $project_id) {
		// SELECT
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		// COLS
		$select->columns ( array (
				'id',
				'license_formats_id',
				'family_id',
				'number_files',
				'project_id',
				'company_id',
		) );
		// JOIN
		$select->join ( 'family', "family.id = {$this->table}.family_id", array (
				'family_name' 
		), 'inner' );
		
		// WHERE
		$select->where ( "{$this->table}.license_formats_id='{$license_formats_id}'" );
		$select->where ( "{$this->table}.project_id='{$project_id}'" );
		$select->where ( "{$this->table}.company_id='{$company_id}'" );
		$select->where ( "{$this->table}.removed='0' OR {$this->table}.removed IS NULL" );
		// ORDER
		$select->order ( "{$this->table}.license_formats_id ASC" );
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
     * @param $company_id
     * @param $project_id
     * @return array|bool
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