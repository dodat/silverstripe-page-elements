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
	
	
	function forCMSTemplate() {
		return $this->renderWith("Slot_cms");
	}
	
	function forTemplate() {
		return $this->renderWith("Slot");
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
	
	
	//TODO: needs review
	function AddIcon() {
		//return SSPE_DIR . "/images/Element_add.png";
		return "http://www.page-elements.com/page-elements/images/Element_add.png";
	}
	
	function AddLink() {
		return "admin/EditForm/field/Slots/item/{$this->ID}/DetailForm/field/Elements/add/";
	}
	
	/** temporary fix for non superadmin users to edit elements **/
	function canEdit() {
		return Permission::check("CMS_ACCESS_CMSMain");
	}
	
}
