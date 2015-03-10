<?php
namespace penguin\model;
use \penguin\common\Functions;
class VarcharField extends ModelField {
	var $maxlength;
	function __construct($dict=array()) {
		$this->maxlength = Functions::nz($dict['maxlength'],255);
		if (!isset($dict['default'])) $dict['default'] = '';
		parent::__construct($dict);
	}
	function getType() {
		return "varchar($this->maxlength)";
	}
	function getFormType() {
		return "string";
	}
}
?>