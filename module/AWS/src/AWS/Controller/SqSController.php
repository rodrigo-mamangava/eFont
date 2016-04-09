<?php
namespace AWS\Controller;

class SqSController
{

    private static $__accessKey;
 // AWS Access key
    private static $__secretKey;
 // AWS Secret key
    private static $__queue;
 // AWS email queue
    private static $__host;

    /**
     * Constructor, used if you're not calling the class statically
     *
     * @param string $accessKey
     *            Access key
     * @param string $secretKey
     *            Secret key
     * @param string $queue            
     * @return void
     *
     */
    public function __construct($accessKey = null, $secretKey = null, $queue = null, $host = null)
    {
        if ($accessKey !== null && $secretKey !== null && $queue !== null && $host !== null)
            self::setAuth($accessKey, $secretKey, $queue, $host);
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
        self::$__queue = $from;
        self::$__host = $host;
    }

    /**
     * Envia uma mensagem para a SQS
     *
     * @param unknown $message            
     */
    public function send($message)
    {
        $SQS = new \ZendService\Amazon\Sqs\Sqs(self::$__accessKey, self::$__secretKey);
        $queue_url = $SQS->create(self::$__queue);
        return $SQS->send($queue_url, $message);
    }

    /**
     * Recebe e deleta as mensagens em uma fila
     *
     * @param unknown $count            
     * @return multitype:
     */
    public function receive($count)
    {
        $SQS = new \ZendService\Amazon\Sqs\Sqs(self::$__accessKey, self::$__secretKey);
        $queue_url = $SQS->create(self::$__queue);
        // Itens
        $rs = array();
        // Recebendo
        $messagens = $SQS->receive($queue_url, $count);
        foreach ($messagens as $message) {
            $rs[] = $message['body'];
            // Remocao da fila
            $SQS->deleteMessage($queue_url, $message['handle']);
        }
        // Retorno
        return $rs;
    }
}