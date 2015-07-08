<?php
namespace penguin\forms;

abstract class Form implements \Iterator, FormInterface {
	protected static $_fields = array();
	private static $_initialized = array();
	private $fields =array();
	private $index = 0;
	public $cleaned_data=array();
	public $data;
	private $is_bound = false;
	private $_errors = null;
	public $formset_number = null;
	function __construct($data=null) {
		static::initialize();
		$this->data = $data;
		$this->is_bound = !is_null($data);
		$i=0;
		foreach (static::getFields() as $k=>$field) {
			$ffp = new FormFieldProxy($field,$this);
			$this->fields[$i++] = $ffp;
			$this->fields[$k] = $ffp;
		}
	}
	protected static function initialize () {
		$class = get_called_class();
		if (!isset(self::$_initialized[$class])) {
			self::$_initialized[$class] = true;
			static::init();
			static::build();
		}
	}
	function __get($var) {
		if (isset($this->fields[$var]) && $this->fields[$var] instanceof FormFieldProxy) {	

			return $this->fields[$var];
		}
	}
	function __isset($var) {
		if (isset(static::${$var}) && static::${$var} instanceof FormField) {
			return true;
		}
		return false;
	}
	protected static function build() {
		$class = get_called_class();
		static::$_fields[$class] = array();
		$reflector = new \ReflectionClass($class);
		foreach ($reflector->getStaticProperties() as $name=>$f) {
			if ($f instanceof FormField) {
					$f->name = $name;
					$f->dbname = $name;
					$f->_form = $class;
					static::$_fields[$class][$name] =$f;
			}	
		}
	}
	static function getFields() {
		static::initialize();
		return static::$_fields[get_called_class()];
	}
	function as_p() {
		$out = array();
		foreach (static::getFields() as $field) {
			$out[] = "<p>".$field->label($this).$field->render($this)."</p>";
		}
		return join("\n",$out);
	}
	function __toString() {
		return $this->as_p();
	}
	function current() {
		return $this->fields[$this->index];
	}
	function next() {
		$this->index++;
	}
	function rewind() {
		$this->index =0;
	}
	function valid() {	
		return isset($this->fields[$this->index]);
	}
	function key() {
		return $this->index;
	}
	function isValid() {
		$this->fullClean();
		return is_null($this->_errors);
	}
	function errors() {
		if (is_null($this->_errors)) {
			$this->fullClean();
		}
		return $this->_errors;
	}
	function addError($field,$error) {
		if (!isset($this->_errors[$field])) $this->_errors[$field] = array();
		$this->_errors[$field][] = $error;
	}
	function getErrors($fieldname) {
		return isset($this->_errors[$fieldname]) ? $this->_errors[$fieldname] : array();
	}
	function fullClean() {
		if (!$this->is_bound) return;
		$this->cleaned_data = array();
		$this->clean();
	}
	function clean() {
		foreach (static::getFields() as $field) {
			$this->cleaned_data[$field->dbname] = $field->normalize($this->data);
			$field->validate($this->cleaned_data[$field->dbname],$this);
		}
	}
	abstract function save();
	static function makeFormSet($amount,$input=null) {
		return new FormSet(get_called_class(),$amount,$input);
	}
}