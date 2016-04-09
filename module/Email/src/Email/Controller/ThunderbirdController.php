<?php

namespace Email\Controller;

/**
 * Consulta a lista pulica de dominios do Thunerbird
 * 
 * @author Claudio
 * @see https://developer.mozilla.org/en-US/docs/Mozilla/Thunderbird/Autoconfiguration Autoconfiguration in Thunderbird
 *      Thunderbird 3.1 and later (and 3.0 to some degree) includes mail account autoconfiguration
 *      functionality. The goal of autoconfiguration is to make it very easy for users to configure
 *      the connection of Thunderbird to their email servers. In many cases, people should be able to
 *      download and install Thunderbird, enter their real name, email address and password in the Account
 *      Setup Wizard and have a fully functioning mail client and get and send their mail as securely
 *      as possible.
 *     
 *     
 */
class ThunderbirdController extends \Useful\Controller\ControlController {
	/**
	 * Mozila ISP Database
	 * 
	 * @var unknown
	 */
	const ISPDB = 'https://autoconfig.thunderbird.net/v1.1/';
	/**
	 * Chega se um domain se encontra na base de domains do Thunderbird ISP
	 * 
	 * @param unknown $domain        	
	 */
	public function providers($domain) {
		// Set the configuration parameters
		$config = array (
				'adapter' => '\Zend\Http\Client\Adapter\Curl',
				'sslverifypeer'=> false
		);
		try{
		// Instantiate a client object
		$client = new \Zend\Http\Client ();
		$client->setOptions ( $config );
		$client->setUri(self::ISPDB . $domain);
		$headers = $client->getRequest()->getHeaders();
		$headers->addHeaderLine('Content-Type', 'text/xml; charset=utf-8');
		
		$response = $client->setHeaders($headers)
		->setMethod('GET')
		->setEncType('text/xml')
		->send();
		//Status code
		if($response->getStatusCode() == 200){
			$xml = $response->getBody();
			$source = \Zend\Json\Json::fromXml($xml);
			$response = \Zend\Json\Decoder::decode($source, \Zend\Json\Json::TYPE_ARRAY); 
			
			if(isset($response['clientConfig']['emailProvider']['domain'])){
				$providers = $response['clientConfig']['emailProvider']['domain'];
				 return $providers;
			}
		}
		}catch (\Exception $e){
			
		}
		return false;
	}
}