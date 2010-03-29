<?php

class HTMLElementEditorField extends TextareaField
{
	protected $css;
	protected $controls = array (
		'insertOrderedList' 	=>	true,
		'insertUnorderedList'	=> 	true,
		'insertImage'			=>  true,
		'justifyLeft' 			=>	true,
		'justifyRight' 			=> 	true,
		'justifyCenter'			=> 	true,
		'justifyFull' 			=> 	true,
		'cut' 					=> 	false,
		'copy' 					=>	false,
		'paste' 				=> 	false,
		'increaseFontSize' 		=> 	true,
		'decreaseFontSize' 		=> 	true,
		'h1'					=>  true,
		'h2'					=>  true,
		'h3'					=>  true,
		'h4'					=>  true,
		'h5'					=>  true,
		'h6'					=>  true,
		'html' =>  true
		
		
	);

	function __construct($name, $title = null, $config = array(), $rows = 5, $cols = 55, $value = "", $form = null) {
		parent::__construct($name, $title, $rows, $cols, $value, $form);
		$this->extraClasses = array('hidden');
		if(!empty($config)) {
			foreach($config as $k => $v) {
				if($k == "css") $this->css = $v;
				else if(array_key_exists($k, $this->controls))
					$this->controls[$k] = $v;
			}
		}
	}
	
	private function getCss()
	{
		return $this->css ? "css : '{$this->css}'" : "css : ''";
	}
	
	private function getControls()
	{
		$controls = "controls : {\n";
		$first = true;
		foreach($this->controls as $var => $value) {
			$controls .= $first ? "" : ",\n";
			if((strlen($var) == 2 && $var[0] == "h") 
				&& stristr($_SERVER['HTTP_USER_AGENT'], "Mozilla")
				&& stristr($_SERVER['HTTP_USER_AGENT'], "Safari") === false
			) {
				$var.="mozilla";
			}
			$controls .= $var . " : ";
			$controls .= $value ? "{visible : true}" : "{visible : false}";
			$first = false;
		}
		$controls .= "},\n";
		return $controls;
	}
	
	private function getConfig() {
		return $this->getControls().$this->getCss();
	}
	
	public function FieldHolder() {
		
		Requirements::javascript(SSPE_DIR.'/javascript/jquery.1.3.js');
		Requirements::javascript(SSPE_DIR.'/javascript/jquery.wysiwyg.js');
		
		Requirements::css(SSPE_DIR.'/css/jquery.wysiwyg.css');
		Requirements::customScript("
			var j = jQuery.noConflict();
			console.log(j);
			//jQuery(function() {
				jQuery('#{$this->id()}').wysiwyg({
					{$this->getConfig()}
				}).parents('.htmlelementeditor').removeClass('hidden');
			//});
		");
		return parent::FieldHolder();		
	}	
	
}




