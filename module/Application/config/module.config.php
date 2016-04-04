<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array (
		'router' => array (
				'routes' => array (
						'home' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/',
										'defaults' => array (
												'controller' => 'Application\Controller\Index',
												'action' => 'index' 
										) 
								) 
						),
						'blank' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/blank',
										'defaults' => array (
												'controller' => 'Application\Controller\Index',
												'action' => 'blank' 
										) 
								) 
						),
						'sitemap' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/sitemap',
										'defaults' => array (
												'controller' => 'Application\Controller\Index',
												'action' => 'sitemap' 
										) 
								) 
						),
						/**
						 * LOGGED
						 */
						/**
						 * SHOP
						 */
						'shop-cart' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/shop-cart[/][/:action]',
										'constraints' => array (
												'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\ShopCart',
												'action' => 'index' 
										) 
								) 
						),
						'shop-checkout' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/shop-checkout[/][/:action]',
										'constraints' => array (
												'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\ShopCheckout',
												'action' => 'index' 
										) 
								) 
						),
						'shop-product-details' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/shop-product-details[/][/:action]',
										'constraints' => array (
												'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\ShopProductDetails',
												'action' => 'index' 
										) 
								) 
						),
						'shop-product-users' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/shop-product-users[/][/:action]',
										'constraints' => array (
												'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\ShopProductUsers',
												'action' => 'index' 
										) 
								) 
						),
						'shop-product-list' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/shop-product-list[/][/:action]',
										'constraints' => array (
												'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\ShopProductList',
												'action' => 'index' 
										) 
								) 
						),
						/**
						 * USER
						 */
						'ef-welcome' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/ef-welcome[/][/:action]',
										'constraints' => array (
												'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\Welcome',
												'action' => 'index' 
										) 
								) 
						),
						// The following is a route to simplify getting started creating
						// new controllers and actions without needing to create a new
						// module. Simply drop new controllers in, and you can access them
						// using the path /application/:controller/:action
						'application' => array (
								'type' => 'Literal',
								'options' => array (
										'route' => '/application',
										'defaults' => array (
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Index',
												'action' => 'index' 
										) 
								),
								'may_terminate' => true,
								'child_routes' => array (
										'default' => array (
												'type' => 'Segment',
												'options' => array (
														'route' => '/[:controller[/:action]]',
														'constraints' => array (
																'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
																'action' => '[a-zA-Z][a-zA-Z0-9_-]*' 
														),
														'defaults' => array () 
												) 
										) 
								) 
						) 
				) 
		),
		
		'navigation' => array (
				'default' => array (
						array (
								'label' => 'Home',
								'route' => 'home',
								'lastmod' => '2015-04-22',
								'changefreq' => 'monthly',
								'priority' => '1.0' 
						) 
				) 
		),
		'service_manager' => array (
				'abstract_factories' => array (
						'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
						'Zend\Log\LoggerAbstractServiceFactory' 
				),
				'factories' => array (
						'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory' 
				),
				'aliases' => array (
						'translator' => 'MvcTranslator' 
				) 
		),
		'translator' => array (
				'locale' => 'en_US',
				'translation_file_patterns' => array (
						array (
								'type' => 'gettext',
								'base_dir' => __DIR__ . '/../language',
								'pattern' => '%s.mo' 
						) 
				) 
		),
		'controllers' => array (
				'invokables' => array (
						// View Logged
						// View Shop
						'Application\Controller\ShopCart' => 'Application\Controller\ShopCartController',
						'Application\Controller\ShopCheckout' => 'Application\Controller\ShopCheckoutController',
						'Application\Controller\ShopProductDetails' => 'Application\Controller\ShopProductDetailsController',
						'Application\Controller\ShopProductUsers' => 'Application\Controller\ShopProductUsersController',
						'Application\Controller\ShopProductList' => 'Application\Controller\ShopProductListController',
						'Application\Controller\Welcome' => 'Application\Controller\WelcomeController',
						// Home
						'Application\Controller\Index' => 'Application\Controller\IndexController' 
				) 
		),
		'view_manager' => array (
				'display_not_found_reason' => true,
				'display_exceptions' => true,
				'doctype' => 'HTML5',
				'not_found_template' => 'error/404',
				'exception_template' => 'error/index',
				'template_map' => array (
						'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
						'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
						'error/404' => __DIR__ . '/../view/error/404.phtml',
						'error/index' => __DIR__ . '/../view/error/index.phtml',
						// Layout
						'metatags' => __DIR__ . '/../view/layout/metatags.phtml',
						'navbar' => __DIR__ . '/../view/layout/navbar.phtml',
						'spinners' => __DIR__ . '/../view/layout/spinners.phtml',
						'copyright' => __DIR__ . '/../view/layout/copyright.phtml',
						'message' => __DIR__ . '/../view/layout/message.phtml',
						'upload/file' => __DIR__ . '/../view/application/upload/upload.phtml' 
				),
				'template_path_stack' => array (
						__DIR__ . '/../view' 
				) 
		),
		
		// Placeholder for console routes
		'console' => array (
				'router' => array (
						'routes' => array () 
				) 
		) 
);