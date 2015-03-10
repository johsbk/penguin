<?php
namespace templates\model;
class ImageField extends FileField {
	function __construct($dict=array()) {
		parent::__construct($dict);
	}
	function getFormType() {
		return "image";
	}

	function getFormField() {
		$f = new \templates\forms\FileField(array("path"=>$this->path));
		$f->widget = new \templates\forms\widgets\ImageWidget();
		$f->name = $this->name;
		$f->dbname = $this->dbname;
		return $f;
	}
}
?>