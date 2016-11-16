<?php

namespace penguin\model;

class ImageField extends FileField
{
    public function __construct($dict = array())
    {
        parent::__construct($dict);
    }
    public function getFormType()
    {
        return 'image';
    }

    public function getFormField()
    {
        $f = new \penguin\forms\FileField(array('path' => $this->path));
        $f->widget = new \penguin\forms\widgets\ImageWidget();
        $f->name = $this->name;
        $f->dbname = $this->dbname;

        return $f;
    }
}
