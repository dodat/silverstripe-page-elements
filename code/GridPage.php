<?php

class GridPage extends Page {
	
	static $db = array(
		"Template" => "Varchar"
	);
	
	static $has_many = array(
		"Slots" => "Slot"
	);
	
	
	public function getCMSFields(){
		$fs = parent::getCMSFields();
		
		$TemplateField = new DropdownField(
			"Template",
			"Template",
			$this->getSelectableTemplates(),
			$this->Template
		);
		
		$fs->insertAfter($TemplateField, "ClassName");
		
		
		if($this->Template && $this->isValidTemplate($this->Template)) {
			$field = new SlotManager($this);
		} else {
			$field = new HeaderField("Please choose a Template.");
		}
		
		$fs->removeFieldFromTab("Root.Content.Main", "Content");
		
		$fs->addFieldToTab("Root.Content.Main", $field);
		
		Requirements::customScript(<<<JS

Behaviour.register({
	'select#Form_EditForm_Template' : {
		onchange: function() {
			alert('The template will be updated after the page is saved');
		}
	}
});

JS
);
		return $fs;
	}
	
	
	public function Slot($Name = null) {
		if($this->Slots()->Count() > 0) {
			return $this->Slots()->find("Name", $Name); 
		}
	}
	
	
	public function onBeforeWrite() {
		if($this->ID && $this->Template) {
		
			$changed = $this->getChangedFields();
			if(isset($changed['Template']) && $changed['Template']) {
				$this->ReadSlotsFromTemplate($this->Template);
			}
			//saving the output in $Content to be found in search etc
			$SSV = new SSViewer($this->TemplateAbsFile());
			$this->Content = $SSV->process($this);
			Requirements::clear();
		}
		return parent::onBeforeWrite();
	}
	
	
	public function onAfterWrite(){
		parent::onAfterWrite();
		LeftAndMain::ForceReload();
	}
	
	/**
	 * this gets executed when the page is unpublished
	 * need further investigation how to determine whether the 'actual' record is being deleted
	 */ 
	public function onAfterDelete() {
		if($this->Slots() && $Slots = $this->Slots()) {
			foreach($Slots as $Slot) {
				//$Slot->delete();
			}
		}
		parent::onAfterDelete();
	}
	
	
	
	public function getSelectableTemplates() {
		$temp = array("" => "None");
		$pre = "GridPage";
		
		if($TemplateFiles = glob(Director::getAbsFile($this->TemplateDir()).$pre."*.ss")) {
			foreach($TemplateFiles as $TemplateFile) {
				$filename = basename($TemplateFile, ".ss");
				if($filename != $pre) {
					$filenicename = substr($filename, strpos($filename, "_")+1);
				} else {
					$filenicename = "Default";
				}
				$filenicename = str_replace("col", " Column", $filenicename);
				$temp[$filename] = ucwords($filenicename);
			}
		}
		return $temp; 
	}
	
	/** Template File functions required since 2.4 resets the theme in cms mode */
	
	function TemplateFile() {
		return $this->TemplateDir().$this->Template.".ss";
	}
	
	function TemplateAbsFile() {
		return Director::getAbsFile($this->TemplateFile());
	}
	
	function TemplateDir() {
		return $this->ThemeDir()."/templates/Layout/";
	}
	
	function ThemeDir() {
		if($theme = SSViewer::current_theme()) {
			return THEMES_DIR . "/$theme";
		} elseif($theme = ElementExtension::$theme) {
			return THEMES_DIR . "/$theme";
		} elseif($theme = SiteConfig::current_site_config()->Theme ) {
			return THEMES_DIR . "/$theme";
		} else {
			throw new Exception("cannot detect theme");
		}
	}
	
	function isValidTemplate($Template) {
		return array_key_exists($Template, $this->getSelectableTemplates());
	}
	
	
	protected function ReadSlotsFromTemplate($Template) {
		$cont = file_get_contents($this->TemplateAbsFile());
		$cont = str_replace('$Slot', '$createSlot', $cont);
		$ssv = new SSViewer_FromString($cont);
		
		if($ssv->process($this)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function createSlot($Name) {
		if(!$Slot = $this->Slots()->find("Name", $Name)) {
			$Slot = new Slot;
			$Slot->Name = $Name;
			$Slot->GridPageID = $this->ID;
			$Slot->write();
		}
		return true;
	}
	
	function publish($fromStage, $toStage, $createNewVersion = false) {
		if(Element::is_versioned()) {
			foreach($this->Slots() as $Slot) {
				foreach($Slot->Elements() as $Element) {
					if($Element->hasExtension("Versioned")) {
						$elEx = $Element->getExtensionInstance('Versioned');
						$elEx->setOwner($Element);
						$elEx->publish($fromStage, $toStage, $createNewVersion);
					}
				}
			}
		}
		
		$ext = $this->getExtensionInstance('Versioned');
		$ext->setOwner($this);
		return $ext->publish($fromStage, $toStage, $createNewVersion);
	}
	
	
}

class GridPage_Controller extends Page_Controller  {
	
	function init() {
		parent::init();
		Requirements::css(SSPE_DIR."/css/960gs/960.css");
	}
	
	function index() {
		return $this->renderWith(
			array(
				$this->data()->Template,
				$this->data()->ClassName,
				"Page"
			),
			"Layout");
	}
	
	
	function Slot($Name) {
		if($Slot = $this->data()->Slot($Name)) {
			return $Slot->forTemplate();
		}
	}
}
