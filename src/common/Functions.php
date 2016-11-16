<?php

namespace penguin\common;

use penguin\db\DB;

class Functions
{
    public static $foundOldestAncestors = array();
    public static $gottenArgs = array();
    private static $jsdone = false;
    public static function js()
    {
        if (!self::$jsdone) {
            self::$jsdone = true;

            return '<script type="text/javascript" src="'.TEMPLATE_PATH.'common/js/F.js"></script>';
        }
    }
    public static function makeAlias($string)
    {
        $str = str_replace('-', ' ', $string);

        $str = preg_replace(array('/\s+/', '/[^A-Za-z0-9\-]/'), array('-', ''), $str);

        return trim(strtolower($str));
    }
    public static function phpSelf()
    {
        return isset($_SERVER['php_self']) ? $_SERVER['php_self'] : '';
    }
    public static function nz(&$var, $default = '')
    {
        return isset($var) ? $var : $default;
    }
    public static function formatNumber($number, $digs = 2)
    {
        if ($number == '') {
            $number = 0;
        }
        if (!is_numeric($number)) {
            $number = 0;
        }
        return number_format($number, $digs, ',', '.');
    }
    public static function pctToDbl($pct)
    {
        return str_replace('%', '', $pct);
    }
    public static function getUSvalue($val)
    {
        $val = str_replace('.', '', $val);
        if (strlen($val) > 3 && $val{strlen($val) - 3} == ',') {
            $val{strlen($val) - 3} = '.';
        }
        if (strlen($val) > 2 && $val{strlen($val) - 2} == ',') {
            $val{strlen($val) - 2} = '.';
            $val .= '0';
        }

        return $val;
    }
    public static function getArgs($except = '')
    {
        if (isset(self::$gottenArgs[$except])) {
            return self::$gottenArgs[$except];
        }
        $str = self::phpSelf().'?';
        $bool = false;
        $remove = false;
        $excps = explode(';', $except);
        foreach ($_GET as $key => $value) {
            if (isset($_GET[$key])) {
                foreach ($excps as $excpt) {
                    if ($excpt == $key) {
                        $remove = true;
                    }
                }
                if (!$remove) {
                    if ($bool) {
                        $str = $str.'&';
                    } else {
                        $bool = true;
                    }
                    $str = $str.$key.'='.$value;
                } else {
                    $remove = false;
                }
            }
        }
        self::$gottenArgs[$except] = $str;

        return $str;
    }
    public static function findOldestAncestor($table, $id)
    {
        if (isset(self::$foundOldestAncestors[$table.$id])) {
            return self::$foundOldestAncestors[$table.$id];
        }
        $next = $id;
        $last = $id;
        while (true) {
            if (!is_numeric($next = self::findParent($table, $next))) {
                break;
            }
            $last = $next;
        }
        self::$foundOldestAncestors[$table.$id] = $last;

        return $last;
    }
    public static function findParent(&$table, $id)
    {
        $q = DB::fetchOne('SELECT * FROM '.$table.' WHERE delomraade_id='.$id);
        if (!$q) {
            return false;
        }

        return $q['omraade_id'];
    }
    public static function findChildren($parents, $table)
    {
        $ar = array();
        foreach ($parents as $parent) {
            if (!in_array($parent, $ar)) {
                $ar[] = $parent;
                self::findChildrenSub($parent, $ar, $table);
            }
        }

        return $ar;
    }
    private static function findChildrenSub($id, &$ar, $table)
    {
        $delomraade_id='delomraade_id';
        $rs = DB::fetch("SELECT * FROM $table WHERE omraade_id=$id");
        while ($row = $rs->next()) {
            if (!in_array($row[$delomraade_id], $ar)) {
                $ar[] = $row[$delomraade_id];
                self::findChildrenSub($row[$delomraade_id], $ar, $table);
            }
        }
    }
}
