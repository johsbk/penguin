<?php
namespace penguin\forms;
use penguin\forms\widgets\SelectWidget;
class ModelChoiceField extends FormField {
	private $choices = array();
	function __construct($dict=array()) {
		if (!isset($dict['queryset'])) throw new \Exception('No queryset provided.');
		$this->widget = new SelectWidget();
		$this->choices = array_map(function ($item) { return array($item->id,$item->__toString()); },$dict['queryset']->toArray());
		parent::__construct($dict);
	}
	function render($form) {
		return $this->widget->render($this->getName($form),$this->getValue($form),$this->widgetAttrs(),$this->choices);
	}
}