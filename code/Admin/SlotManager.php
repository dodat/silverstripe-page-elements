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
		$this->setPopupSize(900, 500);
		
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
		$ret = "";
		if($this->Items()) {
			foreach($this->Items() as $Item) {
				if($Item->Name == $Name) {
					$AddIcon = SSPE_DIR . "/images/Element_add.png";
					$Slot = $this->controller->Slot($Name);
					$ret .=<<<HTML
	<table class="SlotHeader">
		<tr>
			<td>
				<h4>{$Name}</h4>
			</td>
			<td class="actions">
				<a href="{$Item->AddLink()}" title="Add an Element">
					<img src="/{$AddIcon}" alt="Add an Element" title="Add an Element"/>
				</a>
			</td>
		</tr>
	</table>
	<div class="Slot {$Name}" id="Slot-{$Slot->ID}">
		{$Slot->forCMSTemplate()}
	</div>
HTML;
				}
			}
		}
		return $ret;
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
						if($SlotID = $this->urlParams['ID']) $Element->SlotID = $SlotID;
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
	
}
