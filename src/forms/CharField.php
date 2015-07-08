<?php
namespace templates\forms;
use templates\forms\widgets\TextInputWidget;
class CharField extends FormField {
	function __construct($dict=array()) {
		$this->widget = new TextInputWidget();
		parent::__construct($dict);
	}
}