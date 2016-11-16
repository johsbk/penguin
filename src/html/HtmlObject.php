<?php

namespace penguin\html;

class HtmlObject
{
    public $tagname = '';
    public $attrs = array();
    public $content;
    public function __construct($tagname, $content, $attrs = array())
    {
        $this->tagname = $tagname;
        $this->content = $content;
        $this->attrs = $attrs;
    }
    public function __toString()
    {
        $args = array_map(function ($value, $key) {
            return "$value=\"$key\"";
        }, array_keys($this->attrs), $this->attrs);

        return '<'.$this->tagname.' '.implode(' ', $args).">$this->content</".$this->tagname.'>';
    }
    public function setAttr($name, $attr)
    {
        $this->attrs[$name] = $attr;
    }
}
