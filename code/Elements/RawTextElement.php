<?php

class RawTextElement extends Element {
	
	static $NiceName = "Raw Text";
	
	static $db = array(
		"Content" => "Text"
	);
	
	
	function getCMSFields() {
		return new FieldSet(new TextareaField("Content", "Content", 15, 30));
	}
	
	
	function forTemplate() {
		return $this->Content;
	}
	
}
