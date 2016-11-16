<?php

namespace penguin\model;

use penguin\common\Functions;
use penguin\db\DB;

abstract class ModelField
{
    public $name;
    public $dbname;
    public $primary;
    public $allownull;
    public $default;
    public $extra;
    public $_model;
    public function __construct($dict = array())
    {
        $this->primary = Functions::nz($dict['primary'], false);
        $this->default = Functions::nz($dict['default'], null);
        $this->extra = Functions::nz($dict['extra'], null);
        $this->allownull = Functions::nz($dict['allownull'], false);
    }
    public function checkIntegrity($table)
    {
        echo "$this->name: ";
        $q = DB::fetchOne("SHOW COLUMNS FROM `$table` WHERE Field='$this->dbname'");
        $err = '';
        if (!$q) {
            $err .= ' Field not found';
            DB::query("ALTER TABLE  `$table` ADD  `$this->dbname` ".$this->getType().(!$this->allownull ? ' NOT NULL' : '').($this->extra != '' ? " $this->extra" : '').($this->default != null ? " DEFAULT '$this->default'" : ''));
            $err .= ': added!';
        } else {
            if ($q['Type'] != $this->getType()) {
                $err .= " Type is $q[Type] but should be: ".$this->getType();
            }
            if (($q['Key'] == 'PRI') != $this->primary) {
                if ($this->primary) {
                    $err .= ' Should be primary';
                } else {
                    $err .= ' Should not be primary';
                }
            }
            if ($q['Default'] != $this->default) {
                $err .= " Default is $q[Default] but should be: $this->default";
            }
            if ($q['Extra'] != $this->extra) {
                $err .= " Extra is $q[Extra] but should be: $this->extra";
            }
        }
        if ($err == '') {
            echo ' OK';
        } else {
            echo $err;
        }
        echo '<br />';
    }
    public function set($value)
    {
        return $value;
    }
    public function get($value)
    {
        return $value;
    }
    public function toString($value)
    {
        return is_null($value) ? 'NULL' : (string) $value;
    }
    public function skip()
    {
        return false;
    }
    abstract public function getType();
    abstract public function getFormType();
    public function getFormField()
    {
        $f = new \penguin\forms\CharField();
        $f->name = $this->name;
        $f->dbname = $this->dbname;

        return $f;
    }
}
