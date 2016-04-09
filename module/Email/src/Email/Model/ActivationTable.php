<?php
namespace Email\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

class ActivationTable extends AbstractTableGateway
{
    protected $table = 'mail_activation';
 // Nome da tabela no banco
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Salva/Atualiza um item
     * @param unknown $email
     * @param unknown $mobile_number
     * @param unknown $mobile_ddi
     * @param unknown $pin
     * @param unknown $secure
     * @return boolean|number
     */
    public function create($email, $mobile_number, $mobile_ddi, $pin, $secure)
    {
        $data = array(
            'email' => $email,
            'mobile_number' => $mobile_number,
            'mobile_ddi' => $mobile_ddi,
            'pin' => $pin,
            'dt_creation' => date("Y-m-d H:i:s"),
            'dt_update' => date("Y-m-d H:i:s"),
            'used' => 0,
            'secure' => $secure,
        );
        
        unset($data['id']);
        // Inserindo
        if (! $this->insert($data)) {
            return false;
        }
        return $this->getLastInsertValue();
    }

    /**
     * Busca pelo email
     * 
     * @param unknown $email            
     * @return boolean|multitype:
     */
    public function findByEmail($email, $id = null)
    {
        $select = new Select();
        $select->from($this->table);
        $select->where("email='{$email}' ");
        if (! is_null($id) && $id > 0) {
            $select->where(" id='{$id}' ");
        }
        $select->order("id DESC");
        $select->limit(1);
        
        $resultSet = $this->selectWith($select);
        if (! $resultSet) {
            return false;
        }
        $row = $resultSet->current();
        if (! $row) {
            return false;
        }
        /**
         * Retornando Array, pois sera usado diretamente nas APIs
         */
        return $this->putColumnsInTheArray($row);
    }

    /**
     * Desabilita um codigo de ativacao
     *
     * @param unknown $id            
     * @return boolean unknown
     */
    public function disabling($id)
    {
        $data = array(
            'used' => 1,
            'dt_update' => date('Y-m-d H:i:s')
        );
        // Atualizando
        $where['id'] = $id;
        $where['used'] = 0;
        if (! $this->update($data, $where)) {
            return false;
        }
        return $id;
    }

    /**
     * Inseri os dados no Array pre formatado
     *
     * @param Array $row            
     * @return Array
     */
    protected function putColumnsInTheArray($row)
    {
    	$data = array();
        $data['id'] = $row->id;
        $data['email'] = $row->email;
        $data['mobile_number'] = $row->mobile_number;
        $data['mobile_ddi'] = $row->mobile_ddi;
        $data['pin'] = $row->pin;
        $data['dt_creation'] = $row->dt_creation;
        $data['dt_update'] = $row->dt_update;
        $data['used'] = $row->used;
        $data['secure'] = $row->secure;
        
        return $data;
    }
}