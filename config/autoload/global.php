<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
return array (
		'db' => array (
				'adapters' => array (
						'Adapter' => array (
								'driver' => 'Pdo',
								'dsn' => 'mysql:dbname=mmgv_efont;host=localhost',
								'username' => 'mmgv_efont',
								'password' => 'mmgv_efont',
								'driver_options' => array (
										PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'' 
								) 
						),
						'Accounts' => array (
								'driver' => 'Pdo',
								'dsn' => 'mysql:dbname=mmgv_efont;host=localhost',
								'username' => 'mmgv_efont',
								'password' => 'mmgv_efont',
								'driver_options' => array (
										PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'' 
								) 
						) 
				) 
		),
		'service_manager' => array (
				'abstract_factories' => array (
						'Zend\Db\Adapter\AdapterAbstractServiceFactory' 
				),
				'factories' => array (
						'Zend\Log\Logger' => function ($sm) {
							$logger = new Zend\Log\Logger ();
							$writer = new Zend\Log\Writer\Stream ( './data/logs/' . date ( 'Y-m-d' ) );
							$logger->addWriter ( $writer );
							$logger->info ( "\n\n +----------+ \n\n" );
							return $logger;
						} 
				) 
		) 
);
