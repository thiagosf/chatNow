<?php

/*
 * Emoticons
 *
 * Credit emoticons: KsK Smiley Pack 16x16 0.2 - by KishKiai
 * License: Freeware
 * http://addons.miranda-im.org/details.php?action=viewfile&id=2867
 *
 */
class Emoticons
{
	public $patterns = array();
	public $patterns_clean = array();
	public $replacements = array();
	
	public function __construct () {
	
		$this->patterns[] 			= '/\(:\)/';
		$this->patterns[] 			= '/\(zzz/';
		$this->patterns[] 			= '/\(rrr/';
		$this->patterns[] 			= '/\(;\)/';
		$this->patterns[] 			= '/\(=\)/';
		$this->patterns[] 			= '/\(B\)/';
		$this->patterns[] 			= '/\(:C/';
		$this->patterns[] 			= '/\(:\\\\/';
		$this->patterns[] 			= '/\(:D/';
		$this->patterns[] 			= '/\(s2/';
		$this->patterns[] 			= '/\(smack/';
		$this->patterns[] 			= '/\(lol/';
		$this->patterns[] 			= '/\(:\|/';
		$this->patterns[] 			= '/\(\^\)/';
		$this->patterns[] 			= '/\(:\(/';
		$this->patterns[] 			= '/\(:O/';
		$this->patterns[] 			= '/\(=O/';
		$this->patterns[] 			= '/\(star/';
		$this->patterns[] 			= '/\(:P/';
		
		$this->patterns_clean[] 	= '(:)';
		$this->patterns_clean[] 	= '(zzz';
		$this->patterns_clean[] 	= '(rrr';
		$this->patterns_clean[] 	= '(;)';
		$this->patterns_clean[] 	= '(=)';
		$this->patterns_clean[] 	= '(B)';
		$this->patterns_clean[] 	= '(:C';
		$this->patterns_clean[] 	= '(:\\';
		$this->patterns_clean[] 	= '(:D';
		$this->patterns_clean[] 	= '(s2';
		$this->patterns_clean[] 	= '(smack';
		$this->patterns_clean[] 	= '(lol';
		$this->patterns_clean[] 	= '(:|';
		$this->patterns_clean[] 	= '(^)';
		$this->patterns_clean[] 	= '(:(';
		$this->patterns_clean[] 	= '(:O';
		$this->patterns_clean[] 	= '(=O';
		$this->patterns_clean[] 	= '(star';
		$this->patterns_clean[] 	= '(:P';
		
		$this->replacements[] 		= 'smile.png';
		$this->replacements[] 		= 'zzz.png';
		$this->replacements[] 		= 'angry.png';
		$this->replacements[] 		= 'blink.png';
		$this->replacements[] 		= 'calm.png';
		$this->replacements[] 		= 'cool.png';
		$this->replacements[] 		= 'cry.png';
		$this->replacements[] 		= 'getlost.png';
		$this->replacements[] 		= 'grin.png';
		$this->replacements[] 		= 'heart.png';
		$this->replacements[] 		= 'kiss.png';
		$this->replacements[] 		= 'lol.png';
		$this->replacements[] 		= 'none.png';
		$this->replacements[] 		= 'oops.png';
		$this->replacements[] 		= 'sad.png';
		$this->replacements[] 		= 'shocked.png';
		$this->replacements[] 		= 'unwell.png';
		$this->replacements[] 		= 'yellowstar.png';
		$this->replacements[] 		= 'tongue.png';
		
		foreach ($this->replacements as $key => $value) {
			$rel = $this->patterns_clean[$key];
			$this->replacements[$key] = sprintf('<img src="images/emoticons/ksk-%s" title="%s" alt="%s" />', $value, $rel, $rel);
		}
	}
	
	static public function transform ($value) {
		if (ACTIVE_EMOTICONS) {
			$emoticons = new self;
			$value = preg_replace($emoticons->patterns, $emoticons->replacements, $value);
			return $value;
		}
		else {
			return $value;
		}
	}
	
	static public function getLinks () {
		if (ACTIVE_EMOTICONS) {
			$emoticons = new self;
			return implode(' ', $emoticons->replacements);
		}
	}
}

?>