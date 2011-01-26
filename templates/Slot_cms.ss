<table class="SlotHeader">
    <tr>
        <td><h4>$Name</h4></td>
		<td class="actions">
			<a class="popuplink" href="$AddLink" title="Add an Element"><img src="$AddIcon" alt="Add an Element to `$Name`" title="Add an Element to `$Name`"/></a>
		</td>
	</tr>
</table>

<div class="Slot $Name" id="Slot-$ID">
<% control Elements %>
	<div class="Element $ClassName $HTMLID" id="$parentClass-$ID">
		<table class="ElementHeader">
			<tr>
				<td class="handle icon">
					<img src="$DragIcon" alt="Drag Element `$Name`" title="Drag Element `$Name`"/>
				</td>
				<td class="name editable">$Name</td>
				<td class="type"><small>$ClassNiceName</small></td>
				<% if isVersioned %>
				<td class="actions icon">
					<a href="$HistoryLink" class="popuplink historylink" title="View history for Element `$Name`">
						<img src="$HistoryIcon" alt="View history for Element `$Name`" title="View history for Element `$Name`"/>
					</a>
				</td>
				<% end_if %>
				<% if canPublish %>
				<td class="actions icon">
					<a href="$PublishLink" class="popuplink publishlink" title="Publish Element `$Name`">
						<img src="$PublishIcon" alt="Publish Element `$Name`" title="Publish Element `$Name`"/>
					</a>
				</td>
				<% end_if %>
				<td class="actions icon">
					<a href="$EditLink" class="popuplink editlink" title="Edit Element `$Name`">
						<img src="$EditIcon" alt="Edit Element `$Name`" title="Edit Element `$Name`"/>
					</a>
				</td>
				<td class="actions icon">
					<a href="$DeleteLink" class="popuplink deletelink" title="Delete Element `$Name`">
						<img src="$DeleteIcon" alt="Delete Element `$Name`" title="Delete Element `$Name`"/>
					</a>
				</td>
			</tr>
		</table>
		<div class="ElementContent" id="$HTMLID">
			$Prefix.RAW
			$forCMSTemplate
			$Suffix.RAW
		</div>
	</div>
<% end_control %>
</div>
