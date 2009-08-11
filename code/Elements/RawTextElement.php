<?php

class RawTextElement extends Element {
	
	static $NiceName = "Raw Text";
	
	static $db = array(
		"Content" => "Text"
	);
	
	
	function getCMSFields() {
		return new FieldSet(new TextareaField("Content"));
	}
	
	
	function forTemplate() {
		return $this->Content;
	}
	
}
