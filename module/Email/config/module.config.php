<?php
return array (
		'factories' => array (
				'Email\Model\BlackListDomainTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Email\Model\BlackListDomainTable ( $dbAdapter );
				},
				'Email\Model\WhiteListDomainTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Email\Model\WhiteListDomainTable ( $dbAdapter );
				},
				'Email\Model\SubscriptionTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Email\Model\SubscriptionTable ( $dbAdapter );
				},
				'Email\Model\BlackListEmailTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Email\Model\BlackListEmailTable ( $dbAdapter );
				},
				'Email\Model\WhiteListEmailTable' => function ($sm) {
					$dbAdapter = $sm->get ( 'Adapter' );
					return new \Email\Model\WhiteListEmailTable ( $dbAdapter );
				},
				'Email\Model\ActivationTable' => function ($sm)
				{
					$dbAdapter = $sm->get('Adapter');
					return new \Email\Model\ActivationTable($dbAdapter);
				},
				
		),
);