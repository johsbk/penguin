<?php

namespace penguin\auth\models;

use penguin\model\BaseModel;
use penguin\model\IdField;
use penguin\model\VarcharField;
use penguin\db\DB;

class Profiles extends BaseModel
{
    public static $id;
    public static $name;
    public static $password;
    public static $salt;
    public static $email;
    public static $fullname;
    protected static function localinit()
    {
        self::$id = new IdField();
        self::$name = new VarcharField(array('maxlength' => 50));
        self::$password = new VarcharField(array('maxlength' => 100));
        self::$email = new VarcharField(array('maxlength' => 100));
        self::$fullname = new VarcharField(array('maxlength' => 100));
        self::$salt = new VarcharField();
        self::addHas('ProfileRights');
        self::addHas('GroupProfiles');
    }
    public function checkRight($right)
    {
        $q = $this->profile_rights->filter(array('right' => $right, 'allow' => 'Deny'))->exists();
        if ($q) {
            return false;
        }
        $q = $this->profile_rights->filter(array('right' => $right, 'allow' => 'Allow'))->exists();
        if ($q) {
            return true;
        }
        $q = DB::fetchOne("SELECT * FROM group_rights gr, group_profiles gp WHERE gr.`right`=$right and allow='Deny' and gr.group_id=gp.group_id and gp.profile_id=".$this->id);
        if ($q) {
            return false;
        }
        $q = DB::fetchOne("SELECT * FROM group_rights gr, group_profiles gp WHERE gr.`right`=$right and allow='Allow' and gr.group_id=gp.group_id and gp.profile_id=".$this->id);
        if ($q) {
            return true;
        }
    }
    public function inGroup($name)
    {
        foreach ($this->group_profiles as $gp) {
            if ($gp->group->name == "$name") {
                return true;
            }
        }

        return false;
    }
    public function __toString()
    {
        return $this->name;
    }
}
