<?php

class HTMLElement extends Element {
	
	static $NiceName = "Html Text";
	
	static $db = array(
		"Content" => "HTMLText"
	);
	
	
	function getCMSFields() {
		return new FieldSet(
			new HTMLElementEditorField(
				"Content",
				"Content",
				array(
					"css" => "/themes/".SSViewer::current_theme()."/css/typography.css"
				),
				10,
				70
			)
		);
	}
	
	
	function forTemplate() {
		return $this->Content;
	}
	
}
