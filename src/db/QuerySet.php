<?php

namespace penguin\db;

use penguin\model\ForeignKeyField;

class QuerySet implements \Iterator
{
    const MODEL = 'model';
    public $query;
    public $model;
    private $_result_cache;
    private $position = 0;
    public function __construct($dict = array())
    {
        $this->model = isset($dict[self::MODEL]) ? $dict[self::MODEL] : null;
        $this->query = isset($dict['query']) ? $dict['query'] : new Query($this->model);
    }
    public function filter($dict = array())
    {
        return $this->_filter_or_exclude(false, $dict);
    }
    public function exclude($dict = array())
    {
        return $this->_filter_or_exclude(true, $dict);
    }
    public function count()
    {
        if (!$this->_result_cache) {
            return $this->query->compiler()->count();
        } else {
            return count($this->_result_cache);
        }
    }
    public function delete()
    {
        $this->query->compiler()->delete();
    }
    public function limit($offset, $limit)
    {
        $clone = clone $this;
        $clone->query->limit = $limit;
        $clone->query->start = $offset;

        return $clone;
    }
    public function first()
    {
        $clone = clone $this;
        $clone->query->limit = 1;
        $clone->query->start = 0;
        if ($clone->valid()) {
            return $clone->current();
        } else {
            return null;
        }
    }
    public function exists()
    {
        if ($this->_result_cache) {
            return count($this->_result_cache) > 0;
        } else {
            return $this->first();
        }
    }
    public function order_by($order_by)
    {
        $this->query->order_by = $order_by;

        return $this;
    }
    private function _filter_or_exclude($negate, $dict = array())
    {
        $clone = clone $this;
        $clone->query = clone $this->query;
        if (is_array($dict) && count($dict) == 0) {
            return $clone;
        }
        if (!$negate) {
            $clone->query->add_q(new Qand($dict));
        } else {
            $clone->query->add_negate_q(new Qand($dict));
        }

        return $clone;
    }
    public function toArray()
    {
        $this->_fetch_all();

        return $this->_result_cache;
    }
    private function _fetch_all()
    {
        if (!$this->_result_cache) {
            $this->_result_cache = array();
            foreach ($this->query->compiler()->results() as $row) {
                $this->_result_cache[] = new $this->model($row);
            }
        }
    }
    public function as_sql()
    {
        echo $this->query->compiler()->as_sql('SELECT *');
    }
    public function all()
    {
        return clone $this;
    }
    public function rewind()
    {
        $this->_fetch_all();
        $this->position = 0;
    }
    public function current()
    {
        $this->_fetch_all();

        return $this->_result_cache[$this->position];
    }
    public function key()
    {
        $this->_fetch_all();

        return $this->position;
    }
    public function next()
    {
        $this->_fetch_all();
        ++$this->position;
    }
    public function valid()
    {
        $this->_fetch_all();

        return isset($this->_result_cache[$this->position]);
    }
    public function prefetch_related($name)
    {
        $model = $this->model;
        $field = $model::getField($name);
        if (!$field instanceof ForeignKeyField) {
            throw new DBException($name.' is not a foreign key');
        }
        $queryset = new self([self::MODEL => $field->model]);
        $q = $this->query->compiler()->as_sql('SELECT t0.'.$field->dbname);
        $queryset = $queryset->filter(['id__in' => $q]);
        foreach ($queryset as $row) {
            $field->cache[$row->id] = $row;
        }

        return $this;
    }
}
