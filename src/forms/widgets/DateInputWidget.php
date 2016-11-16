<?php

namespace penguin\forms\widgets;

class DateInputWidget extends InputWidget
{
    public $type = 'text';
    public function render($name, $value, $attrs = array())
    {
        $attrs['class'] = isset($attrs['class']) ? $attrs['class'].' date' : 'date';

        return parent::render($name, $value, $attrs);
    }
}
