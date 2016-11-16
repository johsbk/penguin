<?php
namespace penguin\db;
class ODBC {
	static $conn;
	
	
	static function login($str) {
		if(!self::$conn = @odbc_connect($str, "", "",SQL_CUR_USE_DRIVER)) throw new DBException('Failed connect: '.odbc_errormsg());
	}
	static function logout() {
		odbc_close(self::$conn);
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $query
	 * @return unknown
	 */
	static function query($query) {
		if(!$var = \odbc_exec(self::$conn,$query)) throw new DBException("(".$query.") had an error: ".\odbc_errormsg());
		return $var;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $query
	 * @return ODBCResultSet
	 */
	static function fetch($query) {
		$var = self::query($query);
		return new ODBCResultSet($var,$query);
	}
	static function fetchArray($query) {
		$rs = self::fetch($query);
		$res = array();
		while ($r = $rs->next()) {
			$res[] = $r;
		}
		$rs->close();
		return $res;
	}
	static function fetchArrayAssoc($query) {
		$rs = self::fetch($query);
		while ($r = $rs->nextAssoc()) {
			$res[] = $r;
		}
		return $res;
	}
	static function fetchOne($query) {
		$rs = self::fetch($query);
		$res = $rs->next();
		return $res;
	}
	static function getLastEntry($table) {
		return self::fetchOne("SELECT * FROM ".$table." ORDER BY id DESC LIMIT 0,1");
	}
	static function ezQuery($type,$table,$array,$where="",$order="",$limit="") {
		$qry = "";
		if ($type=="INSERT") {
			$qry .= "INSERT INTO $table(";
			$values = "values (";
			$i = 0;
			foreach ($array as $key => $val) {
				if ($i++!=0) {
					$qry .= ",";
					$values .= ",";
				}
				$qry .= "`$key`";
				if ($val=="NULL") {
					$values .= "NULL";
				} else {
					$values .= "'$val'";
				}
			}
			$qry .= ") $values)";
		} elseif ($type=="UPDATE") {
			$qry .= "UPDATE $table SET ";
			$i = 0;
			foreach ($array as $key => $val) {
				if ($i++!=0) {
					$qry .= ", ";
				}
				$qry .= "`$key` = '$val'";
			}
			if ($where != "") {
				$qry .= " WHERE $where";
			}
		} elseif ($type=="DELETE") {
			$qry .= "DELETE FROM $table WHERE $where";
		}
		return self::query($qry);
	}
}
?>