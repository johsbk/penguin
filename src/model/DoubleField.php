<?php
namespace templates\model;
class DoubleField extends ModelField {
	function __construct($dict=array()) {
		if (!isset($dict['default'])) $dict['default'] = '';
		parent::__construct($dict);
	}
	function getType() {
		return "double";
	}
	function getFormType() {
		return "real";
	}
	function set($value) {
		return $value==='' || is_null($value) ? 'NULL' : $value;
		if (strpos($value, ',')!==false) {
			$value = str_replace(',','.',str_replace('.', '', $value));
		}
		return $value;
	}
	function toString($value) {
		return $value==='' || is_null($value) ? 'NULL' : $value;
	}
	function getFormField() {
		$f = new \templates\forms\DoubleField();
		$f->name = $this->name;
		$f->dbname = $this->dbname;
		return $f;
	}
}
?>