<?php
namespace templates\model;
use \templates\common\Functions;
class UsmallintField extends ModelField {
	var $size;
	function __construct($dict=array()) {
		$this->size = Functions::nz($dict['size'],5);
		parent::__construct($dict);
	}
	function getType() {
		return "smallint($this->size) unsigned";
	}
	function toString($value) {
		return $value==='' || is_null($value) ? 'NULL' : $value;
	}
	function getFormType() {
		return "int";
	}
}
?>