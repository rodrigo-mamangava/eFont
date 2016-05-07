<?php

namespace Useful\Controller;

/**
 * Classe de utilidades
 *
 * @author Claudio
 *        
 */
class UsefulController {
	
	/**
	 * Chega se eh um paginator e se sim, retorna o array com os resultados
	 *
	 * @param unknown $paginator        	
	 */
	public static function paginatorToArray($paginator) {
		if (is_a ( $paginator, 'Zend\Paginator\Paginator' )) {
			if ($paginator->count () > 0) {
				return iterator_to_array ( $paginator->getCurrentItems () );
			}
			return array ();
		}
		return $paginator;
	}
	/**
	 * Checa se um valor existe em um array
	 *
	 * @param String $value        	
	 * @param array $data        	
	 * @return boolean
	 */
	public static function ifExistsInArray($value, $data) {
		// var_dump($value, $data);
		if (is_array ( $data )) {
			if (in_array ( $value, $data )) {
				return true;
			}
		}
		return false;
	}
	/**
	 * Desfaz o efeito de addslashes() nos atributos de um objeto
	 *
	 * @param unknown $obj        	
	 */
	public static function getStripslashes($obj) {
		foreach ( $obj as $key => $value ) {
			if (is_array ( $value )) {
				continue;
			} elseif (strpos ( $key, 'check_' ) !== false) {
				$obj->{$key} = $value == '1' ? true : false;
			} elseif (strpos ( $key, 'collapsed_' ) !== false || strpos ( $key, 'collapsed' ) !== false) {
				$obj->{$key} = $value == '1' ? true : false;
			}elseif(strpos ( $key, 'ddig' ) !== false){
				$obj->{$key} = \Cryptography\Controller\CryptController::encrypt($value, true);
			} else {
				$obj->{$key} = stripslashes ( $value );
			}
		}
		return $obj;
	}
	
	/**
	 * Obtem a extensao de um arquivo
	 *
	 * @param unknown $filename        	
	 */
	public static function getFileExtesion($filename) {
		return pathinfo ( $filename, PATHINFO_EXTENSION );
	}
	/**
	 * Retorna diferena entre duas datas
	 *
	 * @param unknown $start        	
	 * @param unknown $end        	
	 * @return Array
	 */
	public static function getTimeDifferenceBetweenDates($start, $end) {
		$seconds = strtotime ( $start ) - strtotime ( $end );
		
		$days = floor ( $seconds / 86400 );
		$hours = floor ( ($seconds - ($days * 86400)) / 3600 );
		$minutes = floor ( ($seconds - ($days * 86400) - ($hours * 3600)) / 60 );
		$seconds = floor ( ($seconds - ($days * 86400) - ($hours * 3600) - ($minutes * 60)) );
		
		return array (
				'days' => $days,
				'hours' => $hours,
				'minutes' => $minutes,
				'seconds' => $seconds 
		);
	}
	
	/**
	 * tenta obter o valor de um array pelo nome da chave
	 *
	 * @param unknown $Data        	
	 * @param unknown $value        	
	 * @return NULL
	 */
	public static function getNameArrayOfObject($Data, $value) {
		$str = null;
		foreach ( $Data as $Entry ) {
			if ($Entry->getId () == $value) {
				$str = $Entry->getName ();
				break;
			}
		}
		
		return $str;
	}
	
	/**
	 * Retorna o dia da semana em pt_BR
	 *
	 * @param String $day_week,
	 *        	dia da semana em en_US
	 * @return String $str
	 */
	public static function getDayWeekBrasil($day_week) {
		$str = 'Domingo';
		switch ($day_week) {
			case 'Saturday' :
				$str = 'Sabado';
				break;
			case 'Sunday' :
				$str = 'Domingo';
				break;
			case 'Monday' :
				$str = 'Segunda';
				break;
			case 'Tuesday' :
				$str = 'Terca';
				break;
			case 'Wednesday' :
				$str = 'Quarta';
				break;
			case 'Thursday' :
				$str = 'Quinta';
				break;
			case 'Friday' :
				$str = 'Sexta';
				break;
			default :
				$str = 'Domingo';
		}
		return $str;
	}
	
	/**
	 * Retorna o nome da faixa de premiacao segundo numero de acerto
	 *
	 * @param Int $number,
	 *        	inteiro positivo
	 * @return String
	 */
	public static function getNumberByFaixaNome($number) {
		$str = 'Zero';
		switch ($number) {
			case 20 :
				$str = 'Vinte';
				break;
			case 19 :
				$str = 'Dezenove';
				break;
			case 18 :
				$str = 'Dezoito';
				break;
			case 17 :
				$str = 'Dezesete';
				break;
			case 16 :
				$str = 'Dezesseis';
				break;
			case 15 :
				$str = 'Quinze';
				break;
			case 14 :
				$str = 'Quatorze';
				break;
			case 13 :
				$str = 'Treze';
				break;
			case 12 :
				$str = 'Doze';
				break;
			case 11 :
				$str = 'Onze';
				break;
			case 7 :
				$str = 'Hepta';
				break;
			case 6 :
				$str = 'Sena';
				break;
			case 5 :
				$str = 'Quina';
				break;
			case 4 :
				$str = 'Quadra';
				break;
			case 3 :
				$str = 'Terno';
				break;
			default :
				$str = 'Zero';
		}
		return $str;
	}
	
	/**
	 * Converter o formato da data dd/mm/YYYY para YYYY-mm-dd
	 *
	 * @param Date $dt        	
	 * @return Date
	 */
	public static function getSystemToDb($dt) {
		if (! is_null ( $dt ) && strlen ( $dt ) == 10) {
			$dt = explode ( '/', $dt );
			$result = date ( "Y-m-d", mktime ( 0, 0, 0, $dt [1], $dt [0], $dt [2] ) );
			return $result;
		} elseif (! is_null ( $dt ) && strlen ( $dt ) == 19) {
			$split = explode ( ' ', $dt );
			$date = self::getSystemToDb ( $split [0] );
			$time = $split [1];
			return $date . ' ' . $time;
		}
		
		return $dt;
	}
	
