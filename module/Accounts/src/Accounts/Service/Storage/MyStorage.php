<?php

namespace Accounts\Service\Storage;

use Zend\Authentication\Storage\StorageInterface;
use Zend\Session\Container as SessionNameSpace; 

class MyStorage implements StorageInterface {
	const SESSION_NAMESPACE = "MultipleIdentities";
	protected $_session;
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->_session = new SessionNameSpace ( self::SESSION_NAMESPACE );
	}
	
	/**
	 * Returns true if and only if storage is empty
	 *
	 * @throws \Zend\Authentication\Exception\ExceptionInterface If it is impossible to
	 *         determine whether storage is empty
	 * @return boolean
	 */
	public function isEmpty($provider = null) {
		$container = $this->read ();
		if (! $container) {
			return true;
		} else if ($container->isEmpty ( $provider )) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Returns the contents of storage
	 *
	 * Behavior is undefined when storage is empty.
	 *
	 * @throws \Zend\Authentication\Exception\ExceptionInterface If reading contents from storage is impossible
	 * @return mixed
	 */
	public function read($provider = null) {
		if (! isset ( $this->_session->identityContainer )) {
			return false;
		} else {
			$container = unserialize ( $this->_session->identityContainer );
			if (null !== $provider) {
				return $container->get ( $provider );
			} else {
				return $container;
			}
		}
	}
	
	/**
	 * Writes $contents to storage
	 *
	 * @param mixed $contents        	
	 * @throws \Zend\Authentication\Exception\ExceptionInterface If writing $contents to storage is impossible
	 * @return void
	 */
	public function write($container) {
		if (get_class ( $container ) !== 'Accounts\Service\Identity\Container') {
			throw new \Exception ( 'No valid identity container' );
		}
		$this->_session->identityContainer = serialize ( $container );
	}
	
	/**
	 * Clears contents from storage
	 *
	 * @throws \Zend\Authentication\Exception\ExceptionInterface If clearing contents from storage is impossible
	 * @return void
	 */
	public function clear($provider = null) {
		if (null !== $provider && false != $container = $this->read ()) {
			$container->remove ( $provider );
			$this->write ( $container );
		} else {
			unset ( $this->_session->identityContainer );
		}
	}
}