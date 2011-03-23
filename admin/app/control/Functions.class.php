<?php

class Functions 
{
	/** 
	 * Gera senha
	 */
	static public function generate_password($length = 15, $uppercase = false, $lowercase = true, $numbers = true, $codes = false) {
		$maius = "ABCDEFGHIJKLMNOPQRSTUWXYZ";
		$minus = "abcdefghijklmnopqrstuwxyz";
		$numer = "0123456789";
		$codig = '!@#$%&*()-+.,;?{[}]^><:|';
		$base  = '';
		$base .= ($uppercase) ? $maius : '';
		$base .= ($lowercase) ? $minus : '';
		$base .= ($numbers) ? $numer : '';
		$base .= ($codes) ? $codig : '';
		srand((float) microtime() * 10000000);
		$password = '';
		for ($i = 0; $i < $length; $i++) {
			$password .= substr($base, rand(0, strlen($base)-1), 1);
		}
		return $password;
	}
	
	/**
	 * Gera chave de ativação
	 */
	static public function keyActivation() {
		$code = time();
		$value = 'abcdefghijklmnopqrstuvxz0123456789';
		$total = count($value);
		$c = 0;
		while($c < 20) {
			$code .= rand(0, $total);
			$c++;
		}
		return md5($code);
	}
	
	/**
	 * Deleta arquivo ou arquivos de um diretorio
	 */
	static public function deleteFile($string) {
		if (is_file($string)) {
			unlink($string);
		}
		else if (is_dir($string)) {
			$open = opendir($string);
			while ($file = readdir($open)) {
				if ($file != '.' && $file != '..') {
					$path = $string.'/'.$file;
					if (is_file($path)) {
						chmod($path, 0777);
						unlink($path);
					}
					else if (is_dir($path)) {
						self::deleteFile($path);
					}
				}
			}
			closedir($open);
			chmod($string, 0777);
			rmdir($string);
		}
	}
		
	/**
	 * Retorna query string
	 */
	static public function queryString($remove = array()) {
		if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) {
			$new = array();
			parse_str($_SERVER['QUERY_STRING'], $query_string);
			foreach ($query_string as $key => $querys) {
				if (!in_array($key, $remove)) {
					$new[$key] = $querys;
				}
			}
			return http_build_query($new);;
		}
	}

	/**
	 * Debug object ou array
	 */
	static public function debug($data) {
		echo '<div style="border:2px solid #000;padding:10px;background:#e1e1e1;margin:10px;">';
		echo '<pre>';
		print_r($data);
		echo '</pre>';
		echo '</div>';
	}
	
	// Verifica se string é data no formato brasileiro
	static function isDate ($date) {
		return ereg('^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$', $date);
	}
	
	// Verifica se string é data no formato internacional
	static function isDateDB ($date) {
		return ereg('^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$', $date);
	}

}

?>