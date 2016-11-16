<?php

namespace penguin\form;

class FancyBox
{
    private static $headdone = false;
    public static function head()
    {
        if (!self::$headdone) {
            self::$headdone = true;

            return '<script src="'.TEMPLATE_MEDIA_PATH."fancybox/jquery.fancybox-1.3.4.pack.js\" type=\"text/javascript\"></script>\n<link rel=\"stylesheet\" type=\"text/css\" href=\"".TEMPLATE_MEDIA_PATH.'fancybox/jquery.fancybox-1.3.4.css" />';
        }
    }
}
