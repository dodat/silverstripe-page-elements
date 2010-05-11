<?php


class ElementExtension extends Extension {
	
	static $theme;
	
	function init() {
		self::$theme = SSViewer::current_theme();
	}
	
}
