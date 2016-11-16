<?php

namespace penguin\forms;

use penguin\html\HtmlObject;

abstract class FormField
{
    public $widget = null;
    public $required = true;
    public $name;
    public $dbname;
    public $_form = null;
    public function __construct($args = array())
    {
        if (isset($args['widget'])) {
            $this->widget = $args['widget'];
        }
        if (isset($args['required'])) {
            $this->required = $args['required'];
        }
    }
    public function render($form)
    {
        return $this->widget->render($this->getName($form), $this->getValue($form), $this->widgetAttrs());
    }
    public function getName($form)
    {
        $number = $form->formset_number;

        return (!is_null($number) ? 'form'.$number.'-' : '').$this->dbname;
    }
    public function getValue($form)
    {
        return isset($form->data[$this->dbname]) ? $form->data[$this->dbname] : '';
    }
    public function label($form)
    {
        return new HtmlObject(
            'label',
            $this->labelText(),
            array(
                'for' => 'id_'.$this->getName($form),
                )
            );
    }
    public function labelText()
    {
        return ucfirst(str_replace('_', ' ', $this->name));
    }
    public function widgetAttrs()
    {
        return $this->widget->attrs;
    }
    public function normalize($data)
    {
        return $data[$this->dbname];
    }
    public function validate($value, $form)
    {
    }
    public function errors($form)
    {
        $errors = $form->getErrors($this->name);
        if (count($errors) == 0) {
            return '';
        }
        $out = array('<ul class="errorlist">');

        foreach ($errors as $error) {
            $out[] = "<li>$error</li>";
        }
        $out[] = '</ul>';

        return implode("\n", $out);
    }
}
