<?php
namespace templates\forms\widgets;
abstract class InputWidget extends Widget {
	var $type = null;
	function render($name,$value,$attrs=array()) {
		$attrs['name']=$name;
		$attrs['id'] = 'id_'.$name;
		$attrs['type'] = $this->type;
		if (!is_null($value)) {
			$attrs['value'] = $value;
		}
		$args = array_map(function ($value,$key) {
			return "$value=\"$key\"";
		},array_keys($attrs),$attrs);
		return "<input ".join(' ',$args)." />";
	}
}