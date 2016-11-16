<?php

namespace penguin\model;

use penguin\common\Functions;

class IntField extends ModelField
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
        return "int($this->size)";
    }
    public function set($value)
    {
        if (!is_numeric($value)) {
            throw new ModelException("Field $this->_model.$this->name: Not a numeric value : $value");
        }

        return parent::set($value);
    }
    public function getFormType()
    {
        return 'int';
    }
}
