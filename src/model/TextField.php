<?php
namespace templates\model;
class TextField extends ModelField {
	function __construct($dict=array()) {
		if (!isset($dict['default'])) $dict['default'] = '';
		parent::__construct($dict);
	}
	function getType() {
		return "text";
	}
	function getFormType() {
		return "blob";	
	}
	function getFormField() {
		$f = new \templates\forms\CharField();
		$f->widget = new \templates\forms\widgets\TextareaWidget();
		$f->name = $this->name;
		$f->dbname = $this->dbname;
		return $f;
	}
}
?>