<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

class CustomLicenseHasBasicLicensesTable extends AbstractTableGateway {
	protected $table = 'clicense_has_blicenses';
	// Nome da tabela no banco
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}

    /**
     * Salva um item
     *
     * @param $license_custom_id
     * @param $license_basic_id
     * @return bool
     */
	public function save( $license_custom_id, $license_basic_id ) {
		$data = array (
				'license_custom_id' => $license_custom_id,
				'license_basic_id' => $license_basic_id
		);

        // inserting
        if (! $this->insert ( $data )) {
            return false;
        }
        //var_dump($this);
        return true;
	}

    /**
     * All basic licenses from a custom license
     *
     * @param $license_custom_id
     * @return \Zend\Paginator\Paginator
     */
	public function fetchAll( $license_custom_id ) {
		// SELECT
		$select = new Select ();
		// COLS
		$select->columns ( array (
				'license_custom_id',
				'license_basic_id'
		) );
		// FROM
		$select->from ( $this->table );

        // JOIN
        $select->join (
            'license',
            "license.id = {$this->table}.license_basic_id",
            array (
                'name' => 'name'
            ),
            'inner'
        );

		// WHERE
		$select->where ( "({$this->table}.license_custom_id='{$license_custom_id}')" );
		// ORDER
		$select->order ( "{$this->table}.license_basic_id ASC" );
		// var_dump($select->getSqlString());
		// Executando
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( null );
		$paginator->setCurrentPageNumber ( null );
		
		return ($paginator);
	}

    /**
     * All basic licenses from a custom license
     *
     * @param $company_id
     * @return \Zend\Paginator\Paginator
     */
    public function fetchAllByCompanyId( $company_id ) {
        // SELECT
        $select = new Select ();
        // COLS
        $select->columns ( array (
            'license_custom_id',
            'license_basic_id'
        ) );
        // FROM
        $select->from ( $this->table );

        // JOIN
        $select->join (
            'license',
            new \Zend\Db\Sql\Expression(
                "license.id = {$this->table}.license_basic_id AND license.company_id = {$company_id} "
            ),
            array (
                'name' => 'name'
            ),
            'inner'
        );

        // ORDER
        $select->order ( "{$this->table}.license_custom_id ASC, license.name ASC " );
        // var_dump($select->getSqlString());
        // Executando
        $adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
        $paginator = new \Zend\Paginator\Paginator ( $adapter );
        $paginator->setItemCountPerPage ( null );
        $paginator->setCurrentPageNumber ( null );

        return ($paginator);
    }

    /**
     * Remove pela chave da licenca custom
     *
     * @param $license_custom_id
     * @return bool
     */
	public function removeByCustomLicense ( $license_custom_id ) {
		// Where
		$where = [];
		$where ['license_custom_id'] = $license_custom_id;
		// Excluindo
		if (! $this->delete ( $where ) ) {
			return false;
		}
		return true;
	}
}