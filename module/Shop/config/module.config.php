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
				},
				'Shop\Model\LicensesTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\LicensesTable ( $dbAdapter );
				},
				'Shop\Model\LicenseHasFormatsTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\LicenseHasFormatsTable ( $dbAdapter );
				},
                'Shop\Model\CustomLicenseHasBasicLicensesTable' => function ($sm) {
                    $dbAdapter = $sm->get ( 'Adapter' );
                    return new \Shop\Model\CustomLicenseHasBasicLicensesTable ( $dbAdapter );
                },
				'Shop\Model\LicenseFormatsTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\LicenseFormatsTable ( $dbAdapter );
				},
				'Shop\Model\ProjectsTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\ProjectsTable ( $dbAdapter );
				},
				'Shop\Model\ProjectHasLicenseTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\ProjectHasLicenseTable ( $dbAdapter );
				},
				'Shop\Model\ProjectHasFamilyTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\ProjectHasFamilyTable ( $dbAdapter );
				},
				'Shop\Model\FontStylesTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\FontStylesTable ( $dbAdapter );
				},
				'Shop\Model\FontFilesTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\FontFilesTable ( $dbAdapter );
				},
				/**/
				'Shop\Model\FamiliesTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\FamiliesTable ( $dbAdapter );
				},
				'Shop\Model\FamilyHasFormatsTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\FamilyHasFormatsTable ( $dbAdapter );
				},
				'Shop\Model\FamilyFilesTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Shop\Model\FamilyFilesTable ( $dbAdapter );
				} 
		) 
);/**
 */

