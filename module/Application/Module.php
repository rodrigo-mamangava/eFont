<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module {
	/**
	 *
	 * @param MvcEvent $e        	
	 */
	public function onBootstrap(MvcEvent $e) {
		$eventManager = $e->getApplication ()->getEventManager ();
		$moduleRouteListener = new ModuleRouteListener ();
		$moduleRouteListener->attach ( $eventManager );
		
		$e->getApplication ()->getServiceManager ()->get ( 'viewhelpermanager' )->setFactory ( 'controllerName', function ($sm) use($e) {
			$viewHelper = new \Application\Helper\ControllerName ( $e->getRouteMatch () );
			return $viewHelper;
		} )->setFactory ( 'SystemConfig', function ($sm) {
			$helper = new \Application\Helper\SystemConfig ( $sm );
			return $helper;
		} );
		
		// Language
		// Allow
		$default = 'pt_BR';
		$supported = array (
				'US' => 'en_US',
				'EN' => 'en_US',
				'BR' => 'pt_BR',
				'PT' => 'pt_BR',
				'ES' => 'es_ES',
		);
		// Zend\Session\Container
		$Session = new \Zend\Session\Container ( 'language' );
		// GET LANGUAGE
		if (method_exists ( $e->getRequest (), 'getQuery' )) {
			$lang = strtoupper ( $e->getRequest ()->getQuery ( 'language' ) ); // Search language
			if (array_key_exists ( $lang, $supported )) {
				$Session->locale = $supported [$lang];
			} elseif (! isset ( $Session->locale )) {
				
				$headers = $e->getApplication ()->getRequest ()->getHeaders ();
				if ($headers->has ( 'Accept-Language' )) {
					$headerLocale = $headers->get ( 'Accept-Language' )->getPrioritized ();
					$lang = strtoupper(substr ( $headerLocale [0]->getLanguage (), 0, 2 ));
					if (array_key_exists ( $lang, $supported )) {
						$default = $supported [$lang];
					}
				}
				$Session->locale = $default;
			}
		}
		// Setter Language
		$translator = $e->getApplication ()->getServiceManager ()->get ( 'translator' );
		$translator->setLocale ( $Session->locale );
		
		$viewModel = $e->getApplication ()->getMvcEvent ()->getViewModel ();
		$key = array_search ( $Session->locale, $supported );
		if ($key === false) {
			$viewModel->LANGUAGE = 'br';
		} else {
			$viewModel->LANGUAGE = strtolower ( $key );
		}
	}
	
	/**
	 */
	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}
	
	/**
	 */
	public function getServiceConfig() {
		return include __DIR__ . '/config/module.config.php';
	}
	
	/**
	 *
	 * @return multitype:multitype:multitype:string
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
}
