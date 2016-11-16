<?php

namespace penguin\db;

/**
 * ODBCResultSet.
 *
 * @author johs
 */
class ODBCResultSet
{
    public $result;
    public $numrow = 0;
    public $maxrow;
    public function __construct($result, $query)
    {
        $this->result = $result;
    }
    public function next()
    {
        if ($this->numrow == 0) {
            $res = odbc_fetch_array($this->result, 1);
        } else {
            $res = odbc_fetch_array($this->result);
        }
        ++$this->numrow;

        return $res;
    }
    public function count()
    {
        if ($res = odbc_num_rows($this->result) == -1) {
            $res = 0;
            $tmp = $this->numrow;
            $this->moveFirst();
            while ($row = $this->next()) {
                $res++;
            }
            $this->numrow = $tmp;
            odbc_fetch_array($this->result, $tmp);

            return $res;
        } else {
            return $res;
        }
    }
    public function fetchFields()
    {
        $fields = array();
        for ($i = 0; $i < odbc_num_fields($this->result); ++$i) {
            $fields[] = odbc_field_name($this->result, $i);
        }

        return $fields;
    }
    public function __destruct()
    {
        unset($this->result);
    }
    public function moveFirst()
    {
        $this->numrow = 0;
    }
    public function close()
    {
        odbc_free_result($this->result);
    }
}
