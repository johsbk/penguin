<?php

namespace penguin\db;

class DB
{
    public static $user;
    public static $host;
    public static $pass;
    public static $db;
    public static $conn;
    public static $timespentindb = 0;

    public static function login($user = MYSQL_USER, $pass = MYSQL_PASS, $host = MYSQL_HOST, $db = MYSQL_DB)
    {
        self::$user = $user;
        self::$pass = $pass;
        self::$host = $host;
        self::$db = $db;
        static::connect();
    }
    private static function connect()
    {
        if (!self::$conn = mysqli_connect(self::$host, self::$user, self::$pass)) {
            throw new DBException('mysql_connect error: '.mysqli_error(self::$conn));
        }
        if (!mysqli_select_db(self::$conn, self::$db)) {
            throw new DBException('mysql_select_db error:'.mysqli_error(self::$conn));
        }
        self::query("SET NAMES 'utf8'");
    }
    public static function logout()
    {
        mysqli_close(self::$conn);
    }
    /**
     * Enter description here...
     *
     * @param unknown_type $query
     *
     * @return unknown
     */
    public static function query($query, $firsttry = true)
    {
        if (!$var = mysqli_query(self::$conn, $query)) {
            if (mysqli_errno(self::$conn)== 2006) {
                    self::connect();
                    if ($firsttry) {
                        return static::query($query, false);
                    }
            } else {
                throw new DBException('('.$query.') had an error: '.mysqli_error(self::$conn));
            }
        }
        return $var;
    }
    /**
     * Enter description here...
     *
     * @param unknown_type $query
     *
     * @return ResultSet
     */
    public static function fetch($query)
    {
        $var = self::query($query);

        return new ResultSet($var);
    }
    public static function fetchArray($query)
    {
        $rs = self::fetch($query);
        $res = array();
        while ($r = $rs->next()) {
            $res[] = $r;
        }

        return $res;
    }
    public static function fetchArrayAssoc($query)
    {
        $rs = self::fetch($query);
        $res = array();
        while ($r = $rs->nextAssoc()) {
            $res[] = $r;
        }

        return $res;
    }
    public static function fetchOne($query)
    {
        $var = self::query($query);
        return mysqli_fetch_array($var);
    }
    public static function getLastEntry($table)
    {
        return self::fetchOne('SELECT * FROM '.$table.' ORDER BY id DESC LIMIT 0,1');
    }
    public static function getLastId()
    {
        return mysqli_insert_id(self::$conn);
    }
    public static function escape($str)
    {
        if (is_object($str)) {
            throw new DBException('you have given me an object');
        }

        return mysqli_escape_string(self::$conn, $str);
    }
    public static function ezQuery($type, $table, $array, $where = '', $autoquotes = true)
    {
        $qry = '';
        if ($type == 'INSERT') {
            $qry .= "INSERT INTO `$table`(";
            $values = 'values (';
            $i = 0;
            foreach ($array as $key => $val) {
                if ($i++ != 0) {
                    $qry .= ',';
                    $values .= ',';
                }
                $qry .= "`$key`";
                if ($val !== 0 && ($val == 'NULL' || $val === null)) {
                    $values .= 'NULL';
                } elseif ($val == 'now()') {
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
        } elseif ($type == 'UPDATE') {
            $qry .= "UPDATE `$table` SET ";
            $i = 0;
            foreach ($array as $key => $val) {
                if ($i++ != 0) {
                    $qry .= ', ';
                }
                if ($val == 'NULL' || $val === null) {
                    $val = 'NULL';
                } elseif ($val == 'now()') {
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
            if ($where != '') {
                $qry .= " WHERE $where";
            }
        } elseif ($type == 'DELETE') {
            $qry .= "DELETE FROM $table WHERE $where";
        }

        return self::query($qry);
    }
}
