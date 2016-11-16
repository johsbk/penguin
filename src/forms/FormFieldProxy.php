<?php

namespace penguin\forms;

use penguin\forms\widgets\HiddenInputWidget;

class FormFieldProxy
{
    private $form;
    public $field;
    public function __construct($field, $form)
    {
        $this->form = $form;
        $this->field = $field;
    }
    public function label()
    {
        return $this->field->label($this->form);
    }
    public function render()
    {
        return $this->field->render($this->form);
    }
    public function __toString()
    {
        return $this->field->render($this->form);
    }
    public function setAttr($name, $attr)
    {
        $this->field->widget->attrs[$name] = $attr;
    }
    public function errors()
    {
        return $this->field->errors($this->form);
    }
    public function hidden()
    {
        return $this->field->widget instanceof HiddenInputWidget;
    }
}
