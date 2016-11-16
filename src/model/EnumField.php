<?php

namespace penguin\model;

use penguin\common\Functions;

class EnumField extends ModelField
{
    public $options;
    public function __construct($dict = array())
    {
        $this->options = Functions::nz($dict['options'], array());
        parent::__construct($dict);
    }
    public function getType()
    {
        return 'enum('.implode(',', array_map(function ($value) {
            if (is_numeric($value)) {
                return $value;
            } else {
                return "'".$value."'";
            }
        }, $this->options)).')';
    }
    public function getFormType()
    {
        return 'combobox';
    }
}
