<?php

class SlotManager extends ComplexTableField {
	
	public $template = "SlotManager";
	
	
	function __construct(GridPage $GridPage) {
		//Prevent pagination from restricting the number of Slots to 10
		$this->setShowPagination(false);
		
		parent::__construct(
			$GridPage,
			"Slots",
			"Slot",
			array("Name" => "Name"),
			'getCMSFields_forPopup',
			"\"GridPageID\"='{$GridPage->ID}'"
		);
		
	}
	
	
	public function FieldHolder() {
		$ret = parent::FieldHolder();
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery-livequery/jquery.livequery.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery-ui/jquery.ui.core.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery-ui/jquery.ui.widget.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery-ui/jquery.ui.mouse.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery-ui/jquery.ui.draggable.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery-ui/jquery.ui.sortable.js");
		Requirements::javascript(SSPE_DIR."/javascript/SlotManager.js");
		Requirements::javascript(SSPE_DIR."/javascript/jquery.editable-1.3.3.js");
		Requirements::css(SSPE_DIR."/css/SlotManager.css");
		return $ret;
	}
	
	
	public function Slot($Name) {
		if($this->Items()) {
			if($Slot = $this->Items()->find("Name",$Name)) {
				return $Slot->forCMSTemplate();
			}
		}
	}
	
	
	function Template() {
		if($TemplateFile = $this->controller->TemplateAbsFile()) {
			$contents = file_get_contents($TemplateFile);
			$Template = new SSViewer_FromString($contents);
			return $this->renderWith($Template);
		}
		return "Please choose a Template";
	}
	
	// needed for $ThemeDir to work in templates
	function ThemeDir() {
		return GridPage::ThemeDir();
	}
	
}

class SlotManager_Controller extends Controller {
	
	function sort() {
		if(Permission::check("CMS_ACCESS_CMSMain")) {
			if(!empty($_POST) && is_array($_POST)) {
				foreach($_POST as $group => $map) { 
					foreach($map as $sort => $ID) {
						$Element = DataObject::get_by_id("Element", $ID);
						if($Element->canEdit()) {
							$Element->SortOrder = $sort;
							if($SlotID = (int)$this->urlParams['ID']) $Element->SlotID = $SlotID;
							$Element->write();
						}
					}
				}
			}
		}
	}
	
	function editTitle() {
		$Element = DataObject::get_by_id("Element", (int)$_POST['ID']);
		
		if($Element->canEdit()) {
			$Element->Name = Convert::Raw2SQL($_POST['Name']);
			$Element->write();
		}
	}
	
	function previewVersion() {
		if(Permission::check("CMS_ACCESS_CMSMain")) {
			$Version = (int)$this->urlParams['Name'];
			$ID = (int)$this->urlParams['ID'];
			if($Version == "Stage") {
				$Element = DataObject::get_by_id("Element", $ID);
			} else {
				$Element = Versioned::get_version("Element", $ID, $Version);
			}
			
			if($Element->canEdit()) {
				$Page = $Element->Slot()->GridPage();
				Page_Controller::init();
				
				$PageTemplate = file_get_contents($Page->TemplateAbsFile());
				$PageTemplate = str_replace('$Slot', '$previewSlot', $PageTemplate);
				$PageSSViewer = new SSViewer_FromString($PageTemplate);
				
				return $this->customise(
					array("Element" =>
						$Element->renderWith($PageSSViewer)
					)
				)->renderWith("Element_preview");
			}
		}
	}
	
	function revertElement() {
		$Element = DataObject::get_by_id("Element", (int)$this->urlParams['ID']);
		
		if($Element->canEdit()) {
			$Element->publish((int)$this->urlParams['Name'], "Stage", true);
		}
	}
	
	
}
