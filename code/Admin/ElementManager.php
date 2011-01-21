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
		
		$itemrq = new ElementManager_ItemRequest($this, $childData->ID);
		
		Director::redirect($itemrq->Link("edit"));
		
	}
	
	/** temporary fix for non superadmin users to edit elements **/
	function can($action = null) {
		return Permission::check("CMS_ACCESS_CMSMain");
	}
	
	
}

class ElementManager_ItemRequest extends ComplexTableField_ItemRequest {
	
	
	function saveComplexTableField($data, $form, $request) {
		
		$dataObject = $this->dataObj();
		$form->saveInto($dataObject);
		$dataObject->write();
		
		$dataObject->flushCache();
		
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
		$this->dataObj()->flushCache();
	}
	
	function delete() {
		if(Element::is_versioned()) {
			/**
			 * temporary deleting all versions.
			 * including the live version
			 * TODO: clearify interface to manage
			 * elements with only a live version
			 */
			$this->deleteAllVersions();
		}
		return $this->dataObj()->delete();
	}
	
	function deleteAllVersions() {
		$this->dataObj()->deleteFromStage("Live"); 
		foreach($this->dataObj()->allVersions() as $version) {
			$version->delete();
		}
	}
	
	function history() {
		
		
		Requirements::css(SAPPHIRE_DIR . '/css/Form.css');
		Requirements::css(SAPPHIRE_DIR . '/css/ComplexTableField_popup.css');
		Requirements::css(CMS_DIR . '/css/typography.css');
		Requirements::css(CMS_DIR . '/css/cms_right.css');
		
		
		
		if($this->dataObj()->hasMethod('getRequirementsForPopup')) {
			$this->dataObj()->getRequirementsForPopup();
		}
		$this->dataObj()->flushCache();
		
		echo $this->customise(
			array("DetailForm"=>$this->HistoryBrowser())
		)->renderWith($this->ctf->templatePopup);
	}
	
	
	function HistoryBrowser() {
		$childData = $this->dataObj();
		return $this->dataObj()->renderWith("ElementHistory");	
	}
	
	
	
	function versions() {
		$pageID = $this->urlParams['ID'];
		$page = $this->getRecord($pageID);
		if($page) {
			$versions = $page->allVersions($_REQUEST['unpublished'] ? "" : "\"SiteTree\".\"WasPublished\" = 1");
			return array(
				'Versions' => $versions,
			);
		} else {
			return sprintf(_t('CMSMain.VERSIONSNOPAGE',"Can't find page #%d",PR_LOW),$pageID);
		}
	}

	/**
	 * Roll a page back to a previous version
	 */
	function rollback() {
		if(isset($_REQUEST['Version']) && (bool)$_REQUEST['Version']) {
			$record = $this->performRollback($_REQUEST['ID'], $_REQUEST['Version']);
			echo sprintf(_t('CMSMain.ROLLEDBACKVERSION',"Rolled back to version #%d.  New version number is #%d"),$_REQUEST['Version'],$record->Version);
		} else {
			$record = $this->performRollback($_REQUEST['ID'], "Live");
			echo sprintf(_t('CMSMain.ROLLEDBACKPUB',"Rolled back to published version. New version number is #%d"),$record->Version);
		}
	}
	
}
