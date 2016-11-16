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
}
