<?php
namespace penguin\form;
class Validate {
	static function number($val) {
		if (!is_numeric($val)) throw new Exception("Not a number: $val");
		return $val;
	}
	static function string($val) {
		return addslashes($val);
	}
}