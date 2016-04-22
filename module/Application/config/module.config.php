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
						 * ADMIN
						 */
						/**
						 * SALES
						 */
						'shop-projects' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/shop-projects[/][/:action]',
										'constraints' => array (
												'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\Projects',
												'action' => 'index' 
										) 
								) 
						),
						'ef-products' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/ef-products[/][/:action]',
										'constraints' => array (
												'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\Products',
												'action' => 'index' 
										) 
								) 
						),
						'ef-licenses' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/ef-licenses[/][/:action]',
										'constraints' => array (
												'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\Licenses',
												'action' => 'index' 
										) 
								) 
						),
						'ef-formats' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/ef-formats[/][/:action]',
										'constraints' => array (
												'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\LicenseFormats',
												'action' => 'index' 
										) 
								) 
						),
						'ef-upload' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/ef-upload[/][/:action]',
										'constraints' => array (
												'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\Upload',
												'action' => 'index' 
										) 
								) 
						),
						'ef-font-files' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/ef-font-files[/][/:action]',
										'constraints' => array (
												'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*'
										),
										'defaults' => array (
												'controller' => 'Application\Controller\FontFile',
												'action' => 'index'
										)
								)
						),						
						/**
						 * USER
						 */
						'shop-customer' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/shop-customer[/][/:action]',
										'constraints' => array (
												'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\Customer',
												'action' => 'index' 
										) 
								) 
						),
						'shop-customer-account' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/shop-customer-account[/][/:action]',
										'constraints' => array (
												'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\Index',
												'action' => 'customer' 
										) 
								) 
						),
						'ef-profile' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/ef-profile[/][/:action]',
										'constraints' => array (
												'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\Profile',
												'action' => 'index' 
										) 
								) 
						),
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
						'shop-checkout-complete' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/shop-checkout-complete[/][/:action]',
										'constraints' => array (
												'action' => '(?!\bpage\b)[a-zA-Z][a-zA-Z0-9_-]*' 
										),
										'defaults' => array (
												'controller' => 'Application\Controller\ShopCheckout',
												'action' => 'complete' 
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
						 * Index
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
						// View Sales
						'Application\Controller\Projects' => 'Application\Controller\ProjectsController',
						'Application\Controller\Licenses' => 'Application\Controller\LicensesController',
						'Application\Controller\LicenseFormats' => 'Application\Controller\LicenseFormatsController',						
						'Application\Controller\Products' => 'Application\Controller\ProductsController',
						'Application\Controller\FontFile' => 'Application\Controller\FontFileController',
						// View Logged
						'Application\Controller\Customer' => 'Application\Controller\CustomerController',
						'Application\Controller\Profile' => 'Application\Controller\ProfileController',
						'Application\Controller\Upload' => 'Application\Controller\UploadController',
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
						'upload/file' => __DIR__ . '/../view/application/upload/upload.phtml',
						'shop/product/list' => __DIR__ . '/../view/application/example/product-grid.phtml',
						'shop/product/search' => __DIR__ . '/../view/application/shop-product-list/list.phtml',
						'shop/customer/account' => __DIR__ . '/../view/application/customer/account.phtml',
						'shop/customer/leftmenu' => __DIR__ . '/../view/application/customer/leftmenu.phtml',
						'shop/projects/leftmenu' => __DIR__ . '/../view/application/projects/leftmenu.phtml',
						'shop/licenses/breadcrumbs' => __DIR__ . '/../view/application/licenses/breadcrumbs.phtml',
						'shop/projects/breadcrumbs' => __DIR__ . '/../view/application/projects/breadcrumbs.phtml',
						'shop/products/breadcrumbs' => __DIR__ . '/../view/application/products/breadcrumbs.phtml',
						// Outras partials compartilhados
						'no-data-to-display' => __DIR__ . '/../view/layout/partials/no-data-to-display.phtml',
						'file-upload-form-static' => __DIR__ . '/../view/application/upload/index.phtml',
						'fonts-upload-form-static' => __DIR__ . '/../view/application/upload/fonts.phtml',
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