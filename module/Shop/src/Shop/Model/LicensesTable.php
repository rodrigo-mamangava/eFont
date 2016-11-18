<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

class LicensesTable extends AbstractTableGateway {
	protected $table = 'license';
	// Nome da tabela no banco
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}

    /**
     * Salva/Atualiza um item
     *
     * @param $id
     * @param $name
     * @param $file
     * @param $company_id
     * @param $user_id
     * @param $check_trial
     * @param $check_desktop
     * @param $check_app
     * @param $check_web
     * @param $check_enabled
     * @param $currency_dollar
     * @param $currency_euro
     * @param $currency_libra
     * @param $currency_real
     * @param $check_fmt_otf
     * @param $check_fmt_ttf
     * @param $check_fmt_eot
     * @param $check_fmt_woff
     * @param $check_fmt_woff2
     * @param $check_fmt_trial
     * @param $check_custom
     * @return bool|int
     */
	public function save(
	    $id, $name, $file, $company_id, $user_id, $check_trial,
        $check_desktop, $check_app, $check_web, $check_enabled,
        $currency_dollar, $currency_euro, $currency_libra, $currency_real,
        $check_fmt_otf, $check_fmt_ttf, $check_fmt_eot, $check_fmt_woff,
        $check_fmt_woff2, $check_fmt_trial, $check_custom ) {
		$data = array (
				'company_id' => $company_id,
				'user_id' => $user_id,
				'name' => addslashes ( $name ),
				'media_url' => addslashes ( $file ),
				'check_trial' => ( int ) $check_trial,
				'check_desktop' => ( int ) $check_desktop,
				'check_app' => ( int ) $check_app,
				'check_web' => ( int ) $check_web,
				'check_enabled' => ( int ) $check_enabled,
				'currency_dollar' => addslashes ( $currency_dollar ),
				'currency_euro' => addslashes ( $currency_euro ),
				'currency_libra' => addslashes ( $currency_libra ),
				'currency_real' => addslashes ( $currency_real ),
                'check_fmt_otf' => ( int ) $check_fmt_otf,
                'check_fmt_ttf' => ( int ) $check_fmt_ttf,
                'check_fmt_eot' => ( int ) $check_fmt_eot,
                'check_fmt_woff' => ( int ) $check_fmt_woff,
                'check_fmt_woff2' => ( int ) $check_fmt_woff2,
                'check_fmt_trial' => ( int ) $check_fmt_trial,
                'check_custom' => ( int ) $check_custom,
				'dt_update' => date ( 'Y-m-d H:i:s' ) 
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
				'id' => $id 
		);
		if ($company_id != null && $company_id > 0) {
			$where ['company_id'] = $company_id;
		}
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
			
			if ($company_id != null && $company_id > 0) {
				$select->where ( "{$this->table}.company_id='{$company_id}'" );
			}
			
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
     * Retorna as ativas
     *
     * @param $company_id
     * @return \Zend\Paginator\Paginator
     */
	public function fetchAllActive($company_id){
		// SELECT
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		// WHERE
		$select->where ( "({$this->table}.removed='0' OR {$this->table}.removed IS NULL)" );
		$select->where ( "{$this->table}.company_id='{$company_id}'" );
		$select->where ( "{$this->table}.check_enabled='1'" );
		// ORDER
		$select->order ( "{$this->table}.name ASC" );
		// Executando
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( null );
		$paginator->setCurrentPageNumber ( 0 );
		
		return $paginator;		
	}

    /**
     * Retorna as ativas para o shopping
     *
     * @param $company_id
     * @param $project_id
     * @return \Zend\Paginator\Paginator
     */
	public function fetchAllToShop($company_id, $project_id){
		// SELECT
		$select = new Select ();
		//COLS
		$select->columns(array('*'));
		//JOIN
		$select->join('family_has_license', new \Zend\Db\Sql\Expression ("family_has_license.license_id={$this->table}.id AND family_has_license.check_enabled='1' AND family_has_license.project_id='{$project_id}'"), null, 'inner');
		// FROM
		$select->from ( $this->table );
		// WHERE
		$select->where ( "({$this->table}.removed='0' OR {$this->table}.removed IS NULL)" );
		$select->where ( "{$this->table}.company_id='{$company_id}'" );
		$select->where ( "{$this->table}.check_enabled='1'" );
		//GROUP BY
		$select->group("{$this->table}.id");
		// ORDER
		$select->order ( "{$this->table}.name ASC" );
		//var_dump($select->getSqlString()); exit;
		// Executando
		$adapter = new \Zend\Paginator\Adapter\DbSelect ( $select, $this->adapter, $this->resultSetPrototype );
		$paginator = new \Zend\Paginator\Paginator ( $adapter );
		$paginator->setItemCountPerPage ( null );
		$paginator->setCurrentPageNumber ( 0 );
	
		return $paginator;
	}

    /**
     * Consulta customizada
     *
     * @param $search
     * @param $count
     * @param $offset
     * @param $company_id
     * @param int $check_custom
     * @return \Zend\Paginator\Paginator
     */
	public function filter($search, $count, $offset, $company_id, $check_custom = 0) {
		// SELECT
		$select = new Select ();
        $select->columns(
            array(
                'id',
                'name',
                'media_url',
                'dt_creation',
                'dt_update',
                'removed',
                'company_id',
                'user_id',
                'check_trial',
                'check_desktop',
                'check_app',
                'check_web',
                'check_enabled',
                'currency_dollar',
                'currency_euro',
                'currency_libra',
                'currency_real',
                'check_fmt_otf',
                'check_fmt_ttf',
                'check_fmt_eot',
                'check_fmt_woff',
                'check_fmt_woff2',
                'check_fmt_trial',
                'check_custom',
                'total_info' => new \Zend\Db\Sql\Expression (
                    "( SELECT COUNT(lhf.id) 
                         FROM license_has_formats lhf
                        WHERE lhf.license_id = {$this->table}.id
                          AND TRIM(lhf.parameters) <> ''
                          AND TRIM(lhf.multiplier) <> ''
                          AND lhf.removed <> 1 )"
                )
            )
        );
		// FROM
		$select->from ( $this->table );
		if (! is_null ( $search ) && strlen ( $search ) > 1) {
			$search = addslashes ( $search );
			$select->where ( "({$this->table}.name LIKE '%$search%')" );
		}
		// WHERE
		$select->where ( "({$this->table}.removed='0' OR {$this->table}.removed IS NULL)" );
		
		if ($company_id != null && $company_id > 0) {
			$select->where ( "{$this->table}.company_id='{$company_id}'" );
		}

        if ( $check_custom != null ) {
            $select->where ( "{$this->table}.check_custom='{$check_custom}'" );
        }
		
		// ORDER
		$select->order ( "{$this->table}.name ASC" );

//        echo $select->getSqlString();
//        exit;

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
     * @return \Zend\Paginator\Paginator
     */
	public function fetchAll() {
		// SELECT
		$select = new Select ();
		// FROM
		$select->from ( $this->table );
		// WHERE
		$select->where ( "{$this->table}.removed='0' OR {$this->table}.removed IS NULL" );
		// ORDER
		$select->order ( "{$this->table}.name ASC" );
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
     * @param $id
     * @param $company_id
     * @return array|bool
     */
	public function removed($id, $company_id) {
		// Update
		$data = array ();
		$data ['removed'] = '1';
		// Where
		$where = array ();
		$where ['id'] = $id;
		if ($company_id != null && $company_id > 0) {
			$where ['company_id'] = $company_id;
		}
		// Atualizando
		if (! $this->update ( $data, $where )) {
			return false;
		}
		return $data;
	}
}