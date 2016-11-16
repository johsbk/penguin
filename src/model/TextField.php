<?php

namespace penguin\model;

class TextField extends ModelField
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
        return 'text';
    }
    public function getFormType()
    {
        return 'blob';
    }
    public function getFormField()
    {
        $f = new \penguin\forms\CharField();
        $f->widget = new \penguin\forms\widgets\TextareaWidget();
        $f->name = $this->name;
        $f->dbname = $this->dbname;

        return $f;
    }
}
