<?php
namespace penguin\model;
use \penguin\common\Functions;
use \Exception;
class IntField extends ModelField {
	var $size;
	function __construct($dict=array()) {
		$this->size = Functions::nz($dict['size'],10);
		if (!isset($dict['default'])) $dict['default']=0;
		parent::__construct($dict);
	}
	function getType() {
		return "int($this->size)";
	}
	function set($value) {
		if (!is_numeric($value)) throw new ModelException("Field $this->_model.$this->name: Not a numeric value : $value");
		return parent::set($value);
	}
	function getFormType() {
		return "int";
	}
}
?>