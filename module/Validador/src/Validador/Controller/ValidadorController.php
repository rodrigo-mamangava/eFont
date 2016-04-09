<?php

namespace Validador\Controller;

class ValidadorController {
	
	/**
	 * Verifica se a string informada contem somente numeros
	 *
	 * @param Stirng $value,
	 *        	valor a ser verificado
	 * @return Boolean
	 */
	public static function isValidDigits($value) {
		$validator = new \Zend\Validator\Digits ();
		return $validator->isValid ( $value );
	}
	/**
	 * Compara dois numeros
	 * 
	 * @param unknown $smaller        	
	 * @param unknown $larger        	
	 * @return Boolean, TRUE se o maior >= menor, FALSE se menor > maior
	 */
	public static function ifDigitsComparison($a, $b) {
		
		if (( int ) $a == ( int ) $b){
			return true;
		}elseif (( int ) $a > ( int ) $b){
			return false;
		}elseif (( int ) $a < ( int ) $b){
			return true;
		}
	}
	/**
	 * Verifica se array contem somente numeros
	 * 
	 * @param unknown $arr        	
	 */
	public static function isValidArrayDigits($arr) {
		foreach ( $arr as $item ) {
			if (! self::isValidDigits ( $item )) {
				return false;
				break;
			}
		}
		return true;
	}
	/**
	 * Checa se um arquivo foi enviado
	 * 
	 * @param unknown $file        	
	 */
	public static function isValidSetFile($file) {
		return (isset ( $file ) && $file ['error'] != UPLOAD_ERR_NO_FILE);
	}
	/**
	 * Verifica se o valor informado esta entre dois outros valores.
	 *
	 * @param String $value,
	 *        	valor a ser verificado
	 * @param Int $min,
	 *        	menor valor permitido
	 * @param Int $max,
	 *        	maior valor permitido
	 * @return Boolean
	 */
	public static function isValidBetweenDigits($value, $min = 1, $max = 2) {
		if (self::isValidDigits ( $value )) {
			$validator = new \Zend\Validator\Between ( array (
					'min' => $min,
					'max' => $max 
			) );
			return $validator->isValid ( $value );
		}
		return false;
	}
	/**
	 * Verifica se contem uma string no formato valido de um json
	 *
	 * @param unknown $string        	
	 * @return boolean
	 */
	public static function isValidJson($string) {
		json_decode ( $string );
		return (json_last_error () == JSON_ERROR_NONE);
	}
	
	/**
	 * Verifica se o valor informado e vazio/null
	 *
	 * @param String $value,
	 *        	valor a ser verificado
	 * @return Boolean, FALSE se esta vazio e TRUE se nao esta vazio
	 */
	public static function isValidNotEmpty($value = null) {
		// Returns false on 0 or '0'
		$validator = new \Zend\Validator\NotEmpty ( array (
				\Zend\Validator\NotEmpty::INTEGER,
				\Zend\Validator\NotEmpty::ZERO 
		) );
		return $validator->isValid ( $value );
	}
	
