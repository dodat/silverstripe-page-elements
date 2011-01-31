<?php

class Element extends DataObject {
	
	static $db = array(
		"Name" => "Varchar",
		"SortOrder" => "Int",
		"ExtraClass" => "Varchar",
		"ExtraStyles" => "Text",
		"Prefix" => "Text",
		"Suffix" => "Text",
		"CanViewType" => "Enum('Anyone, LoggedInUsers, NotLoggedInUsers', 'Anyone')"
	);
	
	
	static $has_one = array(
		"Slot" => "Slot"
	);
	
	
	static $defaults = array(
		"Name" => ""
	);
	
	
	static $default_sort = "SortOrder";
	
	
	static $versioning = array(
		"Stage",  "Live"
	);	
	
	
	static function set_versioning($ElementClasses) {
		foreach((array)$ElementClasses as $ElementClass) {
			if(class_exists($ElementClass)) {
				Object::add_extension($ElementClass, "Versioned('Stage', 'Live')");
			} else {
				trigger_error("Element class ".$ElementClass." not found");
			}
			
		}
	}
	
	
	function isVersioned() {
		return $this->hasExtension("Versioned");
	}
	
	
	static function is_versioned() {
		return Object::has_extension("Element", "Versioned");
	}
	
	
	/**
	 * returns @bool instance of HiddenElement interface
	 */
	function Hidden() {
		return $this instanceof HiddenElement;
	}
	
	
	/**
	 * needs param $SlotID to set the parent manually caused by weird things on ComplexTableField @ l550
	 * and Element in order to get the custom fields
	 */
	function CMSFields($SlotID) {
		
		if($this->ID==0) {
			$ClassField = new DropdownField("ClassName", "Type", $this->getClassDropdown(), $this->ClassName);
		} else {
			$ClassField = new ReadonlyField("Type", "Type", $this->getClassNiceName($this->ClassName));
		}
		
		$fs = new FieldSet(
			new TextField('Name', "Name", $this->Name),
			$ClassField,
			new HiddenField("SlotID", "", $SlotID)
		);
		
		if(!$this instanceof HiddenElement) {
			$fs->push($this->getExtraCMSFields());
		}
		
		$fs->merge($this->getCMSFields());
		return $fs;
	}
	
	
	public function getCMSFields() {
		$fs = new FieldSet();
		return $fs;
	}
	
	
	public function getExtraCMSFields() {
		$ExtraStyles = new TextareaField("ExtraStyles");
		$ExtraStyles->addExtraClass("elastic");
		
		$Prefix = new TextareaField("Prefix");
		$Prefix->addExtraClass("elastic");
		
		$Suffix = new TextareaField("Suffix");
		$Suffix->addExtraClass("elastic");
		
		$viewersOptionsField = new OptionsetField(
			"CanViewType", 
			"Who can view this element",
			$this->obj("CanViewType")->EnumValues()
		);
		
		$fs = new FieldSet(
			new TextField("ExtraClass"),
			$ExtraStyles,
			$Prefix,
			$Suffix,
			$viewersOptionsField
		);
		
		
		$fg = new FieldGroup(
			$fs,
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
		
		Requirements::block("assets/base.js");
		Requirements::block("assets/leftandmain.js");
		Requirements::block("sapphire/javascript/ComplexTableField_popup.js");
		Requirements::block("jsparty/leftandmain.js");
		Requirements::block("jsparty/scriptaculous/scriptaculous.js");
		
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		Requirements::javascript(SSPE_DIR."/javascript/ElementManager.js");
		Requirements::javascript(SSPE_DIR.'/javascript/jquery.elastic.js');
		Requirements::javascript(SSPE_DIR.'/javascript/jquery.textarea.js');
		Requirements::customScript(<<<JS
jQuery.noConflict();

jQuery(function($) {
	$("textarea.tabby").tabby();
	$("textarea.elastic").elastic();
	
});
JS
);
		Requirements::css(SSPE_DIR."/css/ElementManager.css");
	}
	
	
	public function forCMSTemplate() {
		if(!$this->canView()) {
			return "This element is restricted to <i>{$this->CanViewType}</i>";
		}
		return $this->forTemplate();
	}
	
	/**
	* Required for SlotManager_Controller::previewVersion
	* to replace Slot() call  in templates and only show
	* the actual element
	**/
	function previewSlot($name = null) {
		if($this->Slot()->Name == $name) {
			return $this;
		}
	}
	
	public function getClassDropdown() {
		$parent = __CLASS__;
		$classes = ClassInfo::subclassesFor($parent);
		$result = array();
		
		foreach($classes as $class) {
			if($class == $parent) continue;
			$instance = singleton($class);
			if((($instance instanceof HiddenClass) || !$instance->canCreate())){ continue; }
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
	 * Permission methods, can be overwritten
	 */
	
	public function canView($member = null) {
		if(!$member || !(is_a($member, 'Member')) || is_numeric($member)) $member = Member::currentUser();
		
		// check for any logged-in users
		if($this->CanViewType == 'LoggedInUsers' && $member) {
			return true;
		}
		
		// admin override
		if($member && Permission::checkMember($member, "ADMIN")) {
			return true;
		}
		
		// check for empty spec
		if(!$this->CanViewType || $this->CanViewType == 'Anyone') {
			return true;
		}
		
		if($this->CanViewType == "NotLoggedInUsers" && !$member) {
			return true;
		}
		
		return false;
	}
	
	
	
	function canPublish() {
		if($this->hasExtension("Versioned")) {
			if($this->stagesDiffer('Stage', 'Live')) {
				return $this->canEdit();
			}
		}
		return false;
	}
	
	
	function canEdit() {
		if($slot = $this->Slot()) {
			return $slot->canEdit();
		}
		return false;
	}
	
	
	function canCreate() {
		return $this->canEdit();
	}
	
	
	
	
	function EditIcon() {
		return SSPE_DIR . "/images/Element_edit.png";
	}
	
	function DeleteIcon() {
		return SSPE_DIR . "/images/Element_delete.png";
	}
	
	function DragIcon() {
		return SSPE_DIR . "/images/Element_drag.png";
	}
	
	function PublishIcon() {
		return SSPE_DIR . "/images/Element_publish.png";
	}
	
	function HistoryIcon() {
		return SSPE_DIR . "/images/Element_undo.png";
	}
	
	function PreviewIcon() {
		return SSPE_DIR . "/images/Element_preview.png";
	}
	
	function RevertIcon() {
		return $this->HistoryIcon();
	}
	
	//TODO: This could be nicer!
	function Link() {
		return Director::absoluteBaseURL()."admin/EditForm/field/Slots/item/".$this->SlotID."/DetailForm/field/Elements/";
	}
	
	function EditLink() {
		return $this->Link()."item/".$this->ID."/edit/";
	}
	
	function DeleteLink() {
		return $this->Link()."item/".$this->ID."/delete/";
	}
	
	function PublishLink() {
		return $this->Link()."item/".$this->ID."/publish/";
	}
	
	function HistoryLink() {
		return $this->Link()."item/".$this->ID."/history/";
	}
	
	
}
