<?php

namespace penguin\forms\widgets;

class FileInputWidget extends Widget
{
    public function render($name, $value, $attrs, $path)
    {
        $attrs['name'] = $name;
        $attrs['id'] = 'id_'.$name;
        $attrs['type'] = 'file';
        $args = array_map(function ($value, $key) {
            return "$value=\"$key\"";
        }, array_keys($attrs), $attrs);

        return $value.'<input '.implode(' ', $args).' />';
    }
}
