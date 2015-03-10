<?php
namespace templates\auth\models;
use \templates\model\BaseModel,
	\templates\model\IdField,
	\templates\model\UintField,
	\templates\model\EnumField,
	\templates\model\ForeignKeyField;
class GroupRights extends BaseModel {
	static $id;
	static $group;
	static $right;
	static $allow;
	protected static function localinit() {
		self::$id = new IdField();
		self::$group = new ForeignKeyField(array('model'=>'Groups'));
		self::$right = new UintField();
		self::$allow = new EnumField(array('options'=>array('Allow','Deny')));
	}
}