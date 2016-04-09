<?php
namespace AWS\Controller;

use Zend\Cache\Storage\Adapter\RedisOptions;
use Zend\Cache\Storage\Adapter\Redis;

class ElasticCacheController
{

    private static $__accessKey;
 // AWS Access key
    private static $__secretKey;
 // AWS Secret key
    private static $__endpoint;
 // AWS endpoint
    private static $__port;

    private static $__cache;
 // Server Connection
    private static $__timeout = 30;

    /**
     * Construtor da Classe
     * 
     * @param string $accessKey            
     * @param string $secretKey            
     * @param string $endpoint            
     * @param string $port            
     * @throws \Exception
     */
    public function __construct($accessKey = null, $secretKey = null, $endpoint = null, $port = null)
    {
        if ($accessKey !== null && $secretKey !== null && $endpoint !== null && $port !== null) {
            $this->setAuth($accessKey, $secretKey, $endpoint, $port);
        } else {
            throw new \Exception('Necessario informar os dados de conexao');
        }
    }

    /**
     * Seta as credencias para conexao
     * 
     * @param unknown $accessKey            
     * @param unknown $secretKey            
     * @param unknown $endpoint            
     * @param unknown $port            
     */
    public function setAuth($accessKey, $secretKey, $endpoint, $port)
    {
        // Settings
        self::$__accessKey = $accessKey;
        self::$__secretKey = $secretKey;
        self::$__endpoint = $endpoint;
        self::$__port = $port;
        // Server
        self::$__cache = $this->getCacheServer();
    }

    /**
     * Executa a Conexao com o servidor
     * 
     * @return \Zend\Cache\Storage\Adapter\Redis
     */
    private function getCacheServer()
    {
        /**
         * The configuration options are encapsulated in a class called RedisOptions
         * Here we setup the server configuration using the values from our config file
         */
        $redis_options = new RedisOptions();
        $redis_options->setServer(array(
            'host' => self::$__endpoint,
            'port' => self::$__port,
            'timeout' => self::$__timeout
        ));
        // /**
        // * This is not required, although it will allow to store anything that can be serialized by PHP in Redis
        // */
        // $redis_options->setLibOptions ( array (
        // Redis::OPT_SERIALIZER => Redis::SERIALIZER_PHP
        // ) );
        
        /**
         * We create the cache passing the RedisOptions instance we just created
         */
        $redis_cache = new Redis($redis_options);
        
        return $redis_cache;
    }

    /**
     * Adiciona um registro na memoria
     * 
     * @param unknown $key            
     * @param unknown $value            
     * @param unknown $seconds            
     */
    public function setAtCache($key, $value, $seconds)
    {
        return self::$__cache->setItem($key, $value);
    }

    /**
     * Retorna um item na memoria pela cache
     * 
     * @param unknown $key            
     */
    public function getAtCache($key)
    {
        return self::$__cache->getItem($key);
    }

    /**
     * Remove um item da memoria
     * 
     * @param unknown $key            
     */
    public function removeAtCache($key)
    {
        return self::$__cache->removeItem($key);
    }
}