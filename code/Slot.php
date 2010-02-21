<?php
class Slot extends DataObject {
	
	
	static $db = array(
		"Name" => "Varchar"
	);
	
	static $has_many = array(
		"Elements" => "Element"
	);
	
	static $has_one = array(
		"GridPage" => "GridPage"
	);
	
	function forTemplate() {
		if(Director::get_site_mode() == "cms") {
			return $this->renderWith("Slot_cms");
		}
		
		$Content = "";
		if($this->Elements() && $Elements = $this->Elements()){
			foreach($Elements as $Element) {
				if($Element instanceof HiddenElement) {
					$Element->forTemplate();
					continue;
				}
				
				$ExtraAttrs = array();
				
				$ID = $Element->HTMLID();
				
				$Classes = array(
					$Element->parentClass(),
					$Element->ClassName
				 );
				
				(!empty($Element->ExtraClass)?$Classes[] = $Element->ExtraClass:"");
				
				if(!empty($Element->ExtraStyles) && $ExtraStyles = $Element->ExtraStyles) {
					$ExtraAttrs[] = "style=\"{$ExtraStyles}\"";
				}
				
				$ClassStr = implode(" ", $Classes);
				$ExtraStr = implode(" ", $ExtraAttrs);
				
				$Content .=<<<HTML
				
<div class="{$ClassStr}" {$ExtraStr} id="{$ID}">
	{$Element->Prefix}
	{$Element->forTemplate()}
	{$Element->Suffix}
</div>
		
HTML;
			}
		}
		return $Content;
	}
	
	
	function getCMSFields_forPopup() {
		$fs = new FieldSet(
			new ElementManager(
				$this,
				"Elements",
				"Element",
				array(
					'Name' => 'Name',
					'ClassNiceName' => 'Type',
					'Content' => 'Content'
				),
				"CMSFields",
				"SlotID='{$this->ID}'"
				)
			);
		return $fs;
	}
	
	
	function onBeforeDelete() {
		if($this->Elements() && $Elements = $this->Elements()) {
			foreach($Elements as $Element) {
				$Element->delete();
			}
		}
		parent::onBeforeDelete();
	}
	
	
	function AddIcon() {
		return SSPE_DIR . "/images/Element_add.png";
	}
	
	
	function AddLink() {
		return "admin/EditForm/field/Slots/item/{$this->ID}/DetailForm/field/Elements/add/";
	}
	
}
