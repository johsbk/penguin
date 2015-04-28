<?php
namespace penguin\mvc;
use penguin\db\DB;

use penguin\mvc\Registry;

use penguin\auth\Auth;
use penguin\Mail\MailService;
class Project {
	private $env = null;
	private $preparetwig = null;
	function __construct() {

	}
	function detectEnvironment($dict) {
		foreach ($dict as $env=>$hosts) if (in_array($_SERVER['SERVER_NAME'], $hosts) $this->env = $env;
	}
	var $defaultLocale = 'auto';
	private function initDatabase() {
		if ($this->env && file_exists($config = SITE_PATH.'/config/'.$this->env.'/database.php'))
			$settings = require($config);
		else
			$settings = require SITE_PATH.'/config/database.php';
		\penguin\db\DB::login($settings['user'],$settings['pass'],$settings['host'],$settings['db']);	
	}
	private function initRoutes() {
		if ($this->env && file_exists($config = SITE_PATH.'/config/'.$this->env.'/routes.php'))
			$routes = require($config);
		else
			$routes = require SITE_PATH.'/config/routes.php';
		Registry::getInstance()->urls = $routes;
	}
	function run() {
		$this->initDatabase();
		$this->initRoutes();
		$loader = new \Twig_Loader_Filesystem(array(SITE_PATH.'/src/twigs/',SITE_PATH.'/vendor/johsbk/penguin/src/twigs/'));
		$twig = new \Twig_Environment($loader,array('cache'=>SITE_PATH.'/cache/','debug'=>true));
		Registry::getInstance()->twig = $twig;
		try {
			$route = (isset($_GET['rt']) ? $_GET['rt'] : '');
			unset($_GET['rt']);
			session_start();
			
			$twig->addGlobal('URL_PATH', URL_PATH);
			$twig->addGlobal('MEDIA_PATH', MEDIA_PATH);
			$twig->addGlobal('request',$_REQUEST);
			$twig->addGlobal('loggedin', Auth::isLoggedin());
			$twig->addGlobal('current_user', Auth::$profile);
			if ($this->preparetwig) $this->preparetwig($twig);			
			$reg = \penguin\mvc\Registry::getInstance();
			$reg->project = $this;
			$reg->template = new \penguin\mvc\Template();
			$reg->router = new \penguin\mvc\Router();
			
			$reg->starttime = $reg->endtime = microtime(true);
			ob_start();

			if (!isset(\penguin\mvc\SessionRegistry::getInstance()->locale)) {
				$this->loadLocale();
			}
			$twig->addGlobal('locale',\penguin\mvc\SessionRegistry::getInstance()->locale);
			$reg->router->loader($route);
			ob_end_flush();
		} catch (\Exception $e) {

			$this->handleException($route,$e);
		}
		\penguin\db\DB::logout();
	}
	public function loadLocale($locale=null) {
		$reg = \penguin\mvc\SessionRegistry::getInstance();
		$reg->locale = 'en-US';
		if (!$locale && $this->defaultLocale=='auto' && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$languages = explode(';',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
			foreach ($languages as $lang) {
				list($locale,) = explode(',',$lang);
				if (file_exists('i18n/'.$locale.'.ini')) {
					$reg->locale = $locale;
					break;
				}
			}
		} else {
			if (file_exists('i18n/'.$locale.'.ini')) {
				$reg->locale = $locale;
			}
		}
		if ($reg->locale != 'en-US') {
			$reg->translation = parse_ini_file('i18n/'.$locale.'.ini');
		} else {
			$reg->translation = array();
		}
		
	}
	function inittwig($fn) {
		$this->preparetwig = $fn;
	}
	
	function handleException($route,\Exception $e) {
		$reg = Registry::getInstance();

		$t = $reg->twig->loadTemplate('error.tpl');
		$c = array();
		$c['route'] = $route;
		$c['type'] = get_class($e);
		$c['msg'] = $e->getMessage();
		$trace = $e->getTrace();
		foreach ($trace as $key=> $entry) { 
			$file = file_get_contents($entry['file']);
			$lines = explode("\n",$file);
			$start = $entry['line']-5;
			$end = $entry['line']+5;
			$trace[$key]['lines'] = array();
			if ($start <0) $start =0;
			if ($end > count($lines)) $end = count($lines);
			for ($i=$start;$i<$end;$i++) {
				$trace[$key]['lines'][$i] =  str_replace(array("\t"," "), array("&nbsp;&nbsp;&nbsp;","&nbsp;"), $lines[$i]);
			}
		}
		$c['trace'] = $trace;
		$out = $t->render($c);
		if (Auth::isLoggedin())
			$userid= Auth::$profile->id;
		else
			$userid = 0;
		if (!DEBUG) {
			DB::query("INSERT into errorlog(time,message,user_id) values(now(),'".DB::escape($out)."',$userid)");
			if (isset($reg->admin_email) ) {
				
				$ms = new MailService('smtp.gmail.com',465,'soyouz@gmail.com','edderkop');
				$ms->send_mail($reg->admin_email,'soyouz@gmail.com','Error occured','',$out);
			}
		}
		echo $out;
		
	}
}