<?php

namespace penguin\forms;

class FormSet implements \Iterator
{
    private $form;
    private $forms = array();
    private $index = 0;
    public function __construct($form, $amount, $input = null)
    {
        $this->form = $form;
        $data = array();
        if (!is_null($input)) {
            foreach ($input as $k => $v) {
                if (strpos($k, '-') !== false) {
                    list($number, $field) = explode('-', $k);
                    $number = substr($number, 4);
                    if (!isset($data[$number])) {
                        $data[$number] = array();
                    }
                    $data[$number][$field] = $v;
                }
            }
        }
        for ($i = 0; $i < $amount; ++$i) {
            if (isset($data[$i])) {
                $f = new $form($data[$i]);
            } else {
                $f = new $form();
            }
            $f->formset_number = $i;
            $this->forms[] = $f;
        }
    }
    public function current()
    {
        return $this->forms[$this->index];
    }
    public function next()
    {
        ++$this->index;
    }
    public function rewind()
    {
        $this->index = 0;
    }
    public function valid()
    {
        return isset($this->forms[$this->index]);
    }
    public function key()
    {
        return $this->index;
    }
    public function __toString()
    {
        $out = array();
        foreach ($this as $form) {
            $out[] = $form->__toString();
        }

        return implode("\n", $out);
    }
    public function isValid()
    {
        foreach ($this as $form) {
            if (!$form->isValid()) {
                return false;
            }
        }

        return true;
    }
}
