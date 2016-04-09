<?php

namespace Email\Controller;

use Useful\Controller\ControlController;
use Validador\Controller\ValidadorController;
use Zend\Json\Json;
use Zend\Math\Rand;

class MailerController extends ControlController {
	
	/**
	 * Envio de propaganda com multiplos dados
	 *
	 * @param unknown $email        	
	 * @param unknown $title        	
	 * @param unknown $message        	
	 * @param unknown $data        	
	 * @param string $template        	
	 */
	public function sendMailAdsData($email, $title, $message, $data, $template = 'default', $route = 'SES') {
		// Pegando as configuracoes
		$config = $this->getConfig ();
		$merge = array_merge ( $config ["Project"], $data );
		return self::route ( $email, $title, $message, $config ["Project"]["host"], $template, $merge, $config, $route );
	}
	/**
	 * Email para novo cliente
	 *
	 * @param unknown $name        	
	 * @param unknown $email        	
	 * @param unknown $secure        	
	 * @param string $language        	
	 */
	public function sendMailActivation($name, $email, $secure, $language = 'pt_BR') {
		// Pegando as configuracoes
		$config = $this->getConfig ();
		$this->setForceLocale ( $language );
		
		$title = sprintf ( $this->getTranslate()->translate ( 'Welcome to %s!' ), $config ["Project"] ["name"] );
		$message = sprintf ( "%s,", $name );
		$message .= "<br/><br/>";
		$message .= utf8_decode ( sprintf ( $this->getTranslate()->translate ( "Thank you for registering in the %s. Please click the link below to activate your account." ), $config ["Project"] ["name"] ) );
		$message .= "<br/><br/>";
		$message .= '<a href="' . $config ["Project"] ["host"] . sprintf ( $config ["Project"] ["activate"], $secure ) . '">' . $this->getTranslate()->translate ( 'Activate Account' ) . '</a>';
		$message .= "<br/><br/>";
		$message .= $this->getTranslate()->translate ( "Thanks," );
		
		return self::route ( $email, $title, $message, $config ["Project"] ["host"], $config ["Project"] ["template_default"], $config ["Project"], $config );
	}
	/**
	 * Email para novo cliente
	 *
	 * @param unknown $name        	
	 * @param unknown $email        	
	 * @param string $language        	
	 */
	public function sendMailNewCustomer($name, $email, $language = 'pt_BR') {
		// Pegando as configuracoes
		$config = $this->getConfig ();
		$this->setForceLocale ( $language );
		
		$title = sprintf ( $this->getTranslate()->translate ( 'Welcome to %s!' ), $config ["Project"] ["name"] );
		$message = sprintf ( "%s,", $name );
		$message .= "<br/><br/>";
		$message .= sprintf ( utf8_decode ( $this->getTranslate()->translate ( "Thank you for registering in the %s App. Your account has been created and now you can access the Application in your smartphone (Android or iPhone ) or website." ) ), $config ["Project"] ["name"] );
		$message .= "<br/><br/>";
		$message .= $this->getTranslate()->translate ( "Thanks," );
		
		return self::route ( $email, $title, $message, $config ["Project"] ["host"], $config ["Project"] ["template_default"], $config ["Project"], $config );
	}
	
	/**
	 * Email do formulario de contato
	 *
	 * @param unknown $name        	
	 * @param unknown $email        	
	 * @param unknown $subject        	
	 * @param unknown $text        	
	 */
	public function sendMailContact($name, $email, $subject, $text) {
		// Pegando as configuracoes
		$config = $this->getConfig ();
		
		$title = sprintf ( $this->getTranslate()->translate ( '[%s] Contact Form ' ), $config ["Project"] ["name"] );
		$message = "Name: " . $name;
		$message .= "<br/><br/>";
		$message .= "Email: " . $email;
		$message .= "<br/><br/>";
		$message .= "Subject: " . $subject;
		$message .= "<br/><br/>";
		$message .= "Message: " . $text;
		
		return self::route ( $config ["Project"] ["support"], $title, $message, $config ["Project"] ["host"], $config ["Project"] ["template_default"], $config ["Project"], $config );
	}
	
