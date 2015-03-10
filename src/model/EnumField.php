<?php
namespace templates\model;
use \templates\common\Functions;
class EnumField extends ModelField {
	var $options;
	function __construct($dict=array()) {
		$this->options = Functions::nz($dict['options'],array());
		parent::__construct($dict);
	}
	function getType() {
		return "enum(".join(",",array_map(function ($value) {
			if (is_numeric($value)) {
				return $value;
			} else {
				return "'".$value."'";
			}
		},$this->options)).")";
	}
	function getFormType() {
		return 'combobox';
	}
}