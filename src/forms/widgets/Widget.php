<?php

namespace penguin\forms\widgets;

abstract class Widget
{
    public $is_hidden = false;
    public $needs_multipart_form = false;
    public $attrs = array();
}
