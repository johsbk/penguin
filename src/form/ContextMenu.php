<?php
namespace penguin\form;
class ContextMenu {
	private static $headdone = false;
	static function head() {
		if (!self::$headdone) {
			self::$headdone = true;
			return "<script src=\"".TEMPLATE_MEDIA_PATH."form/js/jquery.contextMenu.js\" type=\"text/javascript\"></script>\n<link rel=\"stylesheet\" type=\"text/css\" href=\"".TEMPLATE_PATH."form/styles/jquery.contextMenu.css\" />";
		}
	}
}