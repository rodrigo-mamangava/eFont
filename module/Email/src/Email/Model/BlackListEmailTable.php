<?php

namespace Email\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
/**
 * Mapper BlackListEmail
 * @author Claudio
 *
 */
class BlackListEmailTable extends AbstractTableGateway {
	protected $table = 'mail_blacklist_email'; // Nome da tabela no banco
	/**
	 * Construtor
	 * @param Adapter $adapter
	 */
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	/**
	 * Adiciona um item
	 * @param unknown $enrolled
	 */
	public function create($enrolled){
		$data = array('enrolled'=>addslashes($enrolled),'dt_creation'=>date('Y-m-d H:i:s'), 'dt_update'=>date('Y-m-d H:i:s'));
		// Inserindo
		if (! $this->insert ( $data )) {
			return false;
		}
		return $this->getLastInsertValue ();
	}
	/**
	 * Busca customizada
	 * @param unknown $search
	 * @param unknown $count
	 * @param unknown $offset
	 * @throws \Exception
	 */
	public function fetchAll($search, $count, $offset) {
		try {
			//SELECT
			$select = new Select ();
			$select->from ( $this->table );
			// WHERE
			if (strlen ( $search ) > 1) {
				$search = addslashes($search);
				$select->where ( "enrolled='{$search}'");
			}
			$select->where ( "(removed='0' OR removed IS NULL)" );
			// LIMIT
			if ($count != null && $count > 0) {
				$select->limit ( ( int ) $count );
			}
			if ($offset != null && $offset > 0) {
				$select->offset ( ( int ) $offset );
			}
			//QUERY
			$resultSet = $this->selectWith ( $select );
			if (! $resultSet) {
				return false;
			}
		} catch ( Exception $e ) {
			throw new \Exception ( 'ERROR : ' . $e->getMessage () );
		}
		
		$entities = array ();
		foreach ( $resultSet as $row ) {
			$entities [] = $this->putColumnsInTheArray ( $row );
		}
		
		return $entities;
	}
	/**
	 * Busca por um item
	 * @return boolean|multitype:
	 */
	public function fetch($search) {
		$row = $this->select ( array (
				'enrolled' => addslashes($search),
		) )->current ();
	
		if (! $row){
			return false;
		}
		/**
		 * Retornando Array, pois sera usado diretamente nas APIs
		 */
		return $this->putColumnsInTheArray ( $row );
	}
	/**
	 * Desabilita um item pelo nome
	 * @param unknown $item
	 */
	public function removed($item) {
		$data = array();
		$data ['removed'] = '1';
		// Atualizando
		if (! $this->update ( $data, array (
				'enrolled' => $item,
		) )) {
			return false;
		}
		return $data;
	}
	/**
	 * Inseri os dados no Array pre formatado
	 *
	 * @param Array $row
	 * @return Array
	 */
	protected function putColumnsInTheArray($row) {
		$data = array();
		// Adicionando dados
		$data ['id'] = $row->id;
		$data ['enrolled'] = stripslashes($row->enrolled);
		$data ['dt_creation'] = $row->dt_creation;
		$data ['dt_update'] = $row->dt_update;
		$data ['removed'] = $row->removed;
	
		return $data;
	}	
}	