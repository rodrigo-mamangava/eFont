<?php

namespace Forgot\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

/**
 * Mapper lembrete de senha
 * 
 * @author Claudio
 *        
 */
class ForgotTable extends AbstractTableGateway {
	protected $table = 'mail_forgot';
	// Nome da tabela no banco
	public function __construct(Adapter $adapter) {
		$this->adapter = $adapter;
	}
	public function save(\Forgot\Model\Entity\Forgot $Forgot) {
		$data = array (
				'dt_used' => null,
				'key' => $Forgot->getKey (),
				'user_id' => $Forgot->getUserId (),
				'status' => $Forgot->getStatus (),
				'remote' => $Forgot->getRemote (),
				'hash' => $Forgot->getHash (),
				'role' => $Forgot->getRole (),
				'attempts' => 0 
		);
		
		$id = ( int ) $Forgot->getId ();
		
		if ($id == 0) {
			// Setando password
			unset ( $data ['id'] );
			$data ['dt_creation'] = date ( 'Y-m-d H:i:s' );
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
			return $id;
		}
		
		return false;
	}
	
	/**
	 * Retorna as chaves ativas que nao foram utilizadas por um usuario
	 *
	 * @param int $id        	
	 * @return boolean \Forgot\Model\Entity\Forgot
	 */
	public function findByUserId($id, $role) {
		$hour_ago = date ( "Y-m-d H:i:s", time () - 86400 );
		$select = new Select ();
		$select->from ( $this->table );
		$select->where ( " user_id ='{$id}' AND status='0' AND dt_creation >= '{$hour_ago}' AND role='{$role}'" );
		$row = $this->selectWith ( $select )->current ();
		
		if (! $row)
			return false;
		
		$Forgot = new Entity\Forgot ( array (
				'id' => $row->id,
				'key' => $row->key,
				'user_id' => $row->user_id,
				'hash' => $row->hash,
				'role' => $row->role,
				'attempts' => $row->attempts 
		) );
		return $Forgot;
	}
	
	/**
	 * Desabilita o hash
	 *
	 * @param unknown $id        	
	 * @param string $role        	
	 * @return boolean
	 */
	public function disabledHash($user_id, $role) {
		// Atualizando
		$data = array (
				'status' => 1,
				'dt_used' => date ( 'Y-m-d H:i:s' ) 
		);
		
		if (! $this->update ( $data, array (
				'user_id' => $user_id,
				'role' => $role 
		) )) {
			return false;
		}
		return $user_id;
	}
	
	/**
	 * Incrementar o numero de tentativas
	 *
	 * @param unknown $id        	
	 * @return boolean
	 */
	public function plusAttempts($id) {
		// Atualizando
		$data = array (
				'attempts' => new \Zend\Db\Sql\Expression ( 'attempts + 1' ) 
		);
		if (! $this->update ( $data, array (
				'id' => $id 
		) )) {
			return false;
		}
	}
}