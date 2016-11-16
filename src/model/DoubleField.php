<?php

namespace penguin\model;

class DoubleField extends ModelField
{
    public function __construct($dict = array())
    {
        if (!isset($dict['default'])) {
            $dict['default'] = '';
        }
        parent::__construct($dict);
    }
    public function getType()
    {
        return 'double';
    }
    public function getFormType()
    {
        return 'real';
    }
    public function set($value)
    {
        return $value === '' || is_null($value) ? 'NULL' : $value;
    }
    public function toString($value)
    {
        return $value === '' || is_null($value) ? 'NULL' : $value;
    }
    public function getFormField()
    {
        $f = new \penguin\forms\DoubleField();
        $f->name = $this->name;
        $f->dbname = $this->dbname;

        return $f;
    }
}
