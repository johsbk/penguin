<?php
namespace penguin\forms\widgets;
class SelectWidget extends Widget {
	var $emptylabel;
	function __construct($emptyLabel='----------') {
		$this->emptylabel=$emptyLabel;
	}
	function render($name,$value,$attrs=array(),$choices=array()) {
		$attrs['name']=$name;
		$attrs['id'] = 'id_'.$name;
		$args = array_map(function ($value,$key) {
			return "$value=\"$key\"";
		},array_keys($attrs),$attrs);
		$out = array("<select ".join(' ',$args).">");
		if ($this->emptylabel) {
			$out[] = "<option value=\"\">".$this->emptylabel."</option>";
		}
		foreach ($choices as $choice) {
			list($key,$val) = $choice;
			$out[] = "<option ".($key==$value ? 'selected="selected"' : '')." value=\"$key\">$val</option>";
		}
		$out[] = "</select>";
		return join("\n",$out);
	}
}