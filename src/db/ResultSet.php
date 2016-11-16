<?php

namespace penguin\db;

class ResultSet
{
    public $result;
    public function __construct($result)
    {
        $this->result = $result;
    }
    public function next()
    {

        //$tmp = microtime(true);
        $res = mysqli_fetch_array($this->result);
        //DB::timespentindb += microtime(true) -$tmp;
        return $res;
    }
    public function nextAssoc()
    {
        return mysqli_fetch_assoc($this->result);
    }
    public function count()
    {
        return mysqli_num_rows($this->result);
    }
    public function fetchFields()
    {
        return mysqli_fetch_fields($this->result);
    }
    public function __destruct()
    {
        unset($this->result);
    }
    public function moveFirst()
    {
        if ($this->count() > 0) {
            mysqli_data_seek($this->result, 0);
        }
    }
}
