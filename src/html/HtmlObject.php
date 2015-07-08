<?php
namespace penguin\html;
class HtmlObject {
	var $tagname = "";
	var $attrs= array();
	var $content;
	function __construct($tagname,$content,$attrs=array()) {
		$this->tagname = $tagname;
		$this->content = $content;
		$this->attrs = $attrs;
	}
	function __toString() {
		$args = array_map(function ($value,$key) {
			return "$value=\"$key\"";
		},array_keys($this->attrs),$this->attrs);
		return "<".$this->tagname." ".join(' ',$args).">$this->content</".$this->tagname.">";
	}	
	function setAttr($name,$attr) {
		$this->attrs[$name] = $attr;
	}
}