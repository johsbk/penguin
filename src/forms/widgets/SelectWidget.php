<?php

namespace penguin\forms\widgets;

class SelectWidget extends Widget
{
    public $emptylabel;
    public function __construct($emptyLabel = '----------')
    {
        $this->emptylabel = $emptyLabel;
    }
    public function render($name, $value, $attrs = array(), $choices = array())
    {
        $attrs['name'] = $name;
        $attrs['id'] = 'id_'.$name;
        $args = array_map(function ($value, $key) {
            return "$value=\"$key\"";
        }, array_keys($attrs), $attrs);
        $out = array('<select '.implode(' ', $args).'>');
        if ($this->emptylabel) {
            $out[] = '<option value="">'.$this->emptylabel.'</option>';
        }
        foreach ($choices as $choice) {
            list($key, $val) = $choice;
            $out[] = '<option '.($key == $value ? 'selected="selected"' : '')." value=\"$key\">$val</option>";
        }
        $out[] = '</select>';

        return implode("\n", $out);
    }
}
