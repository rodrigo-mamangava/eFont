<?php

namespace Email\Controller;

/**
 * Prevencao contra bounce emails
 *
 * @author Claudio
 */
class BounceController extends \Useful\Controller\ControlController {
	/**
	 * Validacao de enderecos e dominios de emails
	 * @param unknown $email
	 * @param string $SMTP
	 * @param string $MAILGUN
	 * @param string $THUNDERBIRD
	 * @throws \Exception
	 */
	public function prevent($email, $SMTP = false, $MAILGUN = false, $THUNDERBIRD = true) {
		// Expressao regular
		$regexp = self::regexp ( $email );
		if ($regexp !== true) {
			throw new \Exception ( 'INVALID EMAIL ADDRESS.' . " - [2004]" );
		}
		// Zend Validator EmailAddress
		$validator = self::validatorEmail ( $email );
		if ($validator !== true && is_array ( $validator )) {
			$explain = '';
			foreach ( $validator as $reason ) {
				$explain = $reason . ' , ';
			}
			$explain = rtrim ( $explain, ' , ' );
			
			throw new \Exception ( 'EMAIL IS INVALID:' . $explain . " - [2005]" );
		}
		// LISTEDs EMAIL
		// BackList Domain
		$BlackListEmail = new \Email\Controller\BlackListEmailController ( $this->getServiceLocator () );
		// Whitelist email
		$WhiteListEmail = new \Email\Controller\WhiteListEmailController ( $this->getServiceLocator () );
		$white_list_email = $WhiteListEmail->fetch ( $email );
		if (! $white_list_email) {
			$black_list_email = $BlackListEmail->fetch ( $email );
			if ($black_list_email !== false) {
				throw new \Exception ( 'EMAIL BLACKLISTED.' . " - [2006]" );
			}
		}elseif(isset($white_list_email['enrolled'])){
			return true;
		}
		// SUBSCRIPTIONS
		$Subscription = new \Email\Controller\SubscriptionController ( $this->getServiceLocator () );
		$unsubscribe = $Subscription->fetch ( $email );
		if ($unsubscribe !== false) {
			throw new \Exception ( 'EMAIL UNSUBSCRIBE.' . " - [2007]" );
		}
		// LISTEDs DOMAIN
		$domain = self::extractDomainEmail ( $email );
		// Whitelist Domain
		$WhiteListDomain = new \Email\Controller\WhiteListDomainController ( $this->getServiceLocator () );
		$white_list_domain = $WhiteListDomain->fetch ( $domain );
		if (! $white_list_domain) {
			// BackList Domain
			$BlackListDomain = new \Email\Controller\BlackListDomainController ( $this->getServiceLocator () );
			$black_list_domain = $BlackListDomain->fetch ( $domain );
			if ($black_list_domain !== false) {
				throw new \Exception ( 'DOMAIN BLACKLISTED.' . " - [2008]" );
			}
			// Thunderbird
			if ($THUNDERBIRD === true) {
				$Thunderbird = new \Email\Controller\ThunderbirdController ( $this->getServiceLocator () );
				$providers = $Thunderbird->providers ( $domain );
				if ($providers !== false && is_array ( $providers )) {
					$MAILGUN = $SMTP = false; // Desabilita o SMTP e Mailgun
					foreach ($providers as $provider){
						$WhiteListDomain->create ( $provider );
					}
				}
			}
			// SMTP Validation
			if ($SMTP === true) {
				try {
					$mx = self::validationSMTP ( $email );
					if ($mx === true) {
						$WhiteListDomain->create ( $domain );
					} else {
						$BlackListDomain->create ( $domain );
						throw new \Exception ( 'MX IS INVALID.' . " - [2009]" );
					}
				} catch ( \Exception $e ) {
				}
			}
		}
		// MAILGUN
		if ($MAILGUN === true) {
			$Mailgun = new \Email\Controller\MailgunController ( $this->getServiceLocator () );
			$Mailgun->startPubKey ();
			if (! $Mailgun->validate ( $email )) {
				$BlackListEmail->create ( $email );
				throw new \Exception ( 'COULD NOT VALIDATE YOUR EMAIL ADDRESS AT MOMENT.' . " - [2010]" );
			} else {
				$WhiteListDomain->create($domain);
				$WhiteListEmail->create($email);
			}
		}
		return true;
	}
	/**
	 * Validacao simples com regexp
	 *
	 * @param unknown $email        	
	 */
	public function regexp($email) {
		return \Validador\Controller\ValidadorController::isValidEmail ( $email );
	}
	/**
	 * Validacao com Zend Validator
	 *
	 * @param unknown $email        	
	 */
	public function validatorEmail($email) {
		$validator = new \Zend\Validator\EmailAddress ();
		$reasons = array ();
		if ($validator->isValid ( $email )) {
			// email appears to be valid
			return true;
		} else {
			// email is invalid; print the reasons
			foreach ( $validator->getMessages () as $message ) {
				$reasons [] = $message;
			}
			return $reasons;
		}
		return $reasons;
	}
	/**
	 * Validacao do SMTP
	 *
	 * @param unknown $email        	
	 */
	public function validationSMTP($email) {
		$SMTP_Validate_Email = new \Email\Controller\SMTPValidateEmail ( $email );
		$smtp_results = $SMTP_Validate_Email->validate ();
		if (isset ( $smtp_results [$email] )) {
			if ($smtp_results [$email] === true) {
				return true;
			}
		}
		return false;
	}
	/**
	 * Extract Domain from Email
	 *
	 * @param unknown $email        	
	 */
	public function extractDomainEmail($email) {
		// make sure we've got a valid email
		if (filter_var ( $email, FILTER_VALIDATE_EMAIL )) {
			// split on @ and return last value of array (the domain)
			$domain = array_pop ( explode ( '@', $email ) );
			// output domain
			return $domain;
		}
		return false;
	}
}