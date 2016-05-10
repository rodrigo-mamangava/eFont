<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

/**
 * Mapper Fonts e Arquivos
 *
 * @author Claudio
 */
class FontFilesTable extends AbstractTableGateway {
	protected $table = 'font_files';
	// Nome da tabela no banco
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	/**
	 * Salva/Atualiza um item
	 *
	 * @param unknown $id        	
	 * @param unknown $uploadkey        	
	 * @param unknown $font_name        	
	 * @param unknown $font_id        	
	 * @param unknown $font_subfamily        	
	 * @param unknown $font_family        	
	 * @param unknown $font_copyright        	
	 * @param unknown $font_file        	
	 * @param unknown $font_path        	
	 * @param unknown $company_id        	
	 * @param unknown $user_id        	
	 * @param unknown $linked        	
	 * @param unknown $formats_id        	
	 */
	public function save($id, $uploadkey, $font_name, $font_id, $font_subfamily, $font_family, $font_copyright, $font_file, $font_path, $company_id, $user_id, $linked, $formats_id) {
		$data = array (
				'company_id' => $company_id,
				'user_id' => $user_id,
				'formats_id' => $formats_id,
				
				'uploadkey' => $uploadkey,
				
				'font_name' => addslashes ( $font_name ),
				'font_id' => addslashes ( $font_id ),
				'font_subfamily' => addslashes ( $font_subfamily ),
				'font_family' => addslashes ( $font_family ),
				'font_copyright' => addslashes ( $font_copyright ),
				'font_file' => addslashes ( $font_file ),
				'font_path' => addslashes ( $font_path ),
				
				'linked' => ( int ) $linked,
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
	public function fetchAll($company_id, $project_id, $family_id = null, $font_styles_id = null) {
		// SELECT
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		// WHERE
		$select->where ( "{$this->table}.project_id='{$project_id}'" );
		
		if ($company_id != null && $company_id > 1) {
			$select->where ( "{$this->table}.company_id='{$company_id}'" );			
		}
		
		if ($family_id != null && $family_id > 1) {
			$select->where ( "{$this->table}.family_id='{$family_id}'" );
		}
		
		if ($font_styles_id != null && $font_styles_id > 1) {
			$select->where ( "{$this->table}.font_styles_id='{$font_styles_id}'" );
		}
		
		$select->where ( "{$this->table}.removed='0' OR {$this->table}.removed IS NULL" );
		// ORDER
		$select->order ( "{$this->table}.id ASC" );
		// Executando
		// var_dump($select->getSqlString());
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( null );
		$paginator->setCurrentPageNumber ( null );
		
		return $paginator;
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
	 * Sincronizando/Atualizando pelo font styles
	 *
	 * @param unknown $font_styles_id        	
	 * @param unknown $company_id        	
	 * @param unknown $user_id        	
	 * @param unknown $data        	
	 */
	public function synchronize($font_styles_id, $company_id, $user_id, $data) {
		// Update
		$data ['dt_update'] = date ( 'Y-m-d H:i:s' );
		// Where
		$where = array ();
		$where ['font_styles_id'] = $font_styles_id;
		$where ['company_id'] = $company_id;
		$where ['user_id'] = $user_id;
		// Atualizando
		if (! $this->update ( $data, $where )) {
			return false;
		}
		return $font_styles_id;
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