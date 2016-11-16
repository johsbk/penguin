<?php

namespace penguin\db;

class SQLCompiler
{
    public $query;
    public function __construct($query)
    {
        $this->query = $query;
    }
    public function as_sql($select)
    {
        $model = $this->query->model;
        $where = '';
        if (count($this->query->where) > 0) {
            $where = 'AND '.implode(' AND ', $this->query->where);
        }
        /*if ($this->query->order_by) {
            if (substr($this->query->order_by,0,1)=="-") {
                $order_by = substr($this->query->order_by,1);
                $way = 'DESC';
            } else {
                $order_by = $this->query->order_by;
                $way = 'ASC';
            }
            $order_by = "ORDER BY ".$order_by.' '.$way;
        } else {
            $order_by = '';
        }*/
        if ($this->query->limit) {
            $start = $this->query->start ? $this->query->start : 0;
            $limit = "LIMIT $start, {$this->query->limit}";
        } else {
            $limit = '';
        }
        list($tables, $joins, $order_by) = $this->tables($model, $this->query->order_by);
        foreach ($tables as $k => $v) {
            $tables[$k] = $v.' t'.$k;
        }
        $tablestr = implode(',', $tables);
        if ($order_by) {
            $order_by = 'ORDER BY '.$order_by;
        }
        if ($joins) {
            $joins = ' AND '.$joins;
        }
        if ($select == 'DELETE') {
            $sql = "$select FROM t0 USING $tablestr WHERE 0=0 $joins $where $order_by $limit";
        } else {
            $sql = "$select FROM $tablestr WHERE 0=0 $joins $where $order_by $limit";
        }
        //echo $sql;
        return $sql;
    }
    public function tables($model, $order_by)
    {
        $tables = array();
        $joins = array();
        $modelName = $model::getName();
        $tables[] = $modelName;
        $order = array();
        if ($order_by) {
            if (!is_array($order_by)) {
                $order_by = array($order_by);
            }
            foreach ($order_by as $a) {
                if (substr($a, 0, 1) == '-') {
                    $desc = true;
                    $a = substr($a, 1);
                } else {
                    $desc = false;
                }
                if (strpos($a, '__') !== false) {
                    $this->addTable($model, $tables, $joins, $order, $a, 0, $desc);
                } else {
                    $order[] = 't0.'.$a.($desc ? ' DESC' : '');
                }
            }
        }

        return array($tables, implode(' AND ', $joins), implode(',', $order));
    }
    public function addTable($model, &$tables, &$joins, &$order, $a, $tindex, $desc)
    {
        //TODO: better support for multiple order by
        $tmp = explode('__', $a, 2);
        $m = $model::getForeignKeyModel($tmp[0]);
        $mname = $m::getName();
        $tables[] = $mname;
        $joins[] = 't'.$tindex.'.'.$tmp[0].'_id=t'.($tindex + 1).'.id';
        if (strpos($tmp[1], '__') !== false) {
            $this->addTable($m, $tables, $joins, $order, $tmp[1], $tindex + 1, $desc);
        } else {
            $order[] = 't'.($tindex + 1).'.'.$tmp[1].($desc ? ' DESC' : '');
        }
    }
    public function results()
    {
        return DB::fetchArrayAssoc($this->as_sql('SELECT t0.*'));
    }
    public function count()
    {
        //throw new \Exception($this->as_sql('SELECT count(*) as count'));
        $q = DB::fetchOne($this->as_sql('SELECT count(*) as count'));

        return $q['count'];
    }
    public function delete()
    {
        DB::query($this->as_sql('DELETE'));
    }
}
