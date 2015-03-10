<?php
namespace templates\auth;
use \templates\auth\models\Profiles;
class Auth {
	/**
	 * Enter description here...
	 *
	 * @var Profiles
	 */
	static $profile;
	private static $loggedin = false;
	private static $initialized = false;
	static function init() {
		if (!self::$initialized) {
			if (isset($_SESSION['AUTH'])) {
				list(self::$profile,self::$loggedin) = $_SESSION['AUTH'];
			}
			self::$initialized =true;
		}
	}
	static function isLoggedin() {
		self::init();
		return self::$loggedin;
	}
	static function end() {
		$_SESSION['AUTH'] = array(self::$profile,self::$loggedin);
	}
	static function login($username,$password) {
		$p = Profiles::first(array("name"=>$username));
		if ($p) {
			if (self::checkHashedPassword($password,$p->password,$p->salt)) {
				self::$profile = $p;
				self::$loggedin = true;
				self::end();
				return true;
			}
		}
		return false;
	}
	
	static function getId() {
		return self::$profile->id;
	}
	static function logout() {
		self::$profile = null;
		self::$loggedin = false;
		self::end();
	}
	static function checkRight($right) {
		self::init();
		if (!self::$loggedin) return false;
		return self::$profile->checkRight($right);
	}
	static function addUser($username,$password,$groupIds=array()) {
		$p = Profiles::first(array("name"=>$username));
		if ($p) throw new Exception("User already exists");
		$salt = rand(1000,99999);
		$pass = sha1($password.$salt.self::globalPattern());
		$p = new Profiles();
		$p->name= $username;
		$p->password = $pass;
		$p->salt = $salt;
		$p->save();
		echo $username;
		
		$id = $p->id;
		foreach ($groupIds as $gid) {
			$gp = new GroupProfiles();
			$gp->group = $gid;
			$gp->profile = $id;
			$gp->save(); 
		}
		return $p;
	}
	private static function globalPattern() {
		return "a!@#*fadfh";
	}
	static function checkHashedPassword($password,$hash="",$salt="") {
		if ($salt=="") $salt = self::$profile->salt;
		if ($hash=="") $hash = self::$profile->password;
		return sha1($password.$salt.self::globalPattern()) == $hash;
	}
	static function changePassword($password) {
		$salt = rand(1000,99999);
		$pass = sha1($password.$salt.self::globalPattern());
		$p = self::$profile;
		$p->password = $pass;
		$p->salt = $salt;
		$p->save();
	}
}
?>