<?php

namespace Useful\Controller;

/**
 * Encurtar uma URL
 * @author Claudio
 *        
 */
class ShortURLController extends ControlController {
	/**
	 * Encurtador de URL
	 * 
	 * @param unknown $long_url        	
	 */
	public function shortener($long_url) {
		// pegando as configuracoes
		$config = $this->getConfig ();
		$key = $config ["Google"] ["server-key"];
		$app = $config ["Google"] ["application"];
		// Encurtar
		$client = new \Google_Client ();
		$client->setApplicationName ( $app );
		$client->setDeveloperKey ( $key );
		
		$service = new \Google_Service_Urlshortener ( $client );
		
		$url = new \Google_Service_Urlshortener_Url ();
		$url->longUrl = $long_url;
		$short = $service->url->insert ( $url );
		
		return $short->id;
	}
}