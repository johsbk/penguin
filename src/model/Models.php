<?php

namespace penguin\model;

class Models
{
    public static $models = array();
    public static function checkIntegrity()
    {
        foreach (static::$models as $m) {
            $m::checkIntegrity();
        }
    }
}
