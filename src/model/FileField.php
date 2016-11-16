<?php

namespace penguin\model;

class FileField extends VarcharField
{
    public $path;
    public function __construct($dict = array())
    {
        if (!isset($dict['path'])) {
            throw new ModelException('No upload path given');
        }
        $this->path = $dict['path'];
        parent::__construct($dict);
    }
    public function get($value)
    {
        return $this->path.$value;
    }
    public function getFormType()
    {
        return 'file';
    }
}
