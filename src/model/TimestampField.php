<?php
namespace templates\model;
class TimestampField extends ModelField {
	function __construct($dict=array()) {
		$dict['default'] = '0000-00-00 00:00:00';
		$dict['extra'] = 'on update CURRENT_TIMESTAMP';
		parent::__construct($dict);
	}
	function getType() {
		return "timestamp";
	}
	function getFormType() {
		return "datetime";
	}
	function skip() {
		return true;
	}
}
?>