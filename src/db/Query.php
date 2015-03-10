<?php
namespace templates\db;
class Query {
	var $model;
	var $where = array();
	var $order_by = '';
	var $limit;
	var $start;
	function __construct($model) {
		$this->model = $model;
	}
	function add_q($q) {
		$this->where[] = $q->as_sql($this->model);
	}
	function add_negate_q($q) {
		$this->where[] = 'NOT ('.$q->as_sql($this->model).')';
	}
	function compiler() {
		return new SQLCompiler($this);
	}
}