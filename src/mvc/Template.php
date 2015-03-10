<?php
namespace templates\mvc;
use \Exception;
Class Template {

	/*
	 * @Variables array
	 * @access private
	 */
	private $vars = array();
		
	
	
	private $path;
	public function __set($index, $value) {
		$this->vars[$index] = $value;
	}
	public function __get($index) {
		return $this->vars[$index];
	}
	public function __isset($index) {
		return isset($this->vars[$index]);
	}
	function show($name,$includebase=true) {
		$twig = Registry::getInstance()->twig;
        $this->router = Registry::getInstance()->router;
		$app = $this->router->app;
		if (substr($app,0,9)=='templates') {
			$path = IMPORT_PATH .substr($app,10). '/views/'. $name . '.php';
		} else {
			$path = SITE_PATH .'/'.$app. '/views/'. $name . '.php';
		}

		if (file_exists($path) == false) {
			throw new Exception('Template not found in '. $path);
			return false;
		}
		$t= $twig->loadTemplate('phptemplate.tpl');
		$this->path = $path;
		if (!isset($this->vars['context'])) $this->vars['context'] = array();
		$c = $this->vars;
	
		$c['path']=$path;
		$t->display($c);
	}
}

?>