<?php
namespace templates\model;
class IdField extends UintField {
	var $size;
	function __construct($dict=array()) {
		$dict['primary'] = true;
		$dict['extra'] = 'auto_increment';
		parent::__construct($dict);
	}
	function getFormField() {
		$f = new \templates\forms\HiddenField();
		$f->name = $this->name;
		$f->dbname = $this->dbname;
		return $f;
	}
}
?>