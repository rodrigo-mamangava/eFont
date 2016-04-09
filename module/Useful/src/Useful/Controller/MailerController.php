<?php

namespace Useful\Controller;

use Useful\Controller\ControlController;
use Validador\Controller\ValidadorController;
use Zend\Json\Json;
use Zend\Math\Rand;

class MailerController extends ControlController {
	/**
	 * Envio de propaganda com multiplos dados
	 * @param unknown $email
	 * @param unknown $title
	 * @param unknown $message
	 * @param unknown $data
	 * @param string $template
	 */
	public function sendMailAdsData($email, $title, $message, $data, $template = 'template2') {
		// Pegando as configuracoes
		$config = $this->getConfig ();
		// Classe responsavel pelo envio
		$SeS = new \AWS\Controller\SeSController ( $config ["AwsSES"] ["key"], $config ["AwsSES"] ["secret"], $config ["AwsSES"] ["from"], $config ["AwsSES"] ["host"] );
		$merge = array_merge($config ["Project"], $data);
		
		return $SeS->sendTemplate ( $email, $title, $message, $config ["Project"] ["host"], $template, $merge);
	}

	/**
	 * Email para novo cliente
	 *
	 * @param unknown $name
	 * @param unknown $email
	 * @param unknown $secure
	 * @param string $language
	 */
	public function sendMailActivation($name, $email, $secure, $language = 'pt_BR')
	{
		// Pegando as configuracoes
		$config = $this->getConfig();
		$this->setForceLocale($language);
		// Classe responsavel pelo envio
		$SeS = new \AWS\Controller\SeSController($config["AwsSES"]["key"], $config["AwsSES"]["secret"], $config["AwsSES"]["from"], $config["AwsSES"]["host"]);
		$title = sprintf($this->getTranslate()->translate('Welcome to %s!'),$config["Project"]["name"]);
		$message = sprintf("%s,", $name);
		$message .= "<br/><br/>";
		$message .= utf8_decode(sprintf($this->getTranslate()->translate("Thank you for registering in the %s. Please click the link below to activate your account."),$config["Project"]["name"]));
		$message .= "<br/><br/>";
		$message .= '<a href="' . $config["Project"]["host"] . sprintf($config["Project"]["activate"], $secure) . '">' . $this->getTranslate()->translate('Activate Account') . '</a>';
		$message .= "<br/><br/>";
		$message .= $this->getTranslate()->translate("Thanks,");
		return $SeS->sendTemplate($email, $title, $message, $config["Project"]["host"], 'template2', $config["Project"]);
	}
	
	/**
	 * Email para novo cliente
	 *
	 * @param unknown $name
	 * @param unknown $email
	 * @param string $language
	 */
	public function sendMailNewCustomer($name, $email, $language = 'pt_BR')
	{
		// Pegando as configuracoes
		$config = $this->getConfig();
		$this->setForceLocale($language);
		// Classe responsavel pelo envio
		$SeS = new \AWS\Controller\SeSController($config["AwsSES"]["key"], $config["AwsSES"]["secret"], $config["AwsSES"]["from"], $config["AwsSES"]["host"]);
		$title = sprintf($this->getTranslate()->translate('Welcome to %s!'),$config["Project"]["name"]);
		$message = sprintf("%s,", $name);
		$message .= "<br/><br/>";
		$message .= sprintf(utf8_decode($this->getTranslate()->translate("Thank you for registering in the %s App. Your account has been created and now you can access the Application in your smartphone (Android or iPhone ) or website.")),$config["Project"]["name"]);
		$message .= "<br/><br/>";
		$message .= $this->getTranslate()->translate("Thanks,");
		// return $SeS->sendHtml ( $username, $title, $message );
		return $SeS->sendTemplate($email, $title, $message, $config["Project"]["host"],'template2', $config["Project"]);
	}
	
