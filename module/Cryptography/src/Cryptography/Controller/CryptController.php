<?php
namespace Cryptography\Controller;

use Zend\Crypt\BlockCipher;

class CryptController
{

    const IDENTITY_KEY = "ATzIL8m6KblQ33WD";

    /**
     * Realiza a crypt de acordo com a chave
     *
     * @param String $decrypted            
     * @param boolean $base64            
     * @return string
     */
    public static function encrypt($decrypted, $base64 = false)
    {
        $cipher = BlockCipher::factory('mcrypt', array(
            'algorithm' => 'aes'
        ));
        $cipher->setKey(self::IDENTITY_KEY);
        $encrypted = $cipher->encrypt($decrypted);
        // Base 64
        if ($base64) {
            $encrypted = base64_encode($encrypted);
        }
        return $encrypted;
    }

    /**
     * Realiza a tentativa de descypt
     *
     * @param String $encrypted            
     * @param boolean $base64            
     * @return Ambigous <string, boolean>
     */
    public static function decrypt($encrypted, $base64 = false)
    {
        // Base 64
        if ($base64) {
            $encrypted = base64_decode($encrypted);
        }
        
        // Funct
        $cipher = BlockCipher::factory('mcrypt', array(
            'algorithm' => 'aes'
        ));
        $cipher->setKey(self::IDENTITY_KEY);
        $decrypted = $cipher->decrypt($encrypted);
        return $decrypted;
    }

    /**
     * Conceder o token de sessao criptografado
     *
     * @param int $user_id            
     * @param string $role            
     * @param string $ip            
     * @return String $encrypted
     */
    public static function grantCryptToken($user_id, $uuid, $company_id, $role, $ip, $local = 'pt_BR', $version = 1)
    {
        $data['uniqid'] = uniqid() . '-' . $uuid;
        $data['expire'] = time() + (60 * 60 * 8760);
        $data['user_id'] = $user_id;
        $data['company_id'] = $company_id;
        $data['ip'] = $ip;
        $data['role'] = $role;
        $data['local'] = $local;
        $data['version'] = $version;
        return self::encrypt(\Zend\Json\Json::encode($data));
    }

    /**
     * Retorna os atributos de um objeto ja decodificado
     *
     * @param String $decrypted            
     * @throws \Exception
     * @return Array
     */
    public static function getPrivilegesToken($decrypted)
    {
        try {
            $data = \Zend\Json\Json::decode($decrypted, \Zend\Json\Json::TYPE_ARRAY);
        } catch (Exception $e) {
            throw new \Exception('Decoding failing.');
        }
        if (is_array($data)) {
            return $data;
        } else {
            throw new \Exception('Failed in checking privileges');
        }
    }

    /**
     * Gera um codigo seguro
     *
     * @param unknown $PIN            
     * @param unknown $first_part            
     * @param unknown $second_part            
     * @return unknown
     */
    public static function createSecureCode($PIN, $first_part, $second_part)
    {
        // Criando
        // SOMA DOS DIGITOS DO PIN
        $A = array_sum(str_split($PIN));
        // SOMA DOS ULTIMOS 4 DIGITOS DO MSISDN
        $L = substr($second_part, - 4);
        $B = array_sum(str_split($L));
        // SE PIN FOR PAR, USAR OS ULTIMOS 4 DIGITOS SEQUENCIAL (9916)
        // SE PIN FOR IMPAR, USAR OS ULTIMOS 4 DIGITOS INVERTIDO (6199)
        $C = ($PIN % 2 == 0) ? $L : strrev($L);
        // NOME PROJECTO INVERTIDO
        $D = 'fabulamur';
        // Resultado
        $sha1 = $A . $B . $C . $D;
        // Crypt
        $crypt = sha1($sha1);
        return $crypt;
    }
}