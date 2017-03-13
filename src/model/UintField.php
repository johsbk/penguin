<?php

namespace penguin\model;

use penguin\common\Functions;

class UintField extends ModelField
{
    public $size;
    public function __construct($dict = array())
    {
        $this->size = Functions::nz($dict['size'], 10);
        if (!isset($dict['default'])) {
            $dict['default'] = 0;
        }
        parent::__construct($dict);
    }
    public function getType()
    {
        return "int($this->size) unsigned";
    }
    public function set($value)
    {
        if ($value === '') {
            $value = 0;
        }
        if (!is_numeric($value)) {
            throw new ModelException("Field $this->_model.$this->name: Not a numeric value : $value");
        }
        if ($value < 0) {
            throw new ModelException("Field $this->name: no negative values allowed : $value");
        }

        return parent::set(intval($value));
    }
    public function getFormType()
    {
        return 'int';
    }
}
