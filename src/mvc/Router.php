<?php
namespace penguin\mvc;
use penguin\auth\Auth;
use penguin\common\Functions;
use \Exception;
class Router {
	private $path;
	public $file;
	public $controller;
	public $action;
	public $args=array();
	public $route;
	public $app;
	private $urlCache=array();
	 /**
	 *
	 * @set controller directory path
	 *
	 * @param string $path
	 *
	 * @return void
	 *
	 */
	function setPath($path) {
		
		/*** check if path is a directory ***/
		if (is_dir($path) == false) {
			throw new Exception ('Invalid controller path: `' . $path . '`');
		}
		/*** set the path ***/
		$this->path = $path;
	}

	 /**
	 *
	 * @load the controller
	 *
	 * @access public
	 *
	 * @return void
	 *
	 */
	 public function loader($route)
	 {
	 	
	 		
	 		$this->route = $route;
	 		
	        /*** check the route ***/
	        $this->getController($route);

	       
	        /*** a new controller class instance ***/
	        $class = $this->controller;
	        $controller = new $class();
		        /*** check if the action is callable ***/
		        if (!is_callable(array($controller, $this->action)))
		        {
		                $action = 'index';
		        }
		        else
		        {
		                $action = $this->action;
		        }
		        
		    if ($this->checkAccessRules($controller->accessRules(),$action)) {
		        /*** run the action ***/
		        $controller->$action($this->args);
	        } else {
	        	$sr = SessionRegistry::getInstance();
	        	$sr->route = $route;
	        	$this->redirect(URL_PATH.'/auth/');
	        }
	        
	 }
	 /**
	 	checks access rules
	 */
	 private function checkAccessRules($rules,$action) {
	 	foreach ($rules as $rule) {
	 		if ($rule[0]=='deny') {
	 			$result = false; 
	 		} elseif ($rule[0]=='allow') {
	 			$result = true;
	 		} else {
	 			throw new Exception("Unknown rule: ".$rule[0]);
	 		}
	 		if (!isset($rule['users']) || $rule['users']=='*' || ($rule['users']=='@' && Auth::isLoggedin())) {
	 			if (!isset($rule['actions']) || in_array($action, $rule['actions'])) return $result;
	 		}
	 	}
	 	return false;
	 }
	 private function solveRoute($route,$urls,$oldmatches=array()) {

	 	$prepadding = array_shift($urls);
	 	foreach ($urls as $url) {
        	list ($reg,$path) = $url;
        	preg_match('/'.str_replace("/","\/",$reg).'/',$route,$matches);
        	if (count($matches)>0) {
        			
        		if (is_array($path)) {
        			return $this->solveRoute(substr($route,strlen($matches[0])),$path,array_merge($oldmatches,$matches));
        		} else {
        			return array(($prepadding=='' ? '' : $prepadding.'.').$path,array_merge($oldmatches,$matches));
        		}
        	}
        }
        return false;
	 }
	 /**
	 *
	 * @get the controller
	 *
	 * @access private
	 *
	 * @return void
	 *
	 */

	private function getController($route) {
	        /*** get the route from the url ***/
	        $urls = Registry::getInstance()->urls;
	        if (strlen($route)>0 && $route[0]=='/') {
		 		$route = substr($route,1);
		 	}
	        $path = $this->solveRoute($route,$urls);       
	        if (!$path) {
	        	header('HTTP/1.0 404 Not Found');
	        	echo '404 Page not found';
	        	exit;
	        }
	        list($path,$matches) = $path;
			$this->args = array();
			$controller = substr($path,0,$ld=strrpos($path, '.'));
			$controller = substr($controller,0,$sld=strrpos($controller, '.')).".controllers".substr($controller,$sld);
			$i=0;
			$this->controller = str_replace(".","\\",$controller);
			
			$this->app = str_replace('.','/',substr($controller,0,$sld));
			$this->action = substr($path,$ld+1);
			if (count($matches)> 1) {
				$this->args = $matches;
			}
	
	}
	function getLink($controller='index',$action="index",$dict=array()) {
		$str = URL_PATH."/$controller/$action/";
		#if (count($dict)>0) $str.= "?";
		foreach ($dict as $k=>$v) $str .= "$v/";
		return $str;
	}
	function reverseLookup($view,$args = array()) {
		if (isset($this->urlCache[$view])) {
			$path = $this->urlCache[$view];	
		} else {
			$path = $this->_reverseLookup(Registry::getInstance()->urls,$view);
			$this->urlCache[$view] = $path;
		}
		if (!$path) return '';
		#print_r($path); 
		$url = URL_PATH."/";
		
		foreach ($path as $subpath) {
			if ($subpath{0}=="^") $subpath = substr($subpath, 1);
			if (substr($subpath,-1)=="$") $subpath = substr($subpath, 0,-1);
			$url .=$subpath;
		}
		if (count($args)>0) {
			foreach ($args as $k=>$v) { 
				$url = preg_replace("/\(\?\<(?<$k>\w+)\>[^\)]+\)/", $v, $url);
			}
		}
		return $url;
	}
	private function _reverseLookup($urls,$view,$pre=array()) {
		$padding = array_shift($urls);
		if ($padding != "") $padding.= ".";
		foreach ($urls as $url) {
			if (is_array($url[1])) {
				if ($path = $this->_reverseLookup($url[1], $view,array_merge($pre,array($url[0])))) {
					return $path;
				}
			} else {
				if ($view==$padding.$url[1]) {
					return array_merge($pre,array($url[0]));
				}
			}
		}
		return false;
		#print_r($urls);
	}
	function redirect($to="") {
		if ($to=="") {
			$to = Functions::getArgs('');
		} 		
		header('Location: '.$to);
		exit(0);
	}
}