<?php

namespace penguin\model;

use penguin\common\Functions;
use penguin\db\DB;
use penguin\db\QuerySet;
use Exception;

abstract class BaseModel
{
    const WHERE = 'where';
    const SHORT = 'short';
    public static $_name = array();
    public static $_fields = array();
    public static $_indexes = array();
    public static $_initialized = array();
    public static $_has = array();
    private $row = array();
    protected $errors = array();
    public function __construct($row = false)
    {
        static::init();
        if ($row) {
            foreach (static::getFields() as $f) {
                if (isset($row[$f->dbname])) {
                    $this->row[$f->dbname] = $f->set($row[$f->dbname]);
                }
            }
        }
    }
    public function row()
    {
        return $this->row;
    }
    public function __set($var, $value)
    {
        if (isset(static::${$var}) && static::${$var} instanceof ModelField) {
            $this->row[static::${$var}->dbname] = static::${$var}->set($value);

            return;
        }
    }
    public function __isset($var)
    {
        return $this->issetModelField($var)
            || static::issetForeignKeyField($var)
            || static::has($var);
    }
    private function issetModelField($var) {
        return isset(static::${$var}) && static::${$var} instanceof ModelField;
    }
    private function issetForeignKeyField($var) {
        return ($pos = strpos($var, '_id')) !== false && isset(static::${$subvar = substr($var, 0, $pos)});
    }
    public function __get($var)
    {
        self::init();
        if (isset(static::${$var}) && static::${$var} instanceof ModelField) {
            return static::${$var}->get(Functions::nz($this->row[static::${$var}->dbname], static::${$var}->default));
        } elseif (($pos = strpos($var, '_id')) !== false && isset(static::${$subvar = substr($var, 0, $pos)})) {
            return Functions::nz($this->row[$var], static::${$subvar}->default);
        } elseif ($class = static::has($var)) {
            list($m, $f) = $class;

            return $m::filter(array($f->dbname => $this->row['id']));
        }
    }
    public function __call($method, $params)
    {
        self::init();
        if ($class = static::has($method)) {
            list($m, $f) = $class;
            $dict = $params[0];
            if (!isset($dict[self::WHERE])) {
                $dict[self::WHERE] = array();
            }
            $dict[self::WHERE][] = "$f->dbname={$this->row['id']}";

            return $m::all($dict);
        } else {
            throw new ModelException("No function called $method in class ".get_called_class());
        }
    }
    public static function has($name)
    {
        return Functions::nz(static::$_has[get_called_class()][$name], false);
    }
    public static function from($request)
    {
        if (isset($request['id']) && $request['id']) {
            $o = static::find($request['id']);
            foreach (static::getFields() as $f) {
                if (isset($request[$f->dbname])) {
                    $o->{$f->name} = $request[$f->dbname];
                }
            }
        } else {
            $o = new static($request);
        }

        return $o;
    }
    public static function addHas($class, $field = false)
    {
        if (strpos($class, '\\') === false) {
            $pos = strrpos(get_called_class(), '\\');
            $class = substr(get_called_class(), 0, $pos + 1).$class;
        }
        if (!$field) {
            $class::init();
        } else {
            $name = $field->related_name;
            static::$_has[get_called_class()][$name] = array($class, $field);
        }
    }
    protected static function build()
    {
        $class = get_called_class();
        static::$_fields[$class] = array();
        $reflector = new \ReflectionClass($class);
        foreach ($reflector->getProperties() as $prop) {
            /* @var $prop \ReflectionProperty */
            if (!$prop->isPrivate() && !$prop->isProtected()) {
                $f = static::${$prop->name};
                if ($f instanceof ModelField) {
                    if ($f instanceof ForeignKeyField) {
                        $f->dbname = $prop->name.'_id';
                    } else {
                        $f->dbname = $prop->name;
                    }
                    $f->name = $prop->name;
                    $f->_model = $class;
                    static::$_fields[$class][] = $f;
                }
            }
        }
        Models::$models[] = $class;
    }
    protected static function determineName()
    {
        $pattern = '/[A-Z]/';
        $to = '_$0';
        $class = preg_replace($pattern, $to, get_called_class());
        return \strtolower(\substr($class, \strrpos($class, '\\') + 2));
    }
    public static function init()
    {
        $class = get_called_class();
        if (!Functions::nz(static::$_initialized[$class], false)) {
            static::$_initialized[$class] = true;
            static::$_name[$class] = static::determineName();
            static::localinit();
            static::build();
            if (isset($_GET['checkintegrity'])) {
                self::checkIntegrity();
            }
        }
    }
    public static function getForeignKeyModel($field)
    {
        self::init();

        return static::${$field}->model;
    }
    public static function getFields()
    {
        self::init();

        return static::$_fields[get_called_class()];
    }
    public static function getField($field)
    {
        self::init();
        foreach (static::getFields() as $f) {
            if ($f->name == $field) {
                return $f;
            }
        }

        return null;
    }
    public static function getName()
    {
        self::init();

        return static::$_name[get_called_class()];
    }
    protected static function localinit()
    {
    }
    public static function checkIntegrity()
    {
        echo 'Checking '.static::getName().': ';
        try {
            @DB::query('SELECT * FROM '.static::getName());
            echo 'exists.';
        } catch (Exception $e) {
            static::createTable();
            echo 'created';
        }
        echo '<br />';
        foreach (static::getFields() as $f) {
            $f->checkIntegrity(static::getName());
        }
    }
    public static function createTable()
    {
        $sql = array();
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'.static::getName().'` (';
        foreach (static::getFields() as $f) {
            /* @var $f ModelField */
            $sql[] = "`$f->dbname` ".$f->getType().(!$f->allownull ? ' NOT NULL' : '').($f->extra != '' ? " $f->extra" : '').($f->default != null ? " DEFAULT '$f->default'" : '').',';
            if ($f->primary) {
                $primary = $f->dbname;
            }
        }
        $sql[] = "PRIMARY KEY (`$primary`)";
        $sql[] = ') ENGINE=InnoDB';
        DB::query(implode("\n", $sql));
    }
    public static function count($dict = array())
    {
        return static::all($dict)->count();
    }
    public static function find($find, $dict = array())
    {
        static::init();
        $where = Functions::nz($dict[self::WHERE], array());
        $order = Functions::nz($dict['order'], false);
        $limit = Functions::nz($dict['limit'], false);
        $one = Functions::nz($dict['one'], false);
        $count = Functions::nz($dict['count'], false);
        if ($count) {
            $sql = 'SELECT count(id) as count FROM '.static::getName();
        } else {
            $sql = 'SELECT * FROM `'.static::getName().'`';
        }
        if (is_numeric($find)) {
            $one = true;
            $where[] = "id=$find";
        } elseif ($find == 'first') {
            $one = true;
        }

        if (count($where) > 0) {
            $sql .= ' WHERE ('.implode(') and (', $where).')';
        }
        if ($order) {
            $sql .= " ORDER BY $order";
        }

        if ($limit) {
            $sql .= " LIMIT $limit";
        } elseif ($one) {
            $sql .= ' LIMIT 0,1';
        }
        if ($count) {
            $q = DB::fetchOne($sql);

            return $q['count'];
        } elseif ($one) {
            $q = DB::fetchOne($sql);
            if ($q) {
                return new static($q);
            } else {
                return false;
            }
        } else {
            $rs = DB::fetch($sql);
            $ar = array();
            while ($row = $rs->next()) {
                $ar[] = new static($row);
            }

            return $ar;
        }
    }
    public static function first($dict = array())
    {
        return static::all($dict)->first();
    }
    public static function all($dict = array())
    {
        return static::filter($dict);
    }
    public static function filter($args = array())
    {
        static::init();
        $qs = new QuerySet(array('model' => get_called_class()));

        return $qs->filter($args);
    }
    public function save()
    {
        $this->errors = array();
        if (!$this->validate()) {
            return false;
        }
        $ar = array();
        foreach (static::getFields() as $f) {
            if (!($f->name == 'id' && empty($this->row[$f->dbname])) && !$f->skip()) {
                $ar[$f->dbname] = $f->toString(Functions::nz($this->row[$f->dbname], $f->default));
            }
        }
        if (isset($this->row['id']) && $this->row['id'] != 0 && DB::fetchOne('SELECT id FROM '.static::getName().' WHERE id='.$this->row['id'])) {
            DB::ezQuery('UPDATE', static::getName(), $ar, 'id='.$this->row['id']);
        } else {
            DB::ezQuery('INSERT', static::getName(), $ar);

            $this->row['id'] = DB::getLastId();
        }

        return true;
    }
    public function remove()
    {
        DB::query('DELETE FROM `'.static::getName().'` WHERE id='.$this->row['id']);
        unset($this->row['id']);
    }
    public function __toString()
    {
        return $this->row['id'];
    }
    
    public static function cleanString($str)
    {
        $from = array('&');
        $to = array('&amp;');

        return str_replace($from, $to, $str);
    }
    public static function formDict($dict = array())
    {
        $showfields = Functions::nz($dict['fields'], false);
        $dict['table'] = static::getName();
        $fields = array();
        foreach (static::getFields() as $f) {
            if (($showfields && (array_key_exists($f->name, $showfields) || in_array($f->name, $showfields))) || !$showfields) {
                $field = Functions::nz($showfields[$f->name], array());
                $field['name'] = $f->dbname;
                if (!isset($field['type'])) {
                    $field['type'] = $f->getFormType();
                }
                if ($f instanceof ForeignKeyField) {
                    $m = $f->model;
                    $field['dict'] = array('model' => $m::all());
                } elseif ($f instanceof EnumField) {
                    $foptions = array();
                    foreach ($f->options as $option) {
                        $foptions[] = array('name' => $option);
                    }
                    $field['dict'] = array('array' => $foptions, 'hidden' => 'name');
                }
                $fields[] = $field;
            }
        }
        $dict['fields'] = $fields;

        return $dict;
    }
    public function raw()
    {
        return $this->row;
    }
    public function validate()
    {
        return true;
    }
    public function getErrors()
    {
        return $this->errors;
    }
}
