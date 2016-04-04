<?php
return array (
		'controllers' => array (
				'invokables' => array (
						'Accounts\Controller\Account' => 'Accounts\Controller\AccountController',
						'Accounts\Controller\TwoFactor' => 'Accounts\Controller\TwoFactorController',
				) 
		),
		
		'factories' => array (),
		
		'router' => array (
				'routes' => array (
						'sign-up' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/sign-up',
										'defaults' => array (
												'controller' => 'Accounts\Controller\Account',
												'action' => 'signUp' 
										) 
								) 
						),
						'connect' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/connect',
										'defaults' => array (
												'controller' => 'Accounts\Controller\Account',
												'action' => 'connect' 
										) 
								) 
						),
						'logout' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/logout',
										'defaults' => array (
												'controller' => 'Accounts\Controller\Account',
												'action' => 'logout' 
										) 
								) 
						),
						'login' => array (
								'type' => 'Segment',
								'options' => array (
										'route' => '/login',
										'defaults' => array (
												'controller' => 'Accounts\Controller\Account',
												'action' => 'login' 
										) 
								),
								'may_terminate' => true,
								'child_routes' => array (
										'default' => array (
												'type' => 'Segment',
												'options' => array (
														'route' => '[/:provider]',
														'constraints' => array (
																'provider' => '[a-zA-Z][a-zA-Z0-9_-]*' 
														),
														'defaults' => array () 
												) 
										) 
								) 
						),
						'forget' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/forget',
										'defaults' => array (
												'controller' => 'Accounts\Controller\Account',
												'action' => 'forget' 
										) 
								) 
						),
						'available' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/available',
										'defaults' => array (
												'controller' => 'Accounts\Controller\Account',
												'action' => 'available'
										)
								)
						),
						'reset' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/reset',
										'defaults' => array (
												'controller' => 'Accounts\Controller\Account',
												'action' => 'reset' 
										) 
								) 
						),
						'two-factor-auth' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/two-factor-auth',
										'defaults' => array (
												'controller' => 'Accounts\Controller\TwoFactor',
												'action' => 'index'
										)
								)
						),	
						'two-factor-problems' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/two-factor-problems',
										'defaults' => array (
												'controller' => 'Accounts\Controller\TwoFactor',
												'action' => 'problems'
										)
								)
						),		
						'two-factor-verify' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/two-factor-verify',
										'defaults' => array (
												'controller' => 'Accounts\Controller\TwoFactor',
												'action' => 'save'
										)
								)
						),
						
				) 
		),
		'view_manager' => array (
				'template_map' => array (
						'accounts/layout' => __DIR__ . '/../view/layout/layout.phtml',
						'accounts/header' => __DIR__ . '/../view/layout/header.phtml',
						'accounts/footer' => __DIR__ . '/../view/layout/footer.phtml'
				),
				'template_path_stack' => array (
						__DIR__ . '/../view'
				)
		),		 
);