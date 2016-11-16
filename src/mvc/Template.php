<?php

namespace penguin\mvc;

class Template
{
    /*
     * @Variables array
     * @access private
     */
    private $vars = array();

    private $path;
    public function __set($index, $value)
    {
        $this->vars[$index] = $value;
    }
    public function __get($index)
    {
        return $this->vars[$index];
    }
    public function __isset($index)
    {
        return isset($this->vars[$index]);
    }
    public function show($name)
    {
        $twig = Registry::getInstance()->twig;
        $this->router = Registry::getInstance()->router;
        $app = $this->router->app;
        if (substr($app, 0, 9) == 'penguin') {
            $mypath = IMPORT_PATH.substr($app, 10).'/views/'.$name.'.php';
        } else {
            $mypath = SITE_PATH.'/src/'.$app.'/views/'.$name.'.php';
        }

        if (!file_exists($mypath)) {
            throw new MVCException('Template not found in '.$mypath);
        }
        $t = $twig->loadTemplate('phptemplate.tpl');
        $this->path = $mypath;
        if (!isset($this->vars['context'])) {
            $this->vars['context'] = array();
        }
        $c = $this->vars;

        $c['path'] = $mypath;
        $t->display($c);
    }
}
