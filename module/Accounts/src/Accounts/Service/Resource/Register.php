<?php
namespace Accounts\Service\Resource;

use \Accounts\Service\OAuth2\Consumer as Consumer;

/**
 * Conexao com o Register
 *
 * @author CALRAIDEN
 *        
 */
class Register
{

    protected $data = array();

    /**
     * Constructor
     *
     * @param unknown $accessToken            
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Obtem o ID
     *
     * @return NULL
     */
    public function getId()
    {
        return $this->data['id'];
    }

    /**
     * Obtem o Email
     *
     * @return NULL
     */
    public function getEmail()
    {
        return $this->data['email'];
    }

    /**
     * Returna o name da conta
     * @return NULL
     */
    public function getName()
    {
    	return $this->data['name'];
    }
    /**
     * Returna o username da conta
     * @return NULL
     */
    public function getUsername()
    {
        return $this->data['username'];
    }
	/**
	 * Retorna o id da companhia
	 */
	public function getCompany_id(){
		return $this->data['company_id'];
	}
	/**
	 * Retorna o nivel de privilegio
	 */
	public function getPrivilege_type(){
		return isset($this->data['privilege_type'])?$this->data['privilege_type']:null;
	}
	
	/**
	 * 2FA
	 */
	public function getTwo_factor(){
		return isset($this->data['two_factor'])?$this->data['two_factor']:1;
	}
	/**
	 * 2FA Code
	 */
	public function getTwo_factor_secret(){
		return isset($this->data['two_factor_secret'])?$this->data['two_factor_secret']:null;
	}
    /**
     * Obtem o profile do usuario
     *
     * @return array
     */
    public function getProfile()
    {
        return $this->data;
    }
    /**
     * Retorna a imagem do perfil
     */
    public function getImage(){
    	return $this->data['image'];
    }

    public function setImage($image){
    	return $this->data['image'] = $image;
    }
    /**
     * Obtem a imagem
     *
     * @param string $large            
     * @return multitype:
     */
    public function getPicture($large = false)
    {
        return null;
    }

    
    /**
     * Setta os dados
     *
     * @param unknown $label            
     * @param unknown $value            
     */
    protected function _setData($label, $value)
    {
        $this->data[$label] = $value;
    }

    /**
     * Verifica se contem determinado dado
     *
     * @param unknown $label            
     * @return boolean
     */
    protected function _hasData($label)
    {
        return isset($this->data[$label]) && (NULL !== $this->data[$label]);
    }
}