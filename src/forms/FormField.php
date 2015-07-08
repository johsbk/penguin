<?php
namespace penguin\forms;
use \penguin\html\HtmlObject;
abstract class FormField {
	var $widget = null;
	var $required = true;
	var $name;
	var $dbname;
	var $_form = null;
	function __construct($args=array()) {
		if (isset($args['widget'])) {
			$this->widget = $args['widget'];
		}
		if (isset($args['required'])) {
			$this->required=$args['required'];
		}
	}
	function render($form) {
		return $this->widget->render($this->getName($form),$this->getValue($form),$this->widgetAttrs());
	}
	function getName($form) {
		$number = $form->formset_number;
		return (!is_null($number) ? 'form'.$number.'-' :'').$this->dbname;
	} 
	function getValue($form) {
		return isset($form->data[$this->dbname]) ? $form->data[$this->dbname] : '';
	}
	function label($form) {
		return new HtmlObject(
			'label',
			$this->labelText(),
			array(
				'for'=>"id_".$this->getName($form)
				)
			);

	}
	function labelText() {
		return ucfirst(str_replace('_',' ',$this->name));
	}
	function widgetAttrs() {
		return $this->widget->attrs;
	}
	function normalize($data) {
		return $data[$this->dbname];
	}
	function validate($value,$form) {

	}
	function errors($form) {
		$errors = $form->getErrors($this->name);
		if (count($errors)==0) return '';
		$out = array("<ul class=\"errorlist\">");

		foreach ($errors as $error) {
			$out[] = "<li>$error</li>";
		}
		$out[] = "</ul>";
		return join("\n",$out);
	}
}