<?php

class Filters
{
	static public function run($str) {
		$str 	= str_replace('"', '', $str);
		$str 	= urldecode($str);
		$str 	= self::convert($str);
		return 	$str;
	}
	
	static public function charset($str) {
		$str = mb_detect_encoding($str.'a' , 'UTF-8, ISO-8859-1');
		return $str;
	}
	
	static public function convert($str, $default = 'UTF-8') {
		switch($default) {
			case 'UTF-8' : 
				if (self::charset($str) != $default) {
					$str = utf8_encode($str);
				}
				break;
			case 'ISO-8859-1' : 
				if (self::charset($str) != $default) {
					$str = utf8_decode($str);
				}
				break;
		}
		return $str;
	}
	
	static public function toIso($str) {
		if (self::charset($str) != 'ISO-8859-1') {
			$str = utf8_decode($str);
		}
		return $str;
	}
	
	static public function remove_accents($str) {
		$str 	= self::convert($str, 'ISO-8859-1');
		$from 	= self::convert('ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr', 'ISO-8859-1');
		$to 	= self::convert('AAAAAAACEEEEIIIIDNOOOOOOUUUUYbBaaaaaaaceeeeiiiidnoooooouuuyybyRr', 'ISO-8859-1');
		$str	= strtr($str, $from, $to);
		$str 	= self::convert($str);
		return	$str;
	}
	
	static public function anti_sql_injection($str) {
		$str 	= get_magic_quotes_gpc() ? stripslashes($str) : $str;
		$str 	= str_replace('"', '\'', $str);
		$str 	= function_exists("mysql_real_escape_string") ? mysql_real_escape_string($str) : mysql_escape_string($str);
		return 	$str;
	}
	
	static public function cleanForUrl($str){
		//removendo os acentos
		$str 	= trim($str);
		$str 	= Filters::remove_accents($str);
		//trocando espaço em branco por underline
		$str 	= eregi_replace('( )','+',$str);
		//tirando outros caracteres invalidos
		$str 	= eregi_replace('[^a-z0-9\+]','',$str);
		//trocando duplo,tripo,quadrupo... espaço (underline) por 1 underline só
		$str 	= eregi_replace('[\+]{2,}','',$str);
		return strtolower($str);
	}
	
	static public function getUrlImage($str){
		//removendo os acentos
		$str 	= trim($str);
		$str 	= Filters::remove_accents($str);
		//trocando espaço em branco por underline
		$str 	= eregi_replace('( )','_',$str);
		//tirando outros caracteres invalidos
		$str 	= eregi_replace('[^a-z0-9\_]','',$str);
		//trocando duplo,tripo,quadrupo... espaço (underline) por 1 underline só
		$str 	= eregi_replace('[\_]{2,}','',$str);
		return strtolower($str);
	}
	
	static public function strip_tags($data) {
		if (is_object($data)) {
			foreach ($data as $key => $value) {
				if (is_array($value)) {
					$data->$key = self::strip_tags($value);
				}
				else {
					$data->$key = strip_tags($value);
				}
			}
		}
		else if (is_array($data)) {
			foreach ($data as $key => $value) {
				if (is_array($value)) {
					$data[$key] = self::strip_tags($value);
				}
				else {
					$data[$key] = strip_tags($value);
				}
			}
		}
		else if (is_string($data)) {
			$data = strip_tags($data);
		}
		return $data;
	}
	
	/**
	 * Somente numeros
	 */
	static public function toNumber($number) {
		$number = eregi_replace('[^0-9]*', '', $number);
		return $number;
	}
	
	/**
	 * Resgata letras e numeros
	 */
	static public function alphanumeric($str) {
		$str = eregi_replace('[^0-9A-Za-z]*', '', $str);
		return $str;
	}
}

?>