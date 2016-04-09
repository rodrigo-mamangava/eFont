<?php
namespace Forgot\Model\Entity;

use \Exception;

class Forgot
{

    /**
     * int
     *
     * @var id
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * string
     *
     * @var dt_used
     */
    protected $dt_used;

    public function getDtUsed()
    {
        return $this->dt_used;
    }

    public function setDtUsed($dt_used)
    {
        $this->dt_used = $dt_used;
        return $this;
    }

    /**
     * string
     *
     * @var key
     */
    protected $key;

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * string
     *
     * @var status
     */
    protected $status;

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * string
     *
     * @var user_id
     */
    protected $user_id;

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * string
     *
     * @var remote
     */
    protected $remote;

    public function getRemote()
    {
        return $this->remote;
    }

    public function setRemote($remote)
    {
        $this->remote = $remote;
        return $this;
    }

    /**
     * string
     *
     * @var hash
     */
    protected $hash;

    public function getHash()
    {
        return $this->hash;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * string
     *
     * @var attempts
     */
    protected $attempts;

    public function getAttempts()
    {
        return $this->attempts;
    }

    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;
        return $this;
    }

    /**
     * string
     *
     * @var role
     */
    protected $role;

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Other functions
     *
     * @param array $options            
     */
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (! method_exists($this, $method)) {
            throw new Exception('Invalid Method');
        }
        $this->$method($value);
    }

    public function __get($name)
    {
        $method = 'get' . $name;
        if (! method_exists($this, $method)) {
            throw new Exception('Invalid Method');
        }
        return $this->$method();
    }

    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
}