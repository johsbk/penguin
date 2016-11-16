<?php

namespace penguin\model;

class BooleanField extends EnumField
{
    public function __construct($dict = array())
    {
        $dict['options'] = array('true', 'false');
        $dict['default'] = 'false';
        parent::__construct($dict);
    }
    public function set($value) {
    	if (is_bool($value)) {
    		return $value ? 'true' : 'false';
    	}
    	return $value;
    }
}
