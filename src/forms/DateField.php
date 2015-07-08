<?php
namespace templates\forms;
use templates\forms\widgets\DateInputWidget;
class DateField extends FormField {
	function __construct($dict=array()) {
		$this->widget = new DateInputWidget();
		parent::__construct($dict);
	}
}