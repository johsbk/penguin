<?php
namespace penguin\mvc;
class Config {
	static function get($name) {
		$env = Registry::getInstance()->env;
		if ($env && file_exists($config = SITE_PATH.'/config/'.$env.'/'.$name.'.php')) {
            $settings = require $config;
        } else {
            $settings = require SITE_PATH.'/config/'.$name.'.php';
        }
        return $settings;
	}
}
