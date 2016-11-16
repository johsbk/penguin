<?php
namespace penguin\model;
class Models {
	static $models = array();
	static function checkIntegrity() {
		foreach (static::$models as $m) $m::checkIntegrity();
	}
	static function includeModels($pre="./") {
		
	}
}
?>