<?php

namespace penguin\model;

class IdField extends UintField
{
    public $size;
    public function __construct($dict = array())
    {
        $dict['primary'] = true;
        $dict['extra'] = 'auto_increment';
        parent::__construct($dict);
    }
    public function getFormField()
    {
        $f = new \penguin\forms\HiddenField();
        $f->name = $this->name;
        $f->dbname = $this->dbname;

        return $f;
    }
}