	/**
	 * Gera uma senha de acordo com a pre formatacao
	 *
	 * @return Ambigous <string, string|null>
	 */
	public static function createPassword() {
		return \Zend\Math\Rand::getString ( 8, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' );
	}
	
	/**
	 * Gerar uma sequencia de numero
	 *
	 * @param number $length        	
	 */
	public static function createPin($length = 6) {
		return \Zend\Math\Rand::getString ( $length, '0123456789' );
	}
	
	/**
	 * Remove qualquer caractere que nao esteja na lista de permitidos
	 *
	 * @param unknown $string        	
	 * @return mixed
	 */
	public static function removeAllCharacters($string) {
		$string = preg_replace ( "/[\\n\\r\\t\\s]+/", "", rtrim ( trim ( $string ) ) );
		$string = preg_replace ( "/&([a-z])[a-z]+;/i", "$1", htmlentities ( $string, ENT_NOQUOTES ) );
		$string = preg_replace ( "/[^a-zA-Z0-9]/", "", $string );
		return $string;
	}
	/**
	 * Remove caracteres em brancos e espacos
	 * 
	 * @param unknown $string        	
	 */
	public static function removeBlank($string) {
		return str_replace ( array (
				' ',
				'\s',
				'\t' 
		), '', $string );
	}
	/**
	 * Verifica se uma string esta entre um comprimento definido.
	 *
	 * @param String $value,
	 *        	valor a ser verificado
	 * @param Int $min,
	 *        	Define o tamanho minimo permitido para uma string.
	 * @param Int $max,
	 *        	Define o tamanho maximo permitido para uma string.
	 * @return Boolean
	 */
	public static function isValidStringLength($value, $min = 5, $max = 15) {
		if (self::isValidNotEmpty ( $value )) {
			$validator = new \Zend\Validator\StringLength ( array (
					'min' => $min,
					'max' => $max 
			) );
			return $validator->isValid ( $value );
		}
		return false;
	}
	
	/**
	 * Verifica se o valor informado e uma data dentro do formato permitido
	 *
	 * @param String $value,
	 *        	valor a ser verificado
	 * @param String $format,
	 *        	formato de data para validacao. T
	 * @see This param $format accepts format as specified in the standard PHP function date() http://php.net/manual/en/function.date.php
	 * @return Boolean
	 */
	public static function isValidDate($value, $format = 'Y-m-d') {
		if (self::isValidNotEmpty ( $value ) && self::isValidStringLength ( $value, 10, 10 )) {
			$validator = new \Zend\Validator\Date ();
			$validator->setFormat ( $format );
			return $validator->isValid ( $value );
		}
		return false;
	}
	/**
	 * Validacao de hora
	 *
	 * @see http://www.mkyong.com/regular-expressions/how-to-validate-time-in-24-hours-format-with-regular-expression/
	 * @param string $time        	
	 * @return boolean
	 */
	public static function isValidTime($time) {
		$validator = new \Zend\Validator\Regex ( '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/' );
		return $validator->isValid ( $time );
	}
	/**
	 * Verifica se um valor se encontra dentro do padrao de uma expressao regular pre definida
	 *
	 * @param String $value,
	 *        	valor a ser verificado
	 * @param String $const,
	 *        	tipo da expressao
	 * @return Boolean
	 */
	public static function isValidRegexp($value, $const = 'resultado') {
		if (self::isValidNotEmpty ( $value )) {
			$regexp = null;
			switch ($const) {
				case 'resultado' :
					
					// Expressao regular para validacao de resultado
					$regexp = "^([0-9])*((-)?([0-9])+)*$";
					break;
				case 'date_time' :
					return preg_match ( '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value );
					break;
				case 'acronym_bar_name' :
					return self::isValidAcronymBarName ( $value );
					break;
				case 'base64' :
					return base64_decode ( $value );
					break;
				case 'color' :
					return preg_match ( '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $value );
					break;
				case 'phone' :
					return preg_match ( '/^(\+(1)?[0-9]{2,3})?([-.\s]?[0-9]{2,3})?[-.\s]?[0-9]{4,5}[-.\s]?[0-9]{4,5}$/', $value );
					break;
				default :
					$regexp = null;
			}
			// Verificando expressao e valores
			return @ereg ( $regexp, $value );
		}
		return false;
	}
	
	/**
	 * Valida de string contem um nome seguido de barra e terminando com uma sigla de dois caracteres
	 *
	 * @param String $string        	
	 * @return boolean
	 */
	public static function isValidAcronymBarName($string) {
		$validator = new \Zend\Validator\Regex ( '/[A-Za-z\s]+\/[A-Za-z0-9]{2}(\s)?/' );
		return $validator->isValid ( $string );
	}
	
	/**
	 * Verifica se o valor se encontra dentro do padrao do username esperado
	 *
	 * @param String $username,
	 *        	nome do usuario
	 * @return Boolean , TRUE se dentro do padrao e false fora do padrao
	 */
	public static function isValidUsername($username) {
		$validator = new \Zend\Validator\Regex ( '/^[A-Za-z]+[._-]{0,1}[A-Za-z0-9]+$/' );
		return $validator->isValid ( $username );
	}
	
	/**
	 * Valida uma senha segundo a expressao regular pre definida
	 *
	 * @param String $senha        	
	 * @return Boolean , TRUE se dentro do padrao e false fora do padrao
	 */
	public static function isValidSenha($senha) {
		$validator = new \Zend\Validator\Regex ( '/^[A-Za-z0-9@#$%&*?!.]{8,40}$/' );
		return $validator->isValid ( $senha );
	}
	
	/**
	 * Valida uma data e hora segundo a expressao regular pre definida
	 *
	 * @param String $senha        	
	 * @return Boolean , TRUE se dentro do padrao e false fora do padrao
	 */
	public static function isValidDateBrazil($date) {
		$validator = new \Zend\Validator\Regex ( '/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}\s[0-9]{2}:[0-9]{2}$/' );
		return $validator->isValid ( $date );
	}
	
	/**
	 * Valida uma url segundo a expressao regular pre definida
	 *
	 * @param String $senha        	
	 * @return Boolean , TRUE se dentro do padrao e false fora do padrao
	 */
	public static function isValidURL($url) {
		$validator = new \Zend\Validator\Regex ( '/^((http|https)\:\/\/)?(www\.)?[a-zA-Z0-9-_]+\.([a-zA-Z0-9]+\.)?([a-zA-Z0-9]+)([A-za-z0-9-_.\&\/\?\=]+)?$/' );
		return $validator->isValid ( $url );
	}
	
	/**
	 * Valida uma conta de email segundo a expressao regular pre definida
	 *
	 * @param String $senha        	
	 * @return Boolean , TRUE se dentro do padrao e false fora do padrao
	 */
	public static function isValidEmail($mail) {
		$validator = new \Zend\Validator\Regex ( '/^[a-zA-Z0-9._-]+@([a-zA-Z0-9-]+\.)?[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/' );
		return $validator->isValid ( $mail );
	}
	
	/**
	 * Validacao de coordenadas geograficas (latitude e longitude)
	 *
	 * @param String $coordinate        	
	 */
	public static function isValidGeoPosition($coordinate) {
		$validator = new \Zend\Validator\Regex ( '/^(\-?\d+(?:\.\d+)?),?\s*(\-?\d+(?:\.\d+)?)$/' );
		return $validator->isValid ( $coordinate );
	}
	
	/**
	 * Valida o formato de um endereco de IPv4/IPv6
	 *
	 * @param String $ip        	
	 */
	public static function isValidIP($ip) {
		$validator = new \Zend\Validator\Ip ();
		return $validator->isValid ( $ip );
	}
	
	/**
	 * Verifica se uma string se encontra dentro do padroa monetario de um pais.
	 * Default DE (0.000,00)
	 *
	 * @param String $value,
	 *        	valor para verificacao
	 * @return Boolean
	 */
	public static function isValidMoney($value, $locale = 'de') {
		if (self::isValidNotEmpty ( $value )) {
			$value = self::cleanValueMoney ( $value );
			$validator = new \Zend\I18n\Validator\Float ( array (
					'locale' => $locale 
			) );
			return $validator->isValid ( $value );
		}
		return false;
	}
	
	/**
	 * Validacao de inteiro com ponto flutuante
	 *
	 * @param unknown $value        	
	 * @param string $locale        	
	 */
	public static function isValidFloat($value) {
		$validator = new \Zend\Validator\Regex ( '/^(\-?\d+(?:\.\d+)?),?\s*(\-?\d+(?:\.\d+)?)$/' );
		return $validator->isValid ( $value );
	}
	
	/**
	 * Remove caracteres comum em uma variavel de moeda, porem nao permitidos
	 *
	 * @param unknown $value        	
	 */
	public static function cleanValueMoney($value) {
		return str_replace ( array (
				'$',
				'R$',
				'R',
				' ' 
		), '', $value );
	}
	
	/**
	 * Verifica se contem uma string na relacao passada dentro array de palavras.
	 * Retorna TRUE para a primeira palavra encontrada.
	 * Caso encontrado uma palavra, nao eh verificado as palavras seguintes do array.
	 *
	 * @param Array $words,
	 *        	contem a lista de palavras que deve ser procurada dentro do texto
	 * @param String $string,
	 *        	texto para busca
	 * @return boolean
	 */
	public static function ifStringContainsSpecificWords($words, $string) {
		foreach ( $words as $a ) {
			if (strpos ( $string, $a ) !== false) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Compara duas string
	 *
	 * @param string $str1        	
	 * @param string $str2        	
	 * @param boolean $case,
	 *        	Comparação de string caso-sensitivo de Binário seguro dos primeiros n caracteres
	 * @return boolean, TRUE se forem iguais e FALSE se nao forem iguais.
	 */
	public static function ifSafeStringComparison($str1, $str2, $case = false) {
		if (! $case) {
			$cmp = strcmp ( $str1, $str2 );
		} else {
			$cmp = strncasecmp ( str1, $str2 );
		}
		if ($cmp == 0) { // successful match
			return true;
		}
		return false;
	}
	/**
	 * Tenta obter a moeda de um array, caso contrario, retorna default
	 *
	 * @param unknown $data        	
	 * @return Ambigous <string, unknown>
	 */
	public static function whichMyCurrency($data) {
		// Verificacoes adicionais da moeda, defaul eh BRL
		$currency = isset ( $data ['currency'] ) ? $data ['currency'] : 'BRL';
		$currency = ($currency == 'BRL' || $currency == 'USD') ? $currency : 'BRL';
		
		return $currency;
	}
	
	/**
	 * Verifica se o nome do destinatario/rementente esta na lista
	 *
	 * @param unknown $str        	
	 * @return boolean
	 */
	public static function isValidDiscussions($str) {
		switch ($str) {
			case 'CUSTOMER' :
				return true;
				break;
			case 'SYSTEM' :
				return true;
				break;
		}
		return false;
	}
}