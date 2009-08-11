<?php

class SlotManager extends ComplexTableField {
	
	public $template = "SlotManager";

	
	function __construct(GridPage $controller) {
		
		parent::__construct(
			$controller,
			"Slots",
			"Slot",
			array("Name" => "Name"),
			'getCMSFields_forPopup',
			"`GridPageID`='{$controller->ID}'"
		);
		$this->setPopupSize(900, 500);
		
	}
	
	
	public function FieldHolder() {
		$ret = parent::FieldHolder();
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery/ui/ui.core.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery/ui/ui.core.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery/ui/ui.draggable.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery/ui/ui.sortable.js");
		Requirements::javascript(KELP_DIR."/javascript/SlotManager.js");
		Requirements::css(KELP_DIR."/css/SlotManager.css");
		return $ret;
	}
	
	
	
	public function Slot($Name) {
		
		$ret = "";
		if($this->Items()) {
			foreach($this->Items() as $Item) {
				
				if($Item->Name == $Name) {
					$AddIcon = KELP_DIR . "/images/Element_add.png";
					$Slot = $this->controller->Slot($Name);
					$ret .=<<<HTML
	<table class="data">
		<tr>
			<td>
				<h4>{$Name}</h4>
			</td>
			<td class="actions">
				<a href="{$Item->AddLink()}" title="Add a Slotitem">
					<img src="/{$AddIcon}" alt="Add a Slotitem" title="Add a Slotitem"/>
				</a>
			</td>
		</tr>
	</table>
HTML;
					$ret .= "<div class=\"Slot {$Name}\" id=\"Slot-{$Slot->ID}\">";
					$ret .= $Slot->forCMSTemplate();
					$ret .= "</div>";
					
				}
			}
		}
		return $ret;
	}
	
	function Template() {
		if($Template = $this->controller->Template) {
			if(SSViewer::getTemplateFileByType($Template, 'Layout')) {
				return $this->renderWith($Template);
			} else { 
				SSViewer::flush_template_cache();
			}
		} else {
			SSViewer::flush_template_cache();
		}
		
		return "Please choose a Template first!";
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
	
}
