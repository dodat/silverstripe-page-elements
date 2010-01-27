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
				15,
				90
			)
		);
	}
	
	
	function forTemplate() {
		return $this->Content;
	}
	
}
