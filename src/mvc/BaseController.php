<?php

namespace penguin\mvc;

use mvc\FormBuilder;
use penguin\model\BaseModel;

abstract class BaseController
{
    /**
     * Enter description here ...
     *
     * @var Registry
     */
    public $registry;
    /**
     * Enter description here ...
     *
     * @var Twig_Environment
     */
    public $twig;

    private $base_url;
    private $base_twig_path;
    public function __construct()
    {
        $this->registry = Registry::getInstance();
        $this->twig = $this->registry->twig;
        $this->twig->addGlobal('controller', $this);
        $tmp = explode('\\', get_class($this));
        $pattern = '/[A-Z]/';
        $to = '_$0';
        $class = preg_replace($pattern, $to, $tmp[2]);
        $name = substr(strtolower($class), 1);
        $controllerend = '_controller';
        if (substr($name, -1 * strlen($controllerend)) == $controllerend) {
            $name = substr($name, 0, -1 * strlen($controllerend));
        }
        $this->base_twig_path = $tmp[0].'/'.$name.'/';
        $this->base_url = URL_PATH.'/'.$tmp[0].'/'.str_replace('_', '-', $name).'/';
    }
    abstract public function index($args);
    public function redirect($to)
    {
        $this->registry->router->redirect($to);
    }
    public function accessRules()
    {
        return array(
            array('deny'),
        );
    }
    public function getBaseUrl($args)
    {
        return $this->base_url;
    }
    public function getBaseTwigdir()
    {
        return $this->base_twig_path;
    }
    public function reverseLookup($view, $args = array())
    {
        if (strpos($view, '.') === false) {
            $tmp = explode('\\', get_class($this));
            $namespace = $tmp[0];
            $class = $tmp[2];
            $view = $namespace.'.'.$class.'.'.$view;
        }

        return $this->registry->router->reverseLookup($view, $args);
    }
    public function createFormBuilder(BaseModel $model)
    {
        return new FormBuilder($model);
    }
}
