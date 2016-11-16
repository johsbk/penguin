<?php

namespace penguin\model;

class BooleanField extends EnumField
{
    const FALSE = 'false';
    public function __construct($dict = array())
    {
        $dict['options'] = array('true', self::FALSE);
        $dict['default'] = self::FALSE;
        parent::__construct($dict);
    }
    public function set($value) {
    	if (is_bool($value)) {
    		return $value ? 'true' : self::FALSE;
    	}
    	return $value;
    }
}
