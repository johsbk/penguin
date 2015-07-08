<?php
namespace penguin\forms;
use penguin\forms\widgets\HiddenInputWidget;
class FormFieldProxy {
	private $form;
	public $field;
	function __construct($field,$form) {
		$this->form = $form;
		$this->field = $field;
	}
	function label() {
		return $this->field->label($this->form);
	}
	function render() {
		return $this->field->render($this->form);
	}
	function __toString() {
		return $this->field->render($this->form);
	}
	function setAttr($name,$attr) {
		$this->field->widget->attrs[$name] = $attr;
	}
	function errors() {
		return $this->field->errors($this->form);
	}
	function hidden() {
		return $this->field->widget instanceof HiddenInputWidget;
	}
}