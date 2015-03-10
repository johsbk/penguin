<?php
namespace templates\model;
class DateTimeField extends ModelField {
	function __construct($dict=array()) {
		if (!isset($dict['default'])) $dict['default'] = null;
		parent::__construct($dict);
	}
	function getType() {
		return "datetime";
	}
	function getFormType() {
		return $this->getType();
	}
	
	function get($value) {
		if ($value=='0000-00-00 00:00:00' || $value == '') return null;
		return $value;
	}
	function toString($value) {
		if ($value instanceof \DateTime) {
			return $value->format('Y-m-d H:i:s');
		} else {
			if ($value=='') return null;
			return $value;
		}
		
	}
}
?>