<?php

namespace penguin\model;

use penguin\common\Functions;

class ForeignKeyField extends UintField
{
    const STR_CLASS='class';
    public $model;
    public $related_name;
    public $cache = array();
    public function __construct($dict = array())
    {
        $this->model = Functions::nz($dict['model'], false);
        $test = debug_backtrace();
        $classtraceindex = 1;
        if (strpos($this->model, '\\') === false) {
            $orgmodel=$this->model;
            $pos = strrpos($test[1][self::STR_CLASS], '\\');
            $this->model = substr($test[1][self::STR_CLASS], 0, $pos + 1).$this->model;
            if (substr($this->model,0,13)=='penguin\\model') {
                $classtraceindex =2;
                $pos = strrpos($test[2][self::STR_CLASS], '\\');
                $this->model = substr($test[2][self::STR_CLASS], 0, $pos + 1).$orgmodel;
            }
        }
        $model = $this->model;
        /* @var $model BaseModel */
        if (!class_exists($model)) {
            throw new ModelException("Class: $model doesn't exist");
        }
        $mymodel = $test[$classtraceindex][self::STR_CLASS];
        $this->related_name = Functions::nz($dict['related_name'], $mymodel::getName());
        $model::init();
        $model::addHas($test[$classtraceindex][self::STR_CLASS], $this);

        parent::__construct($dict);
    }
    public static function createToModel($model) {
        return new ForeignKeyField(array('model' => $model));
    }
    public function get($value)
    {
        if (!isset($this->cache[$value])) {
            $m = $this->model;
            $this->cache[$value] = $m::find($value);
        }

        return $this->cache[$value];
    }
    public function getFormType()
    {
        return 'combobox';
    }
    public function set($value)
    {
        if ($value instanceof BaseModel) {
            $value = $value->id;
        }

        return parent::set($value);
    }
    public function getFormField()
    {
        $model = $this->model;
        $f = new \penguin\forms\ModelChoiceField(array('queryset' => $model::all()));
        $f->name = $this->name;
        $f->dbname = $this->dbname;

        return $f;
    }
}
