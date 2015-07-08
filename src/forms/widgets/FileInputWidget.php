<?php
namespace templates\forms\widgets;
class FileInputWidget extends Widget {
	function render($name,$value,$attrs=array(),$path) {
		$attrs['name']=$name;
		$attrs['id'] = 'id_'.$name;
		$attrs['type'] = 'file';
		$args = array_map(function ($value,$key) {
			return "$value=\"$key\"";
		},array_keys($attrs),$attrs);
		return $value."<input ".join(' ',$args)." />";
	}
}