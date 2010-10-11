<?php

class HTMLElement extends Element {
	
	static $NiceName = "Wysiwyg";
	
	static $db = array(
		"Content" => "HTMLText"
	);
	
	
	function getCMSFields() {
		HtmlEditorConfig::set_active("elements");
		return new FieldSet(
			new HtmlEditorField("Content")
		);
	}
	
	
	function getRequirementsForPopup() {
		parent::getRequirementsForPopup();
		HtmlEditorField::include_js();
	}
	
	
	function forTemplate() {
		return $this->Content;
	}
	
	
	
}
