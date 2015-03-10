<?php
namespace penguin\mvc;
class SessionRegistry {
	private $vars = array();
	private static $instance;
	const SESSION_REGISTRY = "SESSION_REGISTRY";
	public function __construct() {
		if (isset($_SESSION[self::SESSION_REGISTRY])) {
			$this->vars = $_SESSION[self::SESSION_REGISTRY];
		}
	}
	public function __get($index) {
		return $this->vars[$index];
	}
	public function __set($index,$value) {
		$this->vars[$index] = $value;
	}
	public function __unset($index) {
		unset($this->vars[$index]);
	}
	public function __isset($index) {
		return isset($this->vars[$index]);
	}
	public static function getInstance() {
		if (self::$instance==null) self::$instance = new self;
		return self::$instance;
	}
	public function save() {		
		$_SESSION[self::SESSION_REGISTRY] = $this->vars;
	}
	public function __destruct() {
		$this->save();
	}
}
?>