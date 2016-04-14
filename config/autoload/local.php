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
				'users_table' => "user_system",
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
				'user' => 'APREPARAS3',
				'key' => 'AKIAJ5NGHSLMOGOBUFDA',
				'secret' => '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',
				'bucket' => 'aprepara',
				'contents' => 'contents/',
				'thumb' => 'imagem/',
				'url' => 'https://s3-us-west-2.amazonaws.com/aprepara/imagem/',
				'url_contents' => 'https://s3-us-west-2.amazonaws.com/aprepara/contents/',				
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
				'gcm-key' => 'AIzaSyDWTPdeMbkMVdLNGnCSZT-G7lf2ayjjVN0',
				'gcm-url' => 'https://android.googleapis.com/gcm/send',
				'place-key' => '',
				'place-sensor' => 'true',
				'place-url' => '',
				'analytics' => 'UA--2', // See footer.phtml too
				'server-key'=>'AIzaSyClS4L3YY1LJW5vj6FI9-IhdCm9qmV5oG8',
				'application'=>'affable-beach-118502',
				/**
				 * Google API settings OAuth
				 * The client_id and client_secret can be found at https://code.google.com/apis/console
				 * On the Google site set the Redirect URI to http://YOUR_HOSTNAME/login/google
				 */
				'client_id' => '',
				'client_secret' => '',
				'redirect_uri' => 'http://{HOST}/login/google',
				'scope' => 'https://www.googleapis.com/auth/userinfo.email',
				'auth_url' => 'https://accounts.google.com/o/oauth2/auth',
				'token_url' => 'https://accounts.google.com/o/oauth2/token',
				'grant_type' => 'authorization_code',
				'access_type' => 'online',
				'response_type' => 'code',
				'display' => 'page' 
		),
		'Apple' => array (
				'apns-production' => true,
				'apns-key-dev' => './config/autoload/ck.pem',
				'apns-key-prod' => './config/autoload/ck-prod.pem',
				'apns-url' => '',
				'apns-passphrase' => 'qwerty' 
		) 
);
	
		