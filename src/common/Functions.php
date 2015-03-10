<?php
namespace penguin\common;
use \penguin\db\DB;
use \Exception;
class Functions {
	static $foundOldestAncestors = array();
	static $gottenArgs = array();
	private static $jsdone = false;
	static function js() {
		if (!self::$jsdone) {
			self::$jsdone = true;
			return "<script type=\"text/javascript\" src=\"".TEMPLATE_PATH."common/js/F.js\"></script>";
		}
	}
	static function makeAlias($string) {
		$str = str_replace('-', ' ', $string);


		$str = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $str);

		$str = trim(strtolower($str));
		return $str;
	}
	static function phpSelf() {
		return (isset($_SERVER['php_self']) ? $_SERVER['php_self'] : '');
	}
	static function nz(&$var,$default="") {
		return (isset($var) ? $var : $default);
	}
	static function formatNumber($number,$digs=2) {
		if ($number=="")$number =0;
		if (!is_numeric($number)) $number=0;
		$tmp =number_format($number, $digs, ',', '.');
		
		return $tmp; 
	}
	static function pctToDbl($pct) {
		return str_replace("%","",$pct);
	}
	static function getUSvalue($val) {
		$val = str_replace(".","",$val);
		if (strlen($val)>3 && $val{strlen($val)-3} == ",") $val{strlen($val)-3} = ".";
		if (strlen($val) > 2 && $val{strlen($val)-2} == ",") {
			$val{strlen($val)-2} = ".";
			$val .="0";
		}
		return $val;
	}
	static function getArgs($except = "") {
		if (isset(Functions::$gottenArgs[$except])) return Functions::$gottenArgs[$except];
		global $_GET,$_SERVER;
		$str = Functions::phpSelf()."?";
		$bool = false;
		$remove = false;
		$excps = explode(';',$except);
		foreach($_GET as $key => $value) {
			if (isset($_GET[$key])) {
				foreach ($excps as $excpt) {
					if($excpt == $key) {
						$remove = true;
					}
				}
				if (!$remove) {
					if ($bool) {
						$str = $str."&";
					} else {
						$bool = true;
					}
					$str = $str.$key."=".$value;
				} else {
					$remove = false;
				}
			}
		}
		Functions::$gottenArgs[$except] = $str;
		return $str;
	}
	static function findOldestAncestor($table,$id) {
		if (isset(Functions::$foundOldestAncestors[$table.$id])) return Functions::$foundOldestAncestors[$table.$id];
		$next = $id;
		$last = $id;
		while(true) {
			if(!is_numeric($next = Functions::findParent($table,$next))) break;
			$last = $next;
		}
		Functions::$foundOldestAncestors[$table.$id] = $last;
		return $last;
	}
	static function findParent(&$table,$id) {
		$q = DB::fetchOne("SELECT * FROM ".$table." WHERE delomraade_id=".$id);
		if (!$q) return false;
		return $q['omraade_id'];
	}
	static function findChildren($parents,$table) {
		$ar = array();
		foreach ($parents as $parent) {
			if (!in_array($parent, $ar)) {
				$ar[] = $parent;
				self::findChildrenSub($parent,$ar,$table);
			}
		}
		return $ar;
	}
	private static function findChildrenSub($id,&$ar,$table) {
		$rs = DB::fetch("SELECT * FROM $table WHERE omraade_id=$id");
		while ($row = $rs->next()) {
			if (!in_array($row['delomraade_id'], $ar)) {
				$ar[] = $row['delomraade_id'];
				self::findChildrenSub($row['delomraade_id'], $ar,$table);
			} 
		}
	}
}