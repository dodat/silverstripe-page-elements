<?php

class SlotManager extends ComplexTableField {
	
	public $template = "SlotManager";

	
	function __construct(GridPage $GridPage) {
		
		parent::__construct(
			$GridPage,
			"Slots",
			"Slot",
			array("Name" => "Name"),
			'getCMSFields_forPopup',
			"`GridPageID`='{$GridPage->ID}'"
		);
		
	}
	
	
	public function FieldHolder() {
		$ret = parent::FieldHolder();
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery/ui/ui.core.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery/ui/ui.draggable.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery/ui/ui.sortable.js");
		Requirements::javascript(SSPE_DIR."/javascript/SlotManager.js");
		Requirements::javascript(SSPE_DIR."/javascript/jquery.editable-1.3.3.js");
		Requirements::css(SSPE_DIR."/css/SlotManager.css");
		return $ret;
	}
	
	
	public function Slot($Name) {
		if($Slot = $this->Items()->find("Name",$Name)) {
		   return $Slot->forCMSTemplate();
		}
	}
	
	
	function Template() {
		if($Template = $this->controller->Template) {
			return $this->renderWith($Template);
		}
		return "Please choose a Template";
	}
	
}

class SlotManager_Controller extends Controller {
	
	function sort() {
		if(Permission::check("CMS_ACCESS_CMSMain")) {
			if(!empty($_POST) && is_array($_POST)) {
				foreach($_POST as $group => $map) { 
					foreach($map as $sort => $ID) {
						$Element = DataObject::get_by_id("Element", $ID);
						$Element->SortOrder = $sort;
						if($SlotID = (int)$this->urlParams['ID']) $Element->SlotID = $SlotID;
						$Element->write();
					}
				}
			}
		}
	}
	
	function editTitle() {
		if(Permission::check("CMS_ACCESS_CMSMain")) {
			$Element = DataObject::get_by_id("Element", (int)$_POST['ID']);
			$Element->Name = Convert::Raw2SQL($_POST['Name']);
			$Element->write();
		}
	}
	
	function previewVersion() {
		if(Permission::check("CMS_ACCESS_CMSMain")) {
			$Version = $this->urlParams['Name'];
			$ID = (int)$this->urlParams['ID'];
			if($Version == "Stage") {
				$Element = Versioned::get_one_by_stage("Element", "Stage", array("ID"=>$ID));
			} else {
				$Element = Versioned::get_version("Element", $ID, (int)$Version);
			}
			
			$Page = $Element->Slot()->GridPage();
			Page_Controller::init();
			
			return $this->customise(
					array("Element" => $Element->renderWith($Page->Template))
					)->renderWith("Element_preview");
			return $Element->forCMSTemplate();
		}
	}
	
	function revertElement() {
		if(Permission::check("CMS_ACCESS_CMSMain")) {
			$Element = DataObject::get_by_id("Element", (int)$this->urlParams['ID']);
			$Element->publish((int)$this->urlParams['Name'], "Stage", true);
		}
	}
	
	
}
