<?php
namespace templates\auth\models;
use \templates\model\BaseModel;
use \templates\model\IdField;
use \templates\model\UintField;
use \templates\model\EnumField;
use \templates\model\ForeignKeyField;
class ProfileRights extends BaseModel {
	static $id;
	static $profile;
	static $right;
	static $allow;
	protected static function localinit() {
		self::$id = new IdField();
		self::$profile = new ForeignKeyField(array('model'=>'templates\auth\models\Profiles'));
		self::$right = new UintField();
		self::$allow = new EnumField(array('options'=>array('Allow','Deny')));
	}
}