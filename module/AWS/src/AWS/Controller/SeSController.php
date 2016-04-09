<?php
namespace AWS\Controller;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class SeSController
{

    private static $__accessKey;
 // AWS Access key
    private static $__secretKey;
 // AWS Secret key
    private static $__from;
 // AWS email from
    private static $__host;

    /**
     * Constructor, used if you're not calling the class statically
     *
     * @param string $accessKey
     *            Access key
     * @param string $secretKey
     *            Secret key
     * @param string $from            
     * @return void
     *
     */
    public function __construct($accessKey = null, $secretKey = null, $from = null, $host = null)
    {
        if ($accessKey !== null && $secretKey !== null && $from !== null && $host !== null)
            self::setAuth($accessKey, $secretKey, $from, $host);
    }

    /**
     * Set access information
     *
     * @param string $accessKey
     *            Access key
     * @param string $secretKey
     *            Secret key
     * @param string $from            
     * @param string $host            
     * @return void
     *
     */
    public static function setAuth($accessKey, $secretKey, $from, $host)
    {
        self::$__accessKey = $accessKey;
        self::$__secretKey = $secretKey;
        self::$__from = $from;
        self::$__host = $host;
    }

    /**
     * Envia o email usando a AWS SES
     *
     * @param unknown $to            
     * @param unknown $title            
     * @param unknown $text            
     */
    public function send($to, $title, $text)
    {
        $Message = new Message();
        $Message->addTo($to)
            ->addFrom(self::$__from, 'Quiz')
            ->setSubject($title)
            ->setBody($text);
        // Setup SMTP transport using LOGIN authentication
        $transport = new Smtp();
        $options = new SmtpOptions(array(
            'name' => self::$__host,
            'host' => self::$__host,
            'connection_class' => 'plain',
            'connection_config' => array(
                'username' => self::$__accessKey,
                'password' => self::$__secretKey,
                'ssl' => 'tls',
                'port' => 587
            )
        ));
        $transport->setOptions($options);
        try {
            return $transport->send($Message);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Envia um email HTML usando a AWS SES
     *
     * @param unknown $to            
     * @param unknown $title            
     * @param unknown $text            
     * @throws \Exception
     */
    public function sendHtml($to, $title, $text)
    {
        $message = new Message();
        $message->addTo($to)
            ->addFrom(self::$__from, 'Quiz')
            ->setSubject($title);
        // Setup SMTP transport using LOGIN authentication
        $transport = new SmtpTransport();
        $options = new SmtpOptions(array(
            'name' => self::$__host,
            'host' => self::$__host,
            'connection_class' => 'login',
            'connection_config' => array(
                'username' => self::$__accessKey,
                'password' => self::$__secretKey,
                'ssl' => 'tls',
                'port' => 587
            ),
            'port' => 587
        ));
        
        $html = new MimePart($text);
        $html->type = "text/html";
        
        $body = new MimeMessage();
        $body->addPart($html);
        $message->setBody($body);
        $transport->setOptions($options);
        
        try {
            return $transport->send($message);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Envia um email com template usando a AWS SES
     *
     * @param unknown $to            
     * @param unknown $title            
     * @param unknown $text            
     * @param unknown $path            
     * @param string $tpl            
     * @throws \Exception
     */
    public function sendTemplate($to, $title, $text, $path, $tpl = 'default',$SystemConfig = null)
    {
    	//Template
    	$tpl = (is_null($tpl) || $tpl == null)? 'default': $tpl;
    	//Message
        $message = new Message();
        $message->setEncoding("UTF-8");
        $message->addTo($to)
            ->addFrom(self::$__from, 'Quiz')
            ->setSubject($title);
        // Setup SMTP transport using LOGIN authentication
        $transport = new SmtpTransport();
        $options = new SmtpOptions(array(
            'name' => self::$__host,
            'host' => self::$__host,
            'connection_class' => 'login',
            'connection_config' => array(
                'username' => self::$__accessKey,
                'password' => self::$__secretKey,
                'ssl' => 'tls',
                'port' => 587
            ),
            'port' => 587
        ));
        
        // TEMPLATE
        $view = new \Zend\View\Renderer\PhpRenderer();
        $resolver = new \Zend\View\Resolver\TemplateMapResolver();
        $resolver->setMap(array(
            'mailTemplate' => __DIR__ . '/../../../view/layout/mail/' . $tpl . '.phtml'
        ));
        $view->setResolver($resolver);
        $viewModel = new \Zend\View\Model\ViewModel();
        $viewModel->setVariables(array(
            'to' => $to,
            'title' => $title,
            'content' => utf8_encode($text),
            'path' => $path,
        	'SystemConfig'=>$SystemConfig,
        ));
        $viewModel->setTemplate('mailTemplate');
        $viewModel->setTerminal(true);
        // HTML
        $html = new MimePart($view->render($viewModel, null, true));
        $html->type = "text/html";
        $html->charset = 'utf-8';
        
        $body = new MimeMessage();
        $body->setParts(array(
            $html
        ));
        $message->setBody($body);
        $transport->setOptions($options);
        try {
            $transport->send($message);
            return true; // Send nao retorna nada, entao se nao deu excercao, ta tudo ok
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return false;
    }
}