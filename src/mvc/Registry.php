<?php
namespace templates\mvc;
class Registry {
	private $vars = array();
	/**
	 * 
	 * Enter description here ...
	 * @var Template
	 */
	public $template;
	/**
	 * 
	 * Enter description here ...
	 * @var Router
	 */
	public $router;
	public $installed_apps = array();
	public $urls;
	/**
	 * 
	 * Enter description here ...
	 * @var Twig_Environment
	 */
	public $twig;
	private static $instance;
	public function __get($index) {
		if (!isset($this->$index)) throw new \Exception ('Not set: '.$index); 
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
}