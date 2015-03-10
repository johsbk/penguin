<?php
namespace penguin\auth\models;
use \penguin\model\BaseModel;
use \penguin\model\IdField;
use \penguin\model\UintField;
use \penguin\model\EnumField;
use \penguin\model\ForeignKeyField;
class ProfileRights extends BaseModel {
	static $id;
	static $profile;
	static $right;
	static $allow;
	protected static function localinit() {
		self::$id = new IdField();
		self::$profile = new ForeignKeyField(array('model'=>'penguin\auth\models\Profiles'));
		self::$right = new UintField();
		self::$allow = new EnumField(array('options'=>array('Allow','Deny')));
	}
}