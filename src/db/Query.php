<?php

namespace penguin\db;

class Query
{
    public $model;
    public $where = array();
    public $order_by = '';
    public $limit;
    public $start;
    public function __construct($model)
    {
        $this->model = $model;
    }
    public function add_q($q)
    {
        $this->where[] = $q->as_sql($this->model);
    }
    public function add_negate_q($q)
    {
        $this->where[] = 'NOT ('.$q->as_sql($this->model).')';
    }
    public function compiler()
    {
        return new SQLCompiler($this);
    }
}
