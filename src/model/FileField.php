<?php
namespace penguin\model;
class FileField extends VarcharField {
	var $path;
	function __construct($dict=array()) {
		if (!isset($dict['path'])) throw new ModelException('No upload path given');
		$this->path = $dict['path'];
		parent::__construct($dict);
	}
	function get($value) {
		return $this->path.$value;
	}
	function getFormType() {
		return "file";
	}
}
?>