<?php

namespace penguin\model;

use penguin\common\Functions;

class VarcharField extends ModelField
{
    public $maxlength;
    public function __construct($dict = array())
    {
        $this->maxlength = Functions::nz($dict['maxlength'], 255);
        if (!isset($dict['default'])) {
            $dict['default'] = '';
        }
        parent::__construct($dict);
    }
    public function getType()
    {
        return "varchar($this->maxlength)";
    }
    public function getFormType()
    {
        return 'string';
    }
}
