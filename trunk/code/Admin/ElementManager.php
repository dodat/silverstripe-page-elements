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
	
	function history() {
		
		//print_r($this->dataObj());
		$this->dataObj()->flushCache();
		
		//$Versions = Versioned::get_all_versions($this->dataObj()->ClassName, $this->dataObj()->ID, "Stage");
		//Versioned::Versions()
		
		//Versioned::$reading_stage = "Stage";
		
		$Versions = $this->dataObj()->allVersions();
		foreach($Versions as $Version) {
			
			echo $Version->Version;
			
		}
		
		
		
		/**
		<% control Versions %>$ClassName
	<tr id="page-$RecordID-version-$Version" class="$EvenOdd $PublishedClass">
		<td>$Version</td>
		<td class="$LastEdited" title="$LastEdited.Ago - $LastEdited.Nice">$LastEdited.Ago</td>
		<td>$Author.FirstName $Author.Surname.Initial</td>
		<td>
		<% if Published %>
			<% if Publisher %>
				$Publisher.FirstName $Publisher.Surname.Initial
			<% else %>	
				<% _t('UNKNOWN','Unknown') %>
			<% end_if %>
		<% else %>
			<% _t('NOTPUB','Not published') %>
		<% end_if %>
		</td>			
	</tr>
	<% end_control %>**/
	}
	
	
	
	
	function versions() {
		$pageID = $this->urlParams['ID'];
		$page = $this->getRecord($pageID);
		if($page) {
			$versions = $page->allVersions($_REQUEST['unpublished'] ? "" : "`SiteTree`.WasPublished = 1");
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
