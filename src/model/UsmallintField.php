<?php

namespace penguin\model;

use penguin\common\Functions;

class UsmallintField extends ModelField
{
    public $size;
    public function __construct($dict = array())
    {
        $this->size = Functions::nz($dict['size'], 5);
        parent::__construct($dict);
    }
    public function getType()
    {
        return "smallint($this->size) unsigned";
    }
    public function toString($value)
    {
        return $value === '' || is_null($value) ? 'NULL' : $value;
    }
    public function getFormType()
    {
        return 'int';
    }
}
