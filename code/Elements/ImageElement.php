<?php

class ImageElement extends Element {

    static $has_one = array(
	"Image" => "Image"
    );
    
    function getCMSFields() {
	$fs = parent::getCMSFields();
	$fs->push(
	    new FileIFrameField("Image")
	);
	return $fs;
    }
    
    function forTemplate() {
	return $this->Image()->forTemplate();
    }

}