<?php
namespace templates\model;
class DateField extends ModelField {
	function __construct($dict=array()) {
		parent::__construct($dict);
	}
	function getType() {
		return "date";
	}
	function getFormType() {
		return $this->getType();
	}
	static function currentDate() {
		return date('Y-m-d');
	}
	function get($value) {
		if ($value=='0000-00-00' || $value=='') return null;
		return $value;
	}
	function set($value) {
		if (strlen($value)==10 && $value{2}=='-') {
			$value = substr($value, 6,4).'-'.substr($value,3,2).'-'.substr($value,0,2);
		}
		return $value;
	}
	function toString($value) {
		if ($value instanceof \DateTime) {
			return $value->format('Y-m-d');
		} else {
			if ($value=='') return null;
			return $value;
		}
		
	}
	function getFormField() {
		$f = new \templates\forms\DateField();
		$f->name = $this->name;
		$f->dbname = $this->dbname;
		return $f;
	}
}
?>