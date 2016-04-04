<?php
return array (
		'Project' => array (
				'name' => 'E-Font Shop',
				'host' => 'http://efont.qana.com.br/',
				'support' => 'noreply@qana.com.br',
				'secure_server' => '5QS986BhWqGRkxJGQ4g7Dkpd',
				'template_default' => 'default' 
		),
		'ACL_CUSTOMER' => array (),
		'Register' => array (
				'users_table' => "user",
				'password_column' => 'password',
				'username_column' => 'username',
				'name_column' => 'name',
				'email_column' => 'email',
				'id_column' => 'id',
				'company_id_column' => 'company_id',
				'privilege_type_column' => 'privilege_type_id',
				'two_factor' => 'two_factor',
				'two_factor_secret' => 'two_factor_secret',
				'image' => 'image' 
		),
		'AwsS3' => array (
				'user' => 'S3',
				'key' => 'AKIAJXXVFEVVLTJ2LV3Q',
				'secret' => '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',
				'bucket' => 'mmgv-efont',
				'contents' => 'portal/',
				'thumb' => 'portal/thumb/',
				'url' => 'https://s3-sa-east-1.amazonaws.com/mmgv-efont/portal/',
				
				'contests' => 'contests/files/',
				'contests_url' => 'https://s3-sa-east-1.amazonaws.com/mmgv-efont/contests/files/' 
		),
		'AwsSES' => array (
				'key' => 'AKIAJTUXXPJ7FYRGRUIQ',
				'secret' => 'AgkXBmzTLaNRpe4qUMFPC15rXjWFaB2A/3zbc2azatma',
				'from' => 'noreply@qana.com.br',
				'host' => 'email-smtp.us-west-2.amazonaws.com' 
		),
		'AwsSQS' => array (
				'key' => '',
				'secret' => '',
				'name' => 'mmgv-efont',
				'url' => 'https://sqs.us-east-1.amazonaws.com/115386414934/mmgv-efont',
				'point' => 'sqs.us-east-1.amazonaws.com',
				'arn' => '	arn:aws:sqs:us-east-1:115386414934:mmgv-efont',
				'region' => 'us-east-1' 
		),
		'Facebook' => array (
				/**
				 * Facebook API settings OAuth
				 * The client_id and client_secret can be found at https://developers.facebook.com/apps
				 * On the Facebook site set the Site URL to http://YOUR_HOSTNAME/
				 * The different scopes can be found at https://developers.facebook.com/docs/reference/api/permissions/
				 */
				'client_id' => "",
				'client_secret' => "",
				'redirect_uri' => "https://{host}/login/facebook",
				'scope' => "public_profile,email",
				'cookie' => 'true',
				'status' => 'true',
				'version' => 'v2.5',
				'display' => 'page',
				'auth_url' => 'https://www.facebook.com/dialog/oauth',
				'token_url' => 'https://graph.facebook.com/oauth/access_token' 
		),
		'Google' => array (
				'maps-key' => '',
				'maps-sensor' => 'true',
				'gcm-key' => '',
				'gcm-url' => 'https://android.googleapis.com/gcm/send',
				'place-key' => '',
				'place-sensor' => 'true',
				'place-url' => '',
				'analytics' => 'UA--2' 
		),
		'Apple' => array (
				'apns-production' => true,
				'apns-key-dev' => './config/autoload/ck.pem',
				'apns-key-prod' => './config/autoload/ck-prod.pem',
				'apns-url' => '',
				'apns-passphrase' => 'qwerty' 
		) 
);
	
		