<?php

class Element extends DataObject {
	
	static $db = array(
		"Name" => "Varchar",
		"SortOrder" => "Int",
		"ExtraClass" => "Varchar",
		"ExtraStyles" => "Text",
		"Prefix" => "Text",
		"Suffix" => "Text"
	);
	
	static $has_one = array(
		"Slot" => "Slot"
	);
	
	static $defaults = array(
		"Name" => "New Element"
	);
	
	static $default_sort = "SortOrder";
	
	/**
	 * needs param $SlotID to set the parent manually caused by weird things on ComplextTableField @ l550
	 * and Element in order to get the custom fields
	 */
	function CMSFields(Element $Item, $SlotID) {
		
		if($Item->ID==0) {
			$ClassField = new DropdownField("ClassName", "Type", $this->getClassDropdown(), $this->ClassName);
		} else {
			$ClassField = new ReadonlyField("Type", "Type", $this->getClassNiceName($this->ClassName));
		}
		
		$fs = new FieldSet(
			new TextField('Name', "Name", $this->Name),
			$ClassField,
			new HiddenField("SlotID", "", $SlotID),
			$this->getExtraCMSFields()
		);
		
		$fs->merge($this->getCMSFields());
		return $fs;
	}
	
	
	public function getCMSFields() {
		$fs = new FieldSet();
		return $fs;
	}
	
	public function getExtraCMSFields() {
		
		
		$fg = new FieldGroup(
			new FieldSet(
				new TextField("ExtraClass"),
				new TextareaField("ExtraStyles"),
				new TextareaField("Prefix"),
				new TextareaField("Suffix")
			),
			new FieldSet(
				
			)
		);
		
		$fg->setID("ExtraFields");
		$fg->addExtraClass("ExtraFields");
		$fg->Title = "Advanced";
		
		return $fg;
	}
	
	public function ExtraCMSFields() {
		return new FieldSet(
			new TextField("ExtraClass"),
			new TextareaField("ExtraStyles")
		);
		
	}
	
	/** to avoid confusion in TableListField **/
	function getContent() {
		return $this->getField('Content');
	}
	
	function HTMLID() {
		return "{$this->ClassName}-{$this->ID}";
	}
	
	public function forTemplate() {
		return $this->renderWith($this->RecordClassName);
	}
	
	
	public function getCMSFields_forPopup() {
		return $this->CMSFields();
	}
	
	public function getRequirementsForPopup() {
		Requirements::javascript(SSPE_DIR.'/javascript/jquery.1.3.js');
		//Requirements::javascript(SSPE_DIR."/javascript/jquery/jquery-1.3.2.min.js");
		//Requirements::javascript(SSPE_DIR."/javascript/jquery/jquery-ui-1.7.2.custom.min.js");
		Requirements::javascript(SSPE_DIR."/javascript/ElementManager.js");
		Requirements::css(SSPE_DIR."/css/ElementManager.css");
	}
	
	
	public function forCMSTemplate() {
		return $this->forTemplate();
	}
	
	public function getClassDropdown() {
		$parent = __CLASS__;
		$classes = ClassInfo::subclassesFor($parent);
		$result = array();
		
		foreach($classes as $class) {
			if($class == $parent) continue;
			$instance = singleton($class);
			if((($instance instanceof HiddenClass) ||
				!$instance->canCreate())){ continue; }
			$result[$class] = self::getClassNiceName($class);
		}
		
		asort($result);
		
		return $result;
		
	}
	
	public function getClassNiceName($class = null) {
		if(!$class) {
			$instance = $this;
		} else {
			$instance = singleton($class);
		}
		
		if(!$NiceName = $instance->stat("NiceName")) {
			$NiceName = $instance->i18n_singular_name();
		}
		return $NiceName;
	}
	
	/**
	 * This could be nicer!
	*/
	
	function Link() {
		return Director::absoluteBaseURL()."admin/EditForm/field/Slots/item/".$this->SlotID."/DetailForm/field/Elements/";
	}
	
	function EditLink() {
		return $this->Link()."item/".$this->ID."/edit/";
	}
	
	function DeleteLink() {
		return $this->Link()."item/".$this->ID."/delete/";
	}
	
}


