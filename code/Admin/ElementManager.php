<?php

class ElementManager extends ComplexTableField {
	
	
	
	function getCustomFieldsFor($childData) {
		if(is_a($this->detailFormFields,"Fieldset"))  {
			$fields = $this->detailFormFields;
		} else {
			if(!is_string($this->detailFormFields)) $this->detailFormFields = "getCMSFields";
			$functioncall = $this->detailFormFields;
			if(!$childData->hasMethod($functioncall)) $functioncall = "getCMSFields";
			//pass the childdata on
			$fields = $childData->$functioncall($childData, $this->controller->ID);
		}
		
		return $fields;
	}
	
	
	function saveComplexTableField($data, $form, $params) {
		
		$className = $this->sourceClass();
		$childData = new $className();
		$form->saveInto($childData);
		$childData->SlotID = $this->controller->ID;
		$childData->write();
		
		$form->sessionMessage('Added successfully', 'good');
		
		Director::redirect($this->Link().'/item/'.$childData->ID.'/edit');
		
	}
	
}
