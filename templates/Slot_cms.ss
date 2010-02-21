    <table class="SlotHeader">
        <tr>
            <td><h4>$Name</h4></td>
            <td class="actions">
                <a class="popuplink" href="$AddLink" title="Add an Element"><img src="/$AddIcon" alt="Add an Element" title="Add an Element"/></a>
	    </td>
	    </tr>
	</table>
	<div class="Slot $Name" id="Slot-$ID">

<% control Elements %>

<div class="$parentClass $ClassName $HTMLID" id="$parentClass-$ID">
	<table class="ElementHeader">
		<tr>
			<td class="handle icon">
				<img src="$DragIcon" alt="Drag Element `$Name`" title="Drag Element `$Name`"/>
			</td>
			<td class="name editable">$Name</td>
			<td class="type"><small>$ClassNiceName</small></td>
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
	<table class="SlotContent">
		<tr>
			<td>
			$Prefix.RAW
			$forCMSTemplate
			$Suffix.RAW
			</td>
		</tr>
	</table>	
</div>
<% end_control %>

</div>