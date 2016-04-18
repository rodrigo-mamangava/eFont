<?php
return array (
		'factories' => array (
				'Shop\Model\CompanyTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\CompanyTable ( $dbAdapter );
				},
				'Shop\Model\LicensesTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\LicensesTable ( $dbAdapter );
				},
				'Shop\Model\LicenseHasFormatsTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\LicenseHasFormatsTable ( $dbAdapter );
				},
				'Shop\Model\LicenseFormatsTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\LicenseFormatsTable ( $dbAdapter );
				},
				'Shop\Model\UserSystemTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\UserSystemTable ( $dbAdapter );
				} 
		) 
);