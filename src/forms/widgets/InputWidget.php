<?php

namespace penguin\forms\widgets;

abstract class InputWidget extends Widget
{
    public $type = null;
    public function render($name, $value, $attrs = array())
    {
        $attrs['name'] = $name;
        $attrs['id'] = 'id_'.$name;
        $attrs['type'] = $this->type;
        if (!is_null($value)) {
            $attrs['value'] = $value;
        }
        $args = array_map(function ($value, $key) {
            return "$value=\"$key\"";
        }, array_keys($attrs), $attrs);

        return '<input '.implode(' ', $args).' />';
    }
}
