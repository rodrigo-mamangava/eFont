<?php
return array (
		'factories' => array (
				'Shop\Model\CompanyTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\CompanyTable ( $dbAdapter );
				},
				'Shop\Model\UserSystemTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\UserSystemTable ( $dbAdapter );
				} 
		) 
);