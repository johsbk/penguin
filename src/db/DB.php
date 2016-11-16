<?php
namespace penguin\db;
use \Exception;
class DB {
	static $user;
	static $host;
	static $pass;
	static $db;
	static $conn;
	static $timespentindb=0;
	
	
	static function login($user=MYSQL_USER,$pass=MYSQL_PASS,$host=MYSQL_HOST,$db=MYSQL_DB) {
		self::$user = $user;
		self::$pass = $pass;
		self::$host = $host;
		self::$db = $db;
		#$tmp = microtime(true);
		static::connect();
		
	}
	private static function connect () {
		if (!self::$conn = mysqli_connect(self::$host,self::$user,self::$pass)) throw new DBException('mysql_connect error: '.mysqli_error(self::$conn));
		if (!mysqli_select_db(self::$conn,self::$db)) throw new DBException('mysql_select_db error:'.mysqli_error(self::$conn));
		#$this->timespentindb += microtime(true) -$tmp;
		DB::query("SET NAMES 'utf8'");
	}
	static function logout() {
		#$tmp = microtime(true);
		mysqli_close(self::$conn);
		#this->timespentindb += microtime(true) -$tmp;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $query
	 * @return unknown
	 */
	static function query($query,$firsttry=true) {
		#$tmp = microtime(true);
		if (!$var = mysqli_query(self::$conn,$query)) {
			switch (mysqli_errno(self::$conn)) {
				case 2006:
					self::connect();
					if ($firsttry)
						return static::query($query,false);
				default:
					throw new DBException("(".$query.") had an error: ".mysqli_error(self::$conn));	
			}
			
		} 
		#$this->timespentindb += microtime(true) -$tmp;
		return $var;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $query
	 * @return ResultSet
	 */
	static function fetch($query) {
		$var = self::query($query);
		return new ResultSet($var);
	}
	static function fetchArray($query) {
		$rs = self::fetch($query);
		$res = array();
		while ($r = $rs->next()) {
			$res[] = $r;
		}
		return $res;
	}
	static function fetchArrayAssoc($query) {
		$rs = self::fetch($query);
		$res = array();
		while ($r = $rs->nextAssoc()) {
			$res[] = $r;
		}
		return $res;
	}
	static function fetchOne($query) {
		$var = self::query($query);
#		$tmp = microtime(true)-$tmp;
		$res =  mysqli_fetch_array($var);
#		$this->timespentindb += microtime(true) -$tmp;
		return $res;
	}
	static function getLastEntry($table) {
		return self::fetchOne("SELECT * FROM ".$table." ORDER BY id DESC LIMIT 0,1");
	}
	static function getLastId() {
		return mysqli_insert_id(self::$conn);
	}
	static function escape($str) {
		if (is_object($str)) throw new DBException("you have given me an object");
		return mysqli_escape_string(self::$conn,$str);
	}
	static function ezQuery($type,$table,$array,$where="",$order="",$limit="",$autoquotes=true) {
		$qry = "";
		if ($type=="INSERT") {
			$qry .= "INSERT INTO `$table`(";
			$values = "values (";
			$i = 0;
			foreach ($array as $key => $val) {
				if ($i++!=0) {
					$qry .= ",";
					$values .= ",";
				}
				$qry .= "`$key`";
				if ($val !==0 && ($val=="NULL" || $val===NULL)) {
					$values .= "NULL";
				} elseif ($val=="now()") {
					$values .= $val;
				} else {
					$val = addslashes($val);
					if ($autoquotes) {
						$values .= "'$val'";
					} else {
						$values .= "$val";
					}
				}
			}
			$qry .= ") $values)";
		} elseif ($type=="UPDATE") {
			$qry .= "UPDATE `$table` SET ";
			$i = 0;
			foreach ($array as $key => $val) {
				if ($i++!=0) {
					$qry .= ", ";
				}
				if ($val=="NULL" || $val===NULL) {
					$val = "NULL";
				} elseif ($val=="now()") {
					$values .= $val;
				} else {
					$val = addslashes($val);
					if ($autoquotes) {
						$val = "'$val'";
					} else {
						$val = "$val";
					}
				}
				$qry .= "`$key` = $val";
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