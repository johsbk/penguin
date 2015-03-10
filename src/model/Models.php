<?php
namespace templates\model;
class Models {
	static $models = array();
	static function checkIntegrity() {
		foreach (static::$models as $m) $m::checkIntegrity();
	}
	static function includeModels($pre="./") {
		return;
		$files = array();
		$path = "{$pre}models/";
		$classes = array();
		if ($handle = opendir($path)) {
				while (false !== ($file = readdir($handle))) {
					if (substr($file,-4) == '.php') {
						\templates\Import::inc_once($path.$file);
						$classes[] = substr($file,0,-4);
					}
			}
			closedir($handle);
		}
		foreach ($classes as $class) 
			$class::init();
		return $files;
	}
}
?>