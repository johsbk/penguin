<?php

namespace penguin\auth\models;

use penguin\model\BaseModel;
use penguin\model\IdField;
use penguin\model\VarcharField;

class Groups extends BaseModel
{
    public static $id;
    public static $name;
    protected static function localinit()
    {
        self::$id = new IdField();
        self::$name = new VarcharField();
    }
}