	/**
	 * Envio email com dados para reset da senha
	 *
	 * @param unknown $username        	
	 * @param unknown $hash        	
	 */
	public function sendMailResetPassword($username, $hash, $language = 'pt_BR') {
		// Pegando as configuracoes
		$config = $this->getConfig ();
		$this->setForceLocale ( $language );
		
		$title = sprintf ( $this->getTranslate()->translate ( 'Reset your %s password ' ), $config ["Project"] ["name"] );
		$message = sprintf ( $this->getTranslate()->translate ( "Hello %s, " ), $username );
		$message .= "<br/><br/>";
		$message .= utf8_decode ( sprintf ( $this->getTranslate()->translate ( "We received a request to change your %s account password. Reset your password within 24 hours." ), $config ["Project"] ["name"] ) );
		$message .= "<br/><br/>";
		$url_reset = rtrim ( $config ["Project"] ["host"], '/' );
		$message .= '<a rel="nofollow" target="_blank" href="' . sprintf ( "%s/reset?hash=%s", $url_reset, $hash ) . '" style="text-decoration:none;"><span style="color:#27a6e1;">' . strtoupper ( $this->getTranslate()->translate ( "resetting your password" ) ) . '</span></a>';
		$message .= "<br/><br/>";
		$message .= $this->getTranslate()->translate ( "Thanks," );
		$message .= "<br/>";
		
		return self::route ( $username, $title, $message, $config ["Project"] ["host"], $config ["Project"] ["template_default"], $config ["Project"], $config );
	}
	
	/**
	 * Template envio de email com nova senha
	 *
	 * @param unknown $username        	
	 * @param unknown $password        	
	 */
	public function sendMailNewPassword($username, $password, $language = 'pt_BR') {
		// Pegando as configuracoes
		$config = $this->getConfig ();
		$this->setForceLocale ( $language );
		$title = sprintf ( $this->getTranslate()->translate ( 'Revision to Your %s Account' ), $config ["Project"] ["name"] );
		$message = utf8_decode(sprintf ( $this->getTranslate()->translate ( "Thanks for visiting %s! Per your request, we have successfully changed your password." ), $config ["Project"] ["name"] ));
		$message .= "<br/><br/>";
		$message .= utf8_decode(sprintf ( $this->getTranslate()->translate ( "Your new password is: %s" ), $password ));
		$message .= "<br/><br/>";
		$message .= utf8_decode($this->getTranslate()->translate ( "Should you need to contact us for any reason, you need send mail with the name and e-mail address associated with your account." ));
		$message .= "<br/><br/>";
		$message .= $this->getTranslate()->translate( "Thanks," );
		$message .= "<br/>";
		
		return self::route ( $username, $title, $message, $config ["Project"] ["host"], $config ["Project"] ["template_default"], $config ["Project"], $config );
	}
	/**
	 * Envio de notificacao
	 * @param unknown $email
	 * @param unknown $id_sys_customer
	 * @param unknown $id_service_orders
	 */
	public function sendNotification($email, $id_sys_customer, $id_service_orders, $language = 'pt_BR'){
		exit(1);
		$config = $this->getConfig ();
		$this->setForceLocale ( $language );
		$message = $title = $this->getTranslate()->translate ( '[Quiz] Notification Service');
		//Conteudo
		$ServiceOrderController = new \Application\Controller\ServiceOrderController();
		$ServiceOrderController->setServiceLocator($this->getServiceLocator());
		$data['Form'] = $ServiceOrderController->generalDataOS($id_service_orders, $id_sys_customer);
		//Envio
		return self::route ( $email, $title, $message, $config ["Project"] ["host"], 'notification', $data, $config );
	}
	/**
	 * Define a rota de envio das mensagens
	 * 
	 * @param unknown $to        	
	 * @param unknown $subject        	
	 * @param unknown $message        	
	 * @param unknown $path        	
	 * @param unknown $tpl        	
	 * @param unknown $data        	
	 * @param unknown $config        	
	 * @param string $route        	
	 * @throws \Exception
	 */
	public function route($to, $subject, $message, $path, $tpl, $data, $config, $route = 'SES') {
		switch ($route) {
			case 'SES' :
				// Classe responsavel pelo envio
				$SeS = new \AWS\Controller\SeSController ( $config ["AwsSES"] ["key"], $config ["AwsSES"] ["secret"], $config ["AwsSES"] ["from"], $config ["AwsSES"] ["host"] );
				return $SeS->sendTemplate ( $to, $subject, $message, $path, $tpl, $data );
				break;
			
			case 'MAILGUN' :
				$Mailgun = new \Email\Controller\MailgunController ( $this->getServiceLocator () );
				$Mailgun->startApiKey();
				return $Mailgun->template($to, $subject, $data, $path, $tpl, $config);
				break;
		}
		throw new \Exception ( 'UNKNOWN ROUTE' );
	}
}