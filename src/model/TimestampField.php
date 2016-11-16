<?php

namespace penguin\model;

class TimestampField extends ModelField
{
    public function __construct($dict = array())
    {
        $dict['default'] = '0000-00-00 00:00:00';
        $dict['extra'] = 'on update CURRENT_TIMESTAMP';
        parent::__construct($dict);
    }
    public function getType()
    {
        return 'timestamp';
    }
    public function getFormType()
    {
        return 'datetime';
    }
    public function skip()
    {
        return true;
    }
}
