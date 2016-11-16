<?php

namespace penguin\db;

use penguin\model\BaseModel;
use penguin\model\DateTimeField;
use penguin\model\DateField;

class Qand
{
    public $type = 'AND';
    public $args;
    public $scope = 0;
    public function getNewScope()
    {
        return 't'.($this->scope++);
    }
    public function __construct($args = array())
    {
        $this->args = $args;
    }
    public function as_sql($model)
    {
        $myargs = $this->args;
        ksort($myargs);
        $myargs = $this->_handleArgs($myargs);
        $lines = array();
        $newscope = $this->getNewScope();
        $tmp = $this->_as_sql($model, $myargs, $newscope);
        if ($tmp != '()') {
            $lines[] = $tmp;
        }
        foreach ($this->args as $v) {
            if ($v instanceof self) {
                $tmp = $v->as_sql($model);
                if ($tmp != '()') {
                    $lines[] = $tmp;
                }
            }
        }

        return implode(' '.$this->type.' ', $lines);
    }
    public function _handleArgs($args)
    {
        $newargs = array();
        foreach ($args as $k => $v) {
            if (!($v instanceof self)) {
                $tmp = explode('__', $k, 2);
                if (count($tmp) > 1) {
                    if (!isset($newargs[$tmp[0]])) {
                        $newargs[$tmp[0]] = array();
                    }
                    $newargs[$tmp[0]][$tmp[1]] = $v;
                } else {
                    if (!isset($newargs[$tmp[0]])) {
                        $newargs[$tmp[0]] = $v;
                    }
                }
            }
        }
        foreach ($newargs as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if (strpos($k2, '__') !== false) {
                        $newargs[$k] = $this->_handleArgs($v);
                    }
                }
            }
        }

        return $newargs;
    }
    private function getField($fields,$k) {
        foreach ($fields as $field) {
            if ($k == $field->name || $k == $field->dbname) {
                return $field;
            }
        }
        return false;
    }
    public function _as_sql($model, $args, $scope)
    {
        $exps = array();
        $fields = $model::getFields();
        foreach ($args as $k => $v) {
            $found = false;
            $field = $this->getField($fields,$k);
            if ($field) {
                $found = true;
                $operator = '';
                $f = $k;
                if (is_array($v)) {
                    $exp = $this->solveOperators($v,$scope,$field,$f);
                    if ($exp) {
                        $operator = 'adsf';
                        $exps[] = $exp;
                    }
                } else {
                    $operator = '=';
                    $exps[] = $this->solveEqualOperator($v,$f,$scope);
                    
                }
                if ($operator == '') {
                    $exps[] = $this->solveForeignKey($model,$v,$f,$scope);
                    
                }
            }
            if (!$found && $class = $model::has($k)) {
                $found = true;                    
                $exps[] = $this->solveReverseForeignKey($class,$scope,$v,$f);
            }
            if (!$found) {
                throw new DBException(sprintf('Unknown field: %s in model: %s', $k, $model));
            }
        }

        return '('.implode(' '.$this->type.' ', $exps).')';
    }
    private function solveOperators($v,$scope,$field,$f) {
        $operator = '';
        foreach ($v as $k2 => $v2) {
            if ($v2 instanceof \DateTime) {
                if ($field instanceof DateTimeField) {
                    $v2 = $v2->format('Y-m-d H:i:s');
                } elseif ($field instanceof DateField) {
                    $v2 = $v2->format('Y-m-d');
                } else {
                    throw new DBException('Field '.$field->name.' doesnt take a datetime');
                }
            }
            switch ($k2) {
                case 'contains':
                    $operator = 'LIKE';
                    $v2 = DB::escape($v2);
                    $value = "'%$v2%'";
                    break;
                case 'in':
                    $operator = 'IN';
                    if (is_array($v2)) {
                        $value = sprintf('(%s)', implode(',', $v2));
                    } else {
                        $value = sprintf('(%s)', $v2);
                    }
                    break;
                case 'lte':
                    $operator = '<=';
                    $v2 = DB::escape($v2);
                    $value = "'$v2'";
                    break;
                case 'gte':
                    $operator = '>=';
                    $v2 = DB::escape($v2);
                    $value = "'$v2'";
                    break;
                case 'lt':
                    $operator = '<';
                    $v2 = DB::escape($v2);
                    $value = "'$v2'";
                    break;
                case 'gt':
                    $operator = '>';
                    $v2 = DB::escape($v2);
                    $value = "'$v2'";
                    break;
                case 'isnull':
                    $operator = sprintf('IS %s NULL', $v2 ? '' : 'NOT');
                    $value = '';
                    break;
                default:
                    // nothing
            }
            if ($operator != '') {
                return "$scope.`$f` $operator $value";
            }
        }
    }
    private function solveEqualOperator($v,$f,$scope) {
        if ($v instanceof BaseModel) {
            $value = $v->id;
            $f = sprintf('%s_id', $f);
        } else {
            $value = sprintf("'%s'", DB::escape($v));
        }
        return "$scope.`$f` = $value";
    }
    private function solveForeignKey($model,$v,$f,$scope) {
        $m = $model::getForeignKeyModel($f);
        $modelName = $m::getName();
        $newscope = $this->getNewScope();
        return "$scope.{$f}_id in (SELECT $newscope.id FROM $modelName $newscope WHERE ".$this->_as_sql($m, $v, $newscope).')';
    }
    private function solveReverseForeignKey($class,$scope,$v,$f) {
        list($m, $f) = $class;
        $modelName = $m::getName();
        $newscope = $this->getNewScope();
        return "{$scope}.id in (SELECT {$newscope}.$f->dbname FROM $modelName {$newscope} WHERE ".$this->_as_sql($m, $v, $newscope).')';
    }
}
