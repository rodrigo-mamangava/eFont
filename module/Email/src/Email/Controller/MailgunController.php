<?php

namespace Email\Controller;

use \Mailgun\Mailgun;
use Useful\Controller\ControlController;

/**
 * APIs do Mailgun
 *
 * @author Claudio
 */
class MailgunController extends \Useful\Controller\ControlController {
	protected $mgClient;
	protected $domain;
	protected $apiKey;
	protected $pubKey;
	protected $apiEndpoint;
	protected $apiVersion;
	protected $ssl = true;
	protected $from = 'noreply@qana.com.br';
	/**
	 *
	 * @param \Zend\ServiceManager\ServiceManager $sm        	
	 * @throws \Exception
	 */
	public function __construct($sm) {
		parent::__construct ( $sm );
		$config = $this->getConfig ();
		$mailgun = null;
		if ($config ['Mailgun']) {
			$mailgun = $config ['Mailgun'];
		} else {
			throw new \Exception ( 'Mailgun is not config' );
		}
		// Setters
		$this->apiKey = $mailgun ['apiKey'];
		$this->pubKey = $mailgun ['pubKey'];
		$this->apiEndpoint = $mailgun ['apiEndpoint'];
		$this->apiVersion = $mailgun ['apiVersion'];
		$this->ssl = $mailgun ['ssl'];
		$this->domain = $mailgun ['domain'];
		$this->from = $mailgun ['from'];
	}
	/**
	 * Instancia o Mailgun com private key
	 */
	public function startApiKey() {
		// Mailgun Class
		$this->mgClient = new Mailgun ( $this->apiKey, $this->apiEndpoint, $this->apiVersion, $this->ssl );
	}
	/**
	 * Instancia o Mailgun com o public key
	 */
	public function startPubKey() {
		// Mailgun Class
		$this->mgClient = new Mailgun ( $this->pubKey, $this->apiEndpoint, $this->apiVersion, $this->ssl );
	}
	/**
	 * Validate a single email address
	 *
	 * @param unknown $email        	
	 */
	public function validate($email) {
		// Issue the call to the client.
		$result = $this->mgClient->get ( "address/validate", array (
				'address' => $email 
		) );
		if (is_a ( $result, 'stdClass' )) {
			$http_response_body = $result->http_response_body;
			$http_response_code = $result->http_response_code;
			if ($http_response_code == 200) {
				return $http_response_body->is_valid;
			}
		}
		return false;
	}
	/**
	 * Envio de mensagens
	 * 
	 * @param unknown $to        	
	 * @param unknown $subject        	
	 * @param unknown $text        	
	 * @param string $html        	
	 * @throws \Exception
	 */
	public function sendMessage($to, $subject, $text, $html = null) {
		// Make the call to the client.
		$mail = array ();
		$mail ['from'] = $this->from;
		$mail ['to'] = $to;
		$mail ['subject'] = $subject;
		if ($text == null && $html == null) {
			throw new \Exception ( 'Please, Text and HTML cannot empty!' );
		}
		// TEXT PLAIN
		if (! is_null ( $text )) {
			$mail ['text'] = $text;
		}
		// HTML
		if (! is_null ( $html )) {
			$mail ['html'] = $html;
		}
		// Result
		$result = $this->mgClient->sendMessage ( $this->domain, $mail );
		
		if (is_a ( $result, 'stdClass' )) {
			$http_response_body = $result->http_response_body;
			$http_response_code = $result->http_response_code;
			if ($http_response_code == 200) {
				$response = array('id'=>isset($http_response_body->id)?$http_response_body->id:-1, 'message'=>isset($http_response_body->message)?$http_response_body->message:'');
				return $response;
			}
		}
		return false;
	}
	/**
	 * Template
	 * @param unknown $to
	 * @param unknown $title
	 * @param unknown $text
	 * @param unknown $path
	 * @param string $tpl
	 * @param string $SystemConfig
	 * @return string|Exception
	 */
	public function template($to, $subject, $data, $path, $tpl = 'default',$SystemConfig = null){
		//Template
		$tpl = (is_null($tpl) || $tpl == null)? 'default': $tpl;
		// TEMPLATE
		$view = new \Zend\View\Renderer\PhpRenderer();
		$resolver = new \Zend\View\Resolver\TemplateMapResolver();
		$resolver->setMap(array(
				'mailTemplate' => __DIR__ . '/../../../../AWS/view/layout/mail/' . $tpl . '.phtml'
		));
		$view->setResolver($resolver);
		$viewModel = new \Zend\View\Model\ViewModel();
		$viewModel->setVariables(array(
				'to' => $to,
				'title' => $subject,
				'content' => $data,
				'path' => $path,
				'SystemConfig'=>$SystemConfig,
				'inline'=>true
		));
		
		$viewModel->setTemplate('mailTemplate');
		$viewModel->setTerminal(true);
		// HTML
		$html = new \Zend\Mime\Part($view->render($viewModel, null, true));
		$html->type = "text/html";
		$html->charset = 'utf-8';
		$body = $html->getContent();
		
		return self::sendMessage($to, $subject, null, $body);
	}
	
	/**
	 * retorna a lista de eventos registrados
	 */
	public function events(){
		//Auxiliar
		$recipients = array();
		//SQL
		$queryString = array('event' => 'rejected OR failed');
		# Make the call to the client.
		$result = $this->mgClient->get("$this->domain/events", $queryString);
		#logger
		$this->logger($result, 'mailgun');
		
		if (is_a ( $result, 'stdClass' )) {
			$http_response_body = $result->http_response_body;
			$http_response_code = $result->http_response_code;
			if ($http_response_code == 200) {
				$items = isset($http_response_body->items)?$http_response_body->items:null;
				if(!is_null($items)){
					foreach($items as $item){
						$recipients[] = array('recipient'=>$item->recipient , 'event'=>$item->event);
					}
					//Return
					return $recipients;
				}
			}
		}
		return false;
	}
}