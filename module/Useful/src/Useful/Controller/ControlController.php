<?php

namespace Useful\Controller;

class ControlController {
	protected $_ServiceLocatorInterface = null;
	protected $_language = 'en_US';
	
	/**
	 * Constructor
	 *
	 * @param \Zend\ServiceManager\ServiceManager $ServiceLocatorInterface        	
	 */
	public function __construct(\Zend\ServiceManager\ServiceManager $ServiceLocatorInterface, $language = 'en_US') {
		$this->_ServiceLocatorInterface = $ServiceLocatorInterface;
		$this->_language = $language;
	}
	public function getServiceLocator() {
		return $this->_ServiceLocatorInterface;
	}
	public function setForceLocale($language) {
		$this->_language = $language;
	}
	public function getTranslate() {
		$translator = $this->_ServiceLocatorInterface->get ( 'translator' );
		$translator->setLocale ( $this->_language );
		return $translator;
	}
	
	/**
	 * Retorna uma tabela
	 *
	 * @param Namespace|String $DbTable        	
	 */
	public function getDbTable($DbTable) {
		if (class_exists ( $DbTable )) {
			return $this->_ServiceLocatorInterface->get ( $DbTable );
		}
		return false;
	}
	public static function getStaticDbTable($DbTable) {
		if (class_exists ( $DbTable )) {
			return $this->_ServiceLocatorInterface->get ( $DbTable );
		}
		return false;
	}
	public function getConfig() {
		return $this->_ServiceLocatorInterface->get ( 'Config' );
	}
}