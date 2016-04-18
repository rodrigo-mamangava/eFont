<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

class LicenseFormatsTable extends AbstractTableGateway {
	protected $table = 'license_formats';
	// Nome da tabela no banco
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	/**
	 * Todos os items
	 */
	public function fetchAll() {
		// SELECT
		$select = new Select ();
		$select->from ( $this->table );
		// ORDER
		$select->order ( "{$this->table}.id ASC" );
		// Executando
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( null );
		$paginator->setCurrentPageNumber ( 0 );
		
		return ($paginator);
	}
}