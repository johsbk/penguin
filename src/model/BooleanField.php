<?php
namespace penguin\model;
class BooleanField extends EnumField {
	function __construct($dict=array()) {
		$dict['options'] = array('true','false');
		$dict['default'] = 'false';
		parent::__construct($dict);
	}
}