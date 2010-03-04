
<div class="ElementHistoryBrowser" id="Element-$ID">
	<div class="historyTable">
		<h1>History for Element "$Name"</h1>
		<table>
		    <thead>
		        <tr>
		            <th>Version</th>
		            <th>Date</th>
		            <th>Actions</th>	            
				</tr>
			</thead>
			<tbody>
			<% control allVersions %>
				<tr id="Version-$Version" class="$EvenOdd $PublishedClass">
					<td>$Version</td>
					<td class="$LastEdited" title="$LastEdited.Ago - $LastEdited.Nice">$LastEdited.Ago</td>
		            <td>
		                <a href="SlotManager/previewVersion/$RecordID/$Version" class="previewVersion defaultaction"><img src="$PreviewIcon" alt="preview"/></a>
			            <a href="SlotManager/revertElement/$RecordID/$Version" class="revertElement"><img src="$RevertIcon" alt="Revert to Version $Version"/></a>
			        </td>
				</tr>
			<% end_control %>
		    </tbody>
		</table>
	</div>
	<div class="previewwindow">
	    <h2>Preview</h2>
	    <iframe id="ElementPreview" src="SlotManager/previewVersion/$ID/Stage" ></iframe>
	</div>
</div>
