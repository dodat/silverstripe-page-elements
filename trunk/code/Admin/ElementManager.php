<?php

class ElementManager extends ComplexTableField {
	
	function handleItem($request) {
		return new ElementManager_ItemRequest($this, $request->param('ID'));
	}
	
	function getCustomFieldsFor($childData) {
		if(is_a($this->detailFormFields,"Fieldset"))  {
			$fields = $this->detailFormFields;
		} else {
			if(!is_string($this->detailFormFields)) $this->detailFormFields = "getCMSFields";
			$functioncall = $this->detailFormFields;
			if(!$childData->hasMethod($functioncall)) $functioncall = "getCMSFields";
			//pass the childdata on
			$fields = $childData->$functioncall($this->controller->ID);
		}
		
		return $fields;
	}
	
	
	function saveComplexTableField($data, $form, $params) {
		
		$className = $this->sourceClass();
		$childData = new $className();
		$form->saveInto($childData);
		$childData->SlotID = $this->controller->ID;
		$childData->write();
		if($childData->hasExtension("Versioned")) {
			// publishing versioned
			$childData->publish("Stage", "Live");
		}
		$form->sessionMessage('Added successfully', 'good');
		
		Director::redirect($this->Link().'/item/'.$childData->ID.'/edit');
		
	}
	
}

class ElementManager_ItemRequest extends ComplexTableField_ItemRequest {
	
	
	function saveComplexTableField($data, $form, $request) {
		
		$dataObject = $this->dataObj();
		$form->saveInto($dataObject);
		$dataObject->write();
		
		if($dataObject->hasExtension("Versioned")) {
			// publishing versioned
			$dataObject->publish("Stage", "Live");
		}
		
		$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
		$closeLink = sprintf(
			'<small><a href="' . $referrer . '" onclick="javascript:window.top.GB_hide(); return false;">(%s)</a></small>',
			_t('ComplexTableField.CLOSEPOPUP', 'Close Popup')
		);
		$message = sprintf(
			_t('ComplexTableField.SUCCESSEDIT', 'Saved %s %s %s'),
			Element::getClassNiceName($dataObject->ClassName),
			"<em>`".$dataObject->Title."`</em>",
			$closeLink
		);
		
		$form->sessionMessage($message, 'good');
		Director::redirectBack();
	}
	
	function publish() {
		//TODO Permission check to come here
		$this->dataObj()->publish("Stage", "Live");
	
	}
	
}
