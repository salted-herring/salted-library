<?php
/**
 * @file Debugger
 *
 * Debugging functions
 * */
namespace SaltedHerring;

class Debugger {
	public static function inspect($obj, $die = true) {
		\Debug::dump($obj);
		if ($die) die;
	}
	
	public static function methods(&$obj) {
		if (!empty($obj)){
			echo '<pre>';
			print_r(get_class_methods($obj));
			echo '</pre>';
		}else{
			echo 'object is empty';
		}
		die;
	}
}