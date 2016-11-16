<?php

namespace penguin\forms;

use penguin\forms\widgets\FileInputWidget;

class FileField extends FormField
{
    public $path;
    public function __construct($dict = array())
    {
        if (!isset($dict['path'])) {
            throw new FormException('No upload path given');
        }
        $this->path = $dict['path'];
        $this->widget = new FileInputWidget();
        parent::__construct($dict);
    }
    public function normalize($data)
    {
        if ($_FILES[$this->dbname]['error'] == UPLOAD_ERR_OK) {
            $temp = explode('.', $_FILES[$this->dbname]['name']);
            $ext = end($temp);
            $name = uniqid().'.'.$ext;
            move_uploaded_file($_FILES[$this->dbname]['tmp_name'],
                SITE_PATH.$this->path.$name);

            return $name;
        } else {
            return $data[$this->dbname.'_current'];
        }
    }
    public function render($form)
    {
        return $this->widget->render($this->getName($form), $this->getValue($form), $this->widgetAttrs(), $this->path);
    }
}
