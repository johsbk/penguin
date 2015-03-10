<?php
namespace templates\db;
class ResultSet {
	var $result;
	function __construct($result) {
		$this->result = $result;
	}
	function next() {
		
		#$tmp = microtime(true);
		$res = mysqli_fetch_array($this->result);
		#DB::timespentindb += microtime(true) -$tmp;
		return $res;
	}
	function nextAssoc() {
		return mysqli_fetch_assoc($this->result);
	}
	function count() {
		return mysqli_num_rows($this->result);
	}
	function fetchFields() {
		return mysqli_fetch_fields($this->result);
	}
	function __destruct() {
		unset($this->result);
	}
	function moveFirst() {
		if ($this->count() > 0) { 
			mysqli_data_seek($this->result, 0);
		}
	}
}
?>