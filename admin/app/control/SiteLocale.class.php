<?php

class SiteLocale
{
	const LANG = LANGUAGE;
	
	// Content translate
	public static function setContent($content) {
		
		$data = self::getDataTranslation();
		
		if (!empty($data)) {
			foreach ($data as $line) 
			{
				$content = preg_replace('/('.$line['from'].')/', $line['to'], $content);
			}
		}
		return $content;
	}
	
	// Get translation for data
	private static function getDataTranslation () {
		$file = 'admin/app/locale/'.self::LANG.'/content.csv';
		$data = array();
		
		$handle = fopen ($file, 'r');
		while (($line = fgetcsv($handle, 1000, ",")) !== FALSE) 
		{
			if (isset($line[0], $line[1])) {
				$data[] = array(
					'from' => $line[0], 
					'to' => $line[1], 
				);
			}
		}
		fclose ($handle);
		
		return $data;
	}
	
	// Get language
	public static function getLanguage() {
		$langs_accepst = array(
			'pt_br', 
			'en_us'
		);
		
		$language = 'pt_br';
		$languages = preg_replace(array('/-/', '/,/'), array('_', ';'), $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$langs_array = explode(';', $languages);
		foreach($langs_accepst as $lang) {
			if (in_array($lang, $langs_array)) {
				$language = $lang;
				break;
			}
			
		}
		
		return $language;
	}
}

?>