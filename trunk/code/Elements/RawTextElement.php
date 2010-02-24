<?php

class RawTextElement extends Element {
	
	static $NiceName = "Raw Text";
	
	static $db = array(
		"Content" => "Text"
	);
	
	
	function getCMSFields() {
		$field = new TextareaField("Content", "Content", 15, 30);
		$field->addExtraClass("elastic");
		return new FieldSet($field);
	}
	
	
	function forTemplate() {
		return $this->Content;
	}
		
}
