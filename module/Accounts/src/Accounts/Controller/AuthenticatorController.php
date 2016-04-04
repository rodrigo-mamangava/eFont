<?php

namespace Accounts\Controller;

class AuthenticatorController {
	protected static $_instance = null;
	protected $_storage = null;
	public static function getInstance() {
		if (null === self::$_instance) {
			self::$_instance = new self ();
		}
		return self::$_instance;
	}
	protected function __construct() {
	}
	protected function __clone() {
	}
	public function setStorage(\Zend\Authentication\Storage\StorageInterface $storage) {
		$this->_storage = $storage;
		return $this;
	}
	// The default storage is the MultipleIdenties class
	public function getStorage() {
		if (NULL === $this->_storage) {
			$this->setStorage ( new \Accounts\Service\Storage\MyStorage () );
		}
		return $this->_storage;
	}
	
	/**
	 *
	 * This function doesn't delete the identity information but adds the new
	 * identity to the storage. This function only works with adapters that
	 * create a Generic identity.
	 *
	 * @param \Zend_Auth_Adapter_Interface $adapter        	
	 * @throws Exception
	 *
	 */
	public function authenticate(\Zend\Authentication\Adapter\AdapterInterface $adapter) {
		$result = $adapter->authenticate ();
		$identity = $result->getIdentity ();
		if (NULL === $identity) {
			return $result;
		}
		
		if (get_class ( $identity ) !== 'Accounts\Service\Identity\Generic' && ! is_subclass_of ( $identity, 'Accounts\Service\Identity\Generic' )) {
			if (get_class ( $identity ) !== 'Accounts\Service\Identity\Facebook' && get_class ( $identity ) !== 'Accounts\Service\Identity\Google' && get_class ( $identity ) !== 'Accounts\Service\Identity\Twitter') { // Verificacao adicional
				throw new \Exception ( 'Not a valid identity' );
			}
		}
		
		$currentIdentity = $this->getIdentity ();
		return self::addCurrentIdentity($currentIdentity, $result);
	}
	
	public function addCurrentIdentity($currentIdentity, $result){
		if (false === $currentIdentity || get_class ( $currentIdentity ) !== 'Accounts\Service\Identity\Container') {
			$currentIdentity = new \Accounts\Service\Identity\Container ();
		}
		$currentIdentity->add ( $result->getIdentity () );
		if ($this->hasIdentity ()) {
			$this->clearIdentity ();
		}
		
		if ($result->isValid ()) {
			$this->getStorage ()->write ( $currentIdentity );
		}
		return $result;		
	}
	// The three functions below accept the provider parameter so that a
	// specific identity can be retreived or removed.
	public function hasIdentity($provider = null) {
		return ! $this->getStorage ()->isEmpty ( $provider );
	}
	public function getIdentity($provider = null) {
		$storage = $this->getStorage ();
		if ($storage->isEmpty ( $provider )) {
			return false;
		}
		return $storage->read ( $provider );
	}
	public function clearIdentity($provider = null) {
		$this->getStorage ()->clear ( $provider );
	}
}

