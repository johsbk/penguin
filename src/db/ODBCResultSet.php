<?php
namespace templates\db;
/**
 * 
 * ODBCResultSet
 * @author johs
 *
 */
class ODBCResultSet {
	var $result;
	var $numrow = 0;
	var $maxrow;
	function __construct($result,$query) {
		$this->result = $result;
	}
	function next() {
		if ($this->numrow==0) {
			$res = odbc_fetch_array($this->result,1);	
		} else {
			$res = odbc_fetch_array($this->result);
		}
		$this->numrow++;
		return $res;
	}
	function count() {
		if ($res = odbc_num_rows($this->result) == -1) {
			$res = 0;
			$tmp = $this->numrow;
			$this->moveFirst();
			while ($row=$this->next()) $res++;
			$this->numrow = $tmp;
			odbc_fetch_array($this->result,$tmp);
			return $res;
		} else return $res;
	}
	function fetchFields() {
		$fields = array();
		for ($i=0;$i<odbc_num_fields($this->result);$i++) {
			$fields[] = odbc_field_name($this->result, $i);
		}
		return $fields;
	}
	function __destruct() {
		unset($this->result);
	}
	function moveFirst() {
		$this->numrow=0;
	}
	function close() {
		odbc_free_result($this->result);
	}
}
?>