	/**
	 * Email do formulario de contato
	 *
	 * @param unknown $name
	 * @param unknown $email
	 * @param unknown $subject
	 * @param unknown $text
	 */
	public function sendMailContact($name, $email, $subject, $text)
	{
		// Pegando as configuracoes
		$config = $this->getConfig();
		// Classe responsavel pelo envio
		$SeS = new \AWS\Controller\SeSController($config["AwsSES"]["key"], $config["AwsSES"]["secret"], $config["AwsSES"]["from"], $config["AwsSES"]["host"]);
		$title = sprintf($this->getTranslate()->translate('[%s] Contact Form '),$config["Project"]["name"]);
		$message = "Name: " . $name;
		$message .= "<br/><br/>";
		$message .= "Email: " . $email;
		$message .= "<br/><br/>";
		$message .= "Subject: " . $subject;
		$message .= "<br/><br/>";
		$message .= "Message: " . $text;
	
		return $SeS->sendTemplate($config["Project"]["support"], $title, $message, $config["Project"]["host"],'template2', $config["Project"]);
	}
	
	/**
	 * Envio email com dados para reset da senha
	 *
	 * @param unknown $username
	 * @param unknown $hash
	 */
	public function sendMailResetPassword($username, $hash, $language = 'pt_BR')
	{
		// Pegando as configuracoes
		$config = $this->getConfig();
		$this->setForceLocale($language);
		// Classe responsavel pelo envio
		$SeS = new \AWS\Controller\SeSController($config["AwsSES"]["key"], $config["AwsSES"]["secret"], $config["AwsSES"]["from"], $config["AwsSES"]["host"]);
		$title = sprintf($this->getTranslate()->translate('Reset your %s password '),$config["Project"]["name"]);
		$message = sprintf($this->getTranslate()->translate("Hello %s, "), $username);
		$message .= "<br/><br/>";
		$message .= utf8_decode(sprintf($this->getTranslate()->translate("We received a request to change your %s account password. Reset your password within 24 hours."),$config["Project"]["name"]));
		$message .= "<br/><br/>";
		$url_reset = rtrim($config["Project"]["host"],'/');
		$message .= '<a rel="nofollow" target="_blank" href="' . sprintf("%s/reset?hash=%s", $url_reset, $hash) . '" style="text-decoration:none;"><span style="color:#27a6e1;">' . strtoupper($this->getTranslate()->translate("resetting your password")) . '</span></a>';
		$message .= "<br/><br/>";
		$message .= $this->getTranslate()->translate("Thanks,");
		$message .= "<br/>";
		//$message .= sprintf($this->getTranslate()->translate("The %s"),$config["Project"]["name"]);
	
		return $SeS->sendTemplate($username, $title, $message, $config["Project"]["host"],'template2', $config["Project"]);
	}
	
	/**
	 * Template envio de email com nova senha
	 *
	 * @param unknown $username
	 * @param unknown $password
	 */
	public function sendMailNewPassword($username, $password, $language = 'pt_BR')
	{
		// Pegando as configuracoes
		$config = $this->getConfig();
		$this->setForceLocale($language);
		// Classe responsavel pelo envio
		$SeS = new \AWS\Controller\SeSController($config["AwsSES"]["key"], $config["AwsSES"]["secret"], $config["AwsSES"]["from"], $config["AwsSES"]["host"]);
		$title = sprintf($this->getTranslate()->translate('Revision to Your %s Account'),$config["Project"]["name"]);
		$message = sprintf($this->getTranslate()->translate("Thanks for visiting %s! Per your request, we have successfully changed your password."),$config["Project"]["name"]);
		$message .= "<br/><br/>";
		$message .= sprintf($this->getTranslate()->translate("Your new password is: %s"), $password);
		$message .= "<br/><br/>";
		$message .= $this->getTranslate()->translate("Should you need to contact us for any reason, you need send mail with the name and e-mail address associated with your account.");
		$message .= "<br/><br/>";
		$message .= $this->getTranslate()->translate("Thanks,");
		$message .= "<br/>";
		// $message .= sprintf($this->getTranslate()->translate("The %s"),$config["Project"]["name"]);
	
		return $SeS->sendTemplate($username, $title, $message, $config["Project"]["host"],'template2', $config["Project"]);
	}
}