	/**
	 * Converter o formato da data YYYY-mm-dd para dd/mm/YYYY
	 *
	 * @param Date $dt        	
	 * @param bool $have_hour        	
	 * @return Date
	 */
	public static function getDbToSystem($dt, $is_hour = false) {
		$result = null;
		if (! is_null ( $dt )) {
			$xdt = explode ( '-', $dt );
			$result = @date ( "d/m/Y", mktime ( 0, 0, 0, $xdt [1], $xdt [2], $xdt [0] ) );
			if ($is_hour == true) {
				$ex00 = explode ( ' ', $dt );
				$ex01 = explode ( ':', $ex00 [1] );
				$result .= " $ex01[0]:$ex01[1]:$ex01[2]";
			}
		}
		return $result;
	}
	
	/**
	 * Converte um inteiro para o formato de numero brasileiro
	 * Se apenas um parametro e dado, number sera formatado sem decimais, mas com uma virgula (",") entre cada grupo de milhar.
	 * Se dois parametros sao dados, number sera formatado com o numero de casas decimais especificadas em decimals com um ponto (".") na frente, e uma virgula (",") entre cada grupo de milhar.
	 * Se todos os quatro parametros forem dados, number sera formatado com o numero de casas decimais em decimals, dec_point ao inves do ponto (".") antes das casas decimais e thousands_sep ao inves de uma virgula (",") entre os grupos de milhares.
	 * Somente o primeiro caractere de thousands_sep e usado. Por exemplo, se voce usar foo como o parametro thousands_sep no numero 1000, number_format() ira retornar 1f000.
	 *
	 * @param float $number        	
	 * @param int $decimals        	
	 * @param string $dec_point        	
	 * @param string $thousands_sep
	 *        	@rturn float $number
	 */
	public static function getFormatNumberBrazil($number, $decimals = 0, $dec_point = ',', $thousands_sep = '.') {
		return @number_format ( $number, $decimals, $dec_point, $thousands_sep );
	}
	
	/**
	 * Converter um Objecto em Arry
	 *
	 * @param Object $object,
	 *        	objeto a ser convertido
	 * @return Array
	 */
	public static function objectToArray($object) {
		if (count ( $object ) > 1) {
			$arr = array ();
			for($i = 0; $i < count ( $object ); $i ++) {
				$arr [] = get_object_vars ( $object [$i] );
			}
			return $arr;
		} else {
			return get_object_vars ( $object );
		}
	}
	
	/**
	 * Soma o numero de dias em uma determinada data
	 *
	 * @param Date $date,
	 *        	data inicial
	 * @param Int $days_plan,
	 *        	numero de dias que sera somado
	 * @return Date, nova data com o numero de dias somado
	 */
	public static function nextDate($dt_start, $days_plan = 7) {
		$date = explode ( "-", $dt_start );
		$day = $date [2];
		$month = $date [1];
		$year = $date [0];
		$result = @date ( 'Y-m-d', @mktime ( 0, 0, 0, $month, $day + $days_plan, $year ) );
		return $result;
	}
	
	/**
	 * Diminui o numero de dias em uma determinada data
	 *
	 * @param Date $date,
	 *        	data inicial
	 * @param Int $number,
	 *        	numero de dias que sera reduzido na data
	 * @return Date, nova data
	 */
	public static function previousDate($dt_start, $number = 1) {
		$date = explode ( "-", $dt_start );
		$day = $date [2];
		$month = $date [1];
		$year = $date [0];
		$result = @date ( 'Y-m-d', mktime ( 0, 0, 0, $month, $day - $number, $year ) );
		return $result;
	}
	
	/**
	 * Retira todos os espaco em uma string
	 *
	 * @param String $string,
	 *        	Texto que sera verificado se contem espacos em branco
	 * @return String $string, Texto com espacos em branco removido, caso haja
	 */
	public static function removeBlank($string) {
		return str_replace ( array (
				' ',
				'\s',
				'\t',
				'\r',
				'\x',
				'\0' 
		), '', $string );
	}
	/**
	 * Extrai dezenas de uma string no formato pre definido
	 *
	 * @param String $string        	
	 * @return multitype:
	 */
	public static function getDozens($string) {
		return explode ( '-', self::removeBlank ( $string ) );
	}
	/**
	 * convert/encode os Emoji
	 *
	 * @param unknown $text        	
	 * @param unknown $op        	
	 */
	public static function convertEmoji($text, $op) {
		if ($op == "ENCODE") {
			return preg_replace_callback ( '/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', array (
					'self',
					"encodeEmoji" 
			), $text );
		} else {
			return preg_replace_callback ( '/(\\\u[0-9a-f]{4})+/', array (
					'self',
					"decodeEmoji" 
			), $text );
		}
	}
	/**
	 * Decode Emoji
	 *
	 * @param unknown $match        	
	 */
	public static function encodeEmoji($match) {
		return str_replace ( array (
				'[',
				']',
				'"' 
		), '', json_encode ( $match ) );
	}
	/**
	 * Encode Emoji
	 *
	 * @param unknown $text        	
	 */
	public static function decodeEmoji($text) {
		if (! $text)
			return '';
		$text = $text [0];
		$decode = json_decode ( $text, true );
		if ($decode)
			return $decode;
		$text = '["' . $text . '"]';
		$decode = json_decode ( $text );
		if (count ( $decode ) == 1) {
			return $decode [0];
		}
		return $text;
	}
}

