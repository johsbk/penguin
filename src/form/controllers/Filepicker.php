<?php

namespace penguin\form\controllers;

use penguin\common\Functions;
use penguin\mvc\Registry;
use penguin\mvc\BaseController;

class Filepicker extends BaseController
{
    public static $urls = array(
        'penguin.form.Filepicker',
        array('^$', 'index'),
        array('^(?<path>(.*))$', 'index'),
    );
    public function accessRules()
    {
        return array(
            array('allow', 'users' => '@'),
        );
    }
    public function index($args)
    {
        $t = Registry::getInstance()->twig->loadTemplate('form/filepicker.tpl');
        $c = array();
        $localpath = Functions::nz($args['path'], '');
        $mediapath = SITE_PATH.'/media/';
        $dir = new \DirectoryIterator($mediapath.$localpath);
        $picformats = array('png', 'jpg', 'gif');
        $files = array();
        foreach ($dir as $file) {
            if (!$dir->isDot()) {
                $path = URL_PATH.'/media/'.$localpath.$file;
                $f = new \stdClass();
                if (strlen($file) > 4 && in_array(substr($file, -3), $picformats)) {
                    $picture = $path;
                } elseif ($dir->isDir()) {
                    $picture = TEMPLATE_PATH.'form/pics/folder.png';
                } else {
                    $picture = TEMPLATE_PATH.'form/pics/file.png';
                }
                $f->path = $localpath;
                $f->picture = $picture;
                $f->name = (string) $file;
                $f->type = ($dir->isDir() ? 'dir' : 'file');
                $files[] = $f;
            }
        }
        $c['files'] = $files;
        $t->display($c);
    }
}
