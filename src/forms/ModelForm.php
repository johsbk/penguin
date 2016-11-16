<?php
namespace penguin\forms;
class ModelForm extends Form {
	protected static $model;
	protected static $fields;
	private static $_model;
	static function init() {
	}
	protected static function build() {
		$model = static::getModel();
		$model::init();
		$modelfields = $model::getFields();
		$class = get_called_class();
		static::$_fields[$class] = array();
		foreach ($modelfields as $field) {
			if (!isset(static::$fields)||in_array($field->name,static::$fields)||$field->name=='id') {
				$f = $field->getFormField();
				$f->_form = $class;
				static::$_fields[$class][$f->name] =$f;
			}
		}
	}
	private static function getModel() {
		if (isset(static::$_model)) return static::$_model;		
		$class = get_called_class();
		if (strpos(static::$model, '\\')===false) {
			
			$pos= strpos($class,'\\');
			self::$_model = substr($class,0,$pos+1).'models\\'.static::$model;
			#echo $this->_model;
		}
		/* @var $model BaseModel */
		if (!class_exists(self::$_model)) throw new FormException("Class: ".self::$_model." doesn't exist");
		return self::$_model;
	}
	function save() {
		$model = static::getModel();
		$data = $this->cleaned_data;
		if (!$data['id']) unset($data['id']);
		$object = $model::from($data);
		$object->save();
	}
}