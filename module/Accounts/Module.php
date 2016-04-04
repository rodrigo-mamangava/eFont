<?php
namespace Accounts;

class Module
{
	/**
	 * Redirecionando para o Layout de Admin
	 * @param ModuleManager $moduleManager
	 */
	public function init(\Zend\ModuleManager\ModuleManager $moduleManager) {
		$sharedEvents = $moduleManager->getEventManager ()->getSharedManager ();
		$sharedEvents->attach ( __NAMESPACE__, 'dispatch', function ($e) {
			// This event will only be fired when an ActionController under the MyModule namespace is dispatched.
			$controller = $e->getTarget ();
			$controller->layout ( 'accounts/layout' );
		}, 100 );
	}
	/**
	 * Carregando configuracoes principais
	 */
	public function getAutoloaderConfig() {
		return array (
				'Zend\Loader\StandardAutoloader' => array (
						'namespaces' => array (
								__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__ 
						) 
				) 
		);
	}
	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}
	public function getServiceConfig() {
		return include __DIR__ . '/config/module.config.php';
	}
}
