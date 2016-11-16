<?php

namespace penguin\model;

class DateTimeField extends ModelField
{
    public function __construct($dict = array())
    {
        if (!isset($dict['default'])) {
            $dict['default'] = null;
        }
        parent::__construct($dict);
    }
    public function getType()
    {
        return 'datetime';
    }
    public function getFormType()
    {
        return $this->getType();
    }

    public function get($value)
    {
        if ($value == '0000-00-00 00:00:00' || $value == '') {
            return null;
        }

        return $value;
    }
    public function toString($value)
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        } else {
            if ($value == '') {
                return null;
            }

            return $value;
        }
    }
}
