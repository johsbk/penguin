<?php
namespace penguin\form;
use penguin\common\Functions;

class DateBox {
	static $jsdone = false;
	
	static function js() {
		if (DateBox::$jsdone) return false;
		DateBox::$jsdone = true;
		return "<script src=\"".TEMPLATE_MEDIA_PATH."form/js/DateBox.js\" type=\"text/javascript\"></script>";
	}
	static function display($dict) {
		$name = Functions::nz($dict['name'],'');
		$default = Functions::nz($dict['default'],'');
		$governs = Functions::nz($dict['governs'],false);
		$onchange = Functions::nz($dict['onchange'],false);
		$width = Functions::nz($dict['width'],80);
		$eu = Functions::nz($dict['eu'],false);
		if ($governs) {
			$onchange= "location = '".Functions::getArgs($governs)."&$governs='+$('input[name=$name]').val()";
		}
		$out = array();
		$out[] = "<div style=\"display:inline; white-space: nowrap;\"><input type=\"text\" ".($onchange ? "onchange=\"$onchange\"" : '')." style=\"width: {$width}px\" name=\"$name\" value=\"$default\" /><input type=\"button\" value=\"#\" onclick=\"DateBox.display(this.previousSibling,".($eu ? 'true' : 'false').")\" /></div>";
		return join("\n",$out);
	}
}
?>