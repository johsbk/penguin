<?php

namespace penguin\auth\models;

use penguin\model\BaseModel;
use penguin\model\IdField;
use penguin\model\UintField;
use penguin\model\EnumField;
use penguin\model\ForeignKeyField;

class GroupRights extends BaseModel
{
    public static $id;
    public static $group;
    public static $right;
    public static $allow;
    protected static function localinit()
    {
        self::$id = new IdField();
        self::$group = new ForeignKeyField(array('model' => 'Groups'));
        self::$right = new UintField();
        self::$allow = new EnumField(array('options' => array('Allow', 'Deny')));
    }
}
