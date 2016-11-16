<?php

namespace penguin\forms;

use penguin\forms\widgets\TextInputWidget;

class DoubleField extends FormField
{
    public function __construct($dict = array())
    {
        $this->widget = new TextInputWidget();
        parent::__construct($dict);
    }
    public function validate($value, $form)
    {
        if (!is_numeric($value)) {
            $form->addError($this->name, 'Not a number');
        }
    }
}
