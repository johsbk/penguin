<?php
namespace templates\model;
use \templates\common\Functions;
use \Exception;
class UintField extends ModelField {
	var $size;
	function __construct($dict=array()) {
		$this->size = Functions::nz($dict['size'],10);
		if (!isset($dict['default'])) $dict['default']=0;
		parent::__construct($dict);
	}
	function getType() {
		return "int($this->size) unsigned";
	}
	function set($value) {
		if ($value==='') $value=0;
		if (!is_numeric($value)) throw new Exception("Field $this->_model.$this->name: Not a numeric value : $value");
		if ($value < 0) throw new Exception("Field $this->name: no negative values allowed : $value");
		return parent::set($value);
	}
	function getFormType() {
		return "int";
	}
}
?>