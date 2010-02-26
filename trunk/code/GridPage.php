<?php

class GridPage extends Page {
	
	static $db = array(
		"Template" => "Varchar"
	);
	
	static $has_many = array(
		"Slots" => "Slot"
	);
	
	
	public function getSelectableTemplates() {
		$temp = array("" => "None");
		$pre = "GridPage";
		
		if($TemplateFiles = glob(Director::getAbsFile($this->ThemeDir())."/templates/Layout/$pre*.ss")) {
			foreach($TemplateFiles as $TemplateFile) {
				$filename = basename($TemplateFile, ".ss");
				if($filename != $pre) {
					$filenicename = substr($filename, strpos($filename, "_")+1);
				} else {
					$filenicename = "Default";
				}
				$filenicename = str_replace("cols", " Columns", $filenicename);
				$temp[$filename] = ucwords($filenicename);
			}
		}
		return $temp; 
	}
	
	
	function isValidTemplate($Template) {
		return array_key_exists($Template, $this->getSelectableTemplates());
	}
	
	
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
			if(isset($this->changed['Template']) && $this->changed['Template']) {
				$this->ReadSlotsFromTemplate($this->Template);
			}
		}
		return parent::onBeforeWrite();
	}
	
	
	public function onAfterWrite(){
		parent::onAfterWrite();
		LeftAndMain::ForceReload();
	}
	
	
	public function onBeforeDelete() {
		if($this->Slots() && $Slots = $this->Slots()) {
			foreach($Slots as $Slot) {
				$Slot->delete();
			}
		}
		parent::onBeforeDelete();
	}
	
	
	protected function ReadSlotsFromTemplate($Template) {
		$file = SSViewer::getTemplateFileByType($Template, 'Layout');
		$cont = file_get_contents($file);
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
		return $Slot;
	}
	
	
	public function forTemplate() {
		if(SSViewer::getTemplateFileByType($this->Template, 'Layout')) {
			return $this->renderWith($this->Template);
		} else {
			SSViewer::flush_template_cache();
			if(SSViewer::getTemplateFileByType($this->Template, 'Layout')) {
				return $this->renderWith($this->Template);
			} else {
				$this->Template = "";
				$this->write();
			}
		}
	}
	
	
	function publish($fromStage, $toStage, $createNewVersion = false) {
		/** this causes weird things in object@l117 when not versioned **/
		foreach($this->Slots() as $Slot) {
			foreach($Slot->Elements()  as $Element) {
				if($Element->hasExtension("Versioned")) {
					$Element->publish($fromStage, $toStage, $createNewVersion);
				}
			}
		}
		
		$this->extension_instances['Versioned']->publish($fromStage, $toStage, $createNewVersion);
	}
	
}

class GridPage_Controller extends Page_Controller  {
	
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
