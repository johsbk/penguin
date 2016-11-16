<?php

namespace penguin\mvc;

class ClassLoader
{
    public static function register()
    {
        spl_autoload_register(array('penguin\mvc\ClassLoader', 'autoload'));
    }
    public static function autoload($class_name)
    {
        $filename = $class_name.'.php';
        $t = str_replace('\\', '/', SITE_PATH.'/src/'.$filename);
        if (file_exists($t)) {
            include $t;

            return true;
        }
    }
}
