<?php
namespace penguin\mvc;
class ClassLoader {
	static function register() {		
		spl_autoload_register(array('penguin\mvc\ClassLoader','autoload'));	
	}
	static function autoload($class_name) {
		$filename = $class_name . '.php';
		$t = str_replace("\\",'/',SITE_PATH.'/src/'.$filename);
		if (file_exists($t)) {
			include($t);
			return true;
		}
	}
}