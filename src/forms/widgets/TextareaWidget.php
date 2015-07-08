<?php
namespace templates\forms\widgets;
class TextareaWidget extends Widget {
	function render($name,$value,$attrs=array()) {
		$attrs['name']=$name;
		$attrs['id'] = 'id_'.$name;
		$args = array_map(function ($value,$key) {
			return "$value=\"$key\"";
		},array_keys($attrs),$attrs);
		return "<textarea ".join(' ',$args).">$value</textarea";
	}
}