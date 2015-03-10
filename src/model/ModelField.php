<?php
namespace penguin\model;
use \penguin\common\Functions;
use \penguin\db\DB;
use \Exception;
abstract class ModelField {
	var $name;
	var $dbname;
	var $primary;
	var $allownull;
	var $default;
	var $extra;
	var $_model;
	function __construct($dict=array()) {
		$this->primary = Functions::nz($dict['primary'],false);
		$this->default = Functions::nz($dict['default'],NULL);
		$this->extra = Functions::nz($dict['extra'],NULL);
		$this->allownull = Functions::nz($dict['allownull'],false);
	}
	function checkIntegrity($table) {
		echo "$this->name: ";
		$q = DB::fetchOne("SHOW COLUMNS FROM `$table` WHERE Field='$this->dbname'");
		$err = "";
		if (!$q) {
			$err .=" Field not found";
			DB::query("ALTER TABLE  `$table` ADD  `$this->dbname` ".$this->getType().(!$this->allownull ? ' NOT NULL' : '').($this->extra != "" ? " $this->extra" : '').($this->default!=null ? " DEFAULT '$this->default'" : ''));
			$err .= ": added!";
		} else {
			if ($q['Type'] != $this->getType()) $err.= " Type is $q[Type] but should be: ".$this->getType();
			if (($q['Key']=='PRI') != $this->primary) {
				if ($this->primary) $err .= " Should be primary";
				else $err .= " Should not be primary";
			}
			if ($q['Default'] != $this->default) $err.= " Default is $q[Default] but should be: $this->default";
			if ($q['Extra'] != $this->extra) $err.= " Extra is $q[Extra] but should be: $this->extra";
		}
		if ($err == "") {
			echo " OK";
		} else {
			echo $err;
		}
		echo "<br />";
		
	}
	function set($value) {
		return $value;
	}
	function get($value) {
		return $value;
	}
	function toString($value) {
		return is_null($value) ? 'NULL' : (string) $value;
	}
	function skip () {
		return false;
	}
	abstract function getType();
	abstract function getFormType();
	function getFormField() {
		$f = new \penguin\forms\CharField();
		$f->name = $this->name;
		$f->dbname = $this->dbname;
		return $f;
	}
}
?>