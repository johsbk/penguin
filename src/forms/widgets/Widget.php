<?php
namespace penguin\forms\widgets;
abstract class Widget {
	var $is_hidden=false;
	var $needs_multipart_form=false;
	var $attrs = array();
}