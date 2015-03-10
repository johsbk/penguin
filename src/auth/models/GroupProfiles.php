<?php
namespace templates\auth\models;
use templates\model\ForeignKeyField;

use templates\model\IdField;

use \templates\model\BaseModel;
class GroupProfiles extends BaseModel {
	static $id;
	static $group;
	static $profile;
	protected static function localinit() {
		self::$id = new IdField();
		self::$group = new ForeignKeyField(array('model'=>'templates\auth\models\Groups'));
		self::$profile = new ForeignKeyField(array('model'=>'templates\auth\models\Profiles'));
	}
}