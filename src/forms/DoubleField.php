<?php
namespace templates\forms;
use templates\forms\widgets\TextInputWidget;
class DoubleField extends FormField {
	function __construct($dict=array()) {
		$this->widget = new TextInputWidget();
		parent::__construct($dict);
	}
	function validate($value,$form) {
		if (!is_numeric($value)) $form->addError($this->name,"Not a number");
	}
}