<?php

namespace penguin\forms;

abstract class Form implements \Iterator, FormInterface
{
    protected static $_fields = array();
    private static $_initialized = array();
    private $fields = array();
    private $index = 0;
    public $cleaned_data = array();
    public $data;
    private $is_bound = false;
    private $_errors = null;
    public $formset_number = null;
    public function __construct($data = null)
    {
        static::initialize();
        $this->data = $data;
        $this->is_bound = !is_null($data);
        $i = 0;
        foreach (static::getFields() as $k => $field) {
            $ffp = new FormFieldProxy($field, $this);
            $this->fields[$i++] = $ffp;
            $this->fields[$k] = $ffp;
        }
    }
    protected static function initialize()
    {
        $class = get_called_class();
        if (!isset(self::$_initialized[$class])) {
            self::$_initialized[$class] = true;
            static::init();
            static::build();
        }
    }
    public function __get($var)
    {
        if (isset($this->fields[$var]) && $this->fields[$var] instanceof FormFieldProxy) {
            return $this->fields[$var];
        }
    }
    public function __isset($var)
    {
        if (isset(static::${$var}) && static::${$var} instanceof FormField) {
            return true;
        }

        return false;
    }
    protected static function build()
    {
        $class = get_called_class();
        static::$_fields[$class] = array();
        $reflector = new \ReflectionClass($class);
        foreach ($reflector->getStaticProperties() as $name => $f) {
            if ($f instanceof FormField) {
                $f->name = $name;
                $f->dbname = $name;
                $f->_form = $class;
                static::$_fields[$class][$name] = $f;
            }
        }
    }
    public static function getFields()
    {
        static::initialize();

        return static::$_fields[get_called_class()];
    }
    public function as_p()
    {
        $out = array();
        foreach (static::getFields() as $field) {
            $out[] = '<p>'.$field->label($this).$field->render($this).'</p>';
        }

        return implode("\n", $out);
    }
    public function __toString()
    {
        return $this->as_p();
    }
    public function current()
    {
        return $this->fields[$this->index];
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
        return isset($this->fields[$this->index]);
    }
    public function key()
    {
        return $this->index;
    }
    public function isValid()
    {
        $this->fullClean();

        return is_null($this->_errors);
    }
    public function errors()
    {
        if (is_null($this->_errors)) {
            $this->fullClean();
        }

        return $this->_errors;
    }
    public function addError($field, $error)
    {
        if (!isset($this->_errors[$field])) {
            $this->_errors[$field] = array();
        }
        $this->_errors[$field][] = $error;
    }
    public function getErrors($fieldname)
    {
        return isset($this->_errors[$fieldname]) ? $this->_errors[$fieldname] : array();
    }
    public function fullClean()
    {
        if (!$this->is_bound) {
            return;
        }
        $this->cleaned_data = array();
        $this->clean();
    }
    public function clean()
    {
        foreach (static::getFields() as $field) {
            $this->cleaned_data[$field->dbname] = $field->normalize($this->data);
            $field->validate($this->cleaned_data[$field->dbname], $this);
        }
    }
    abstract public function save();
    public static function makeFormSet($amount, $input = null)
    {
        return new FormSet(get_called_class(), $amount, $input);
    }
}
