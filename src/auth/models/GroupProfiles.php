<?php
namespace penguin\auth\models;
use penguin\model\ForeignKeyField;

use penguin\model\IdField;

use \penguin\model\BaseModel;
class GroupProfiles extends BaseModel {
	static $id;
	static $group;
	static $profile;
	protected static function localinit() {
		self::$id = new IdField();
		self::$group = new ForeignKeyField(array('model'=>'penguin\auth\models\Groups'));
		self::$profile = new ForeignKeyField(array('model'=>'penguin\auth\models\Profiles'));
	}
}