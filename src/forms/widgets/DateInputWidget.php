<?php
namespace templates\forms\widgets;
class DateInputWidget extends InputWidget {
	var $type="text";
	function render($name,$value,$attrs=array()) {
		$attrs['class'] = isset($attrs['class']) ? $attrs['class'].' date' : 'date';
		return parent::render($name,$value,$attrs);
	}
}