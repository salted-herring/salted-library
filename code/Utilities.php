<?php
/**
 * @file Utilities
 *
 * Generic utility functions
 * */
namespace SaltedHerring;

class Utilities {
	
	public static function stripTags($strip_list, $html) {
		foreach ($strip_list as $tag)
		{
			$html = preg_replace('/<\/?' . $tag . '(.|\s)*?>/', '', $html);
		}
		return $html;
	}
	
	public static function SlagGen($type, $slag, $ID = null) {
		$test = $slag;
		$tick = 1;
		while (!empty(\DataObject::get_one($type, array('Slag' => $test))) && (\DataObject::get_one($type, array('Slag' => $test))->ID != $ID)) {
			$test = $slag . '-' . $tick;
			$tick++;
		}
		$slag = $test;
		return $slag;
	}
	
	public static function endsWith($haystack, $needle) {
		$haystack = strtolower($haystack);
		$needle = strtolower($needle);
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
	}
	
	public static function startsWith($haystack, $needle) {
		$haystack = strtolower($haystack);
		$needle = strtolower($needle);
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}
	public static function sanitiseClassName($string, $space_replacement = '-', $replacement = '') {
		
		$words = explode(' ', trim(strtolower($string)));
		$new_words = array();
		foreach($words as $word) {
			$word = preg_replace('/[^A-Za-z0-9]/', $replacement, trim($word));
			if (strlen($word) > 0) {
				$new_words[] = $word;
			}
		}
		
   		return implode('-', $new_words);
	}
	
	public static function params_to_cachekey($params){
		$str = '';
		if (count($params) > 0) {
			foreach ($params as $name => $value) {
				$value = self::sanitiseClassName($value);
				$str .= $name . '__' . $value . '_';
			}
		
			$str = '__' . rtrim($str, '_');
		}
		return $str;
	}
	
	public static function paramStringify($params, $prefix = '') {
		$str = '';
		if (count($params) > 0) {
			foreach ($params as $name => $value) {
				$value = str_replace(' ', '+', $value);
				$str .= $name . '=' . $value . '&';
			}
		
			$str = $prefix . rtrim($str, '&');
		}
		return $str;
	}
	
	public static function get_emails($groupCode) {
		$group = \DataObject::get_one('Group', "Code = '" . $groupCode . "'");
		
		if (!empty($group)) {
			return $group->Members()->column('Email');
		}
		
		return array();
	}
	
	public static function valid_email($email) {
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	public static function member_exist($email) {
		$member = \DataObject::get_one("Member",  "Email = '".$email."'");
		return !empty($member);
	}
	
	public static function isBrowser() {
		// Regular expression to match common browsers
		$browserlist = '/(opera|aol|msie|firefox|chrome|konqueror|safari|netscape|navigator|mosaic|lynx|amaya|omniweb|avant|camino|flock|seamonkey|mozilla|gecko)+/i';
		
		$validBrowser = preg_match($browserlist, strtolower($_SERVER['HTTP_USER_AGENT'])) === 1;
		
		return $validBrowser;// && !empty($_SERVER['HTTP_REFERER']);
	}
	
	public static function match_string($pattern, $str) {
		return fnmatch($pattern, $str);
	}
	
	public static function truncate_html($s, $l, $e = '&hellip;', $isHTML = true) {
		$s = trim($s);
		$e = (strlen(strip_tags($s)) > $l) ? $e : '';
		$i = 0;
		$tags = array();
	
		if($isHTML) {
			preg_match_all('/<[^>]+>([^<]*)/', $s, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
			foreach($m as $o) {
				if($o[0][1] - $i >= $l) {
					break;                  
				}
				$t = substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
				if($t[0] != '/') {
					$tags[] = $t;                   
				}
				elseif(end($tags) == substr($t, 1)) {
					array_pop($tags);                   
				}
				$i += $o[1][1] - $o[0][1];
			}
		}
		$output = substr($s, 0, $l = min(strlen($s), $l + $i)) . (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '') . $e;
		return $output;
	}
	
	
	/**
	 * Take a string with new line feeds & create paragraphs.
	 * */
	public static function nl2p($string, $viewer) {
		$items = new ArrayList();
			
		foreach(explode(PHP_EOL, $string) as $item) {
			if (trim($item)) {
				$items->push(new ArrayData(array(
					'line'	=> $item
				)));
			}
		}
		
		return $viewer->customise(new ArrayData(array(
			'Paragraphs' => $items
		)))->renderWith('Paragraphs');
	}
	
	/**
	 * find the key that matches a specific pattern.
	 * Used primarily with dbo field tags.
	 *
	 * e.g. UtilityFunctions::getValidKey('/*Description/', $this->db);
	 * */
	public static function getValidKey($pattern, $arr) {
		$keys = array();
		foreach($arr as $key => $value) {
			if (preg_match($pattern, $key)){
				$keys[] = $key;
			}
		}
		
		return $keys;
	}
	
	/**
	 * Get $count words from a piece of text.
	 * */
	public static function getWords($sentence, $count = 10) {
		$sentence = str_replace("\r", '', str_replace("\n", '', trim(strip_tags($sentence))));
		$words = explode(' ', $sentence);
		
		if (count($words) <= $count) {
			return $sentence;
		}
		
		$trimmed = '';
		for ($i = 0; $i < $count; $i++) {
			$trimmed .= $words[$i] . ' ';
		}
		
		$trimmed = trim($trimmed);
		
		return $trimmed;
	}
	
	/**
	 * Get max number of words within a character limit.
	 * */
	public static function getWordsWithinCharLimit($sentence, $limit = 150) {
		$str = '';
		$i = 1;
		
		if(strlen($sentence) < $limit) {
			return $sentence;
		}
		
		while (strlen($current = self::getWords($sentence, $i++)) < $limit) {
			$str = $current;
		}
		
		return $str;
	}
}