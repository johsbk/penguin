<?php

namespace penguin\form;

class Validate
{
    public static function number($val)
    {
        if (!is_numeric($val)) {
            throw new Exception("Not a number: $val");
        }

        return $val;
    }
    public static function string($val)
    {
        return addslashes($val);
    }
}
