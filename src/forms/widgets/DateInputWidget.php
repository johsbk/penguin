<?php

namespace penguin\forms\widgets;

class DateInputWidget extends InputWidget
{
    const STR_CLASS = 'class';
    public $type = 'text';
    public function render($name, $value, $attrs = array())
    {
        $attrs[self::STR_CLASS] = isset($attrs[self::STR_CLASS]) ? $attrs[self::STR_CLASS].' date' : 'date';

        return parent::render($name, $value, $attrs);
    }
}
