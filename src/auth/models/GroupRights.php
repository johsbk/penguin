<?php
namespace penguin\auth\models;
use \penguin\model\BaseModel,
	\penguin\model\IdField,
	\penguin\model\UintField,
	\penguin\model\EnumField,
	\penguin\model\ForeignKeyField;
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