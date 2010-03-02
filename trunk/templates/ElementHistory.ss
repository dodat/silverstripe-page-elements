<div class="ElementHistoryBrowser" id="Element-$ID">
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
                    <a href="#" class="previewVersion">preview</a>
                    <a href="#" class="revertElement">revert</a>
                </td>
	</tr>
	<% end_control %>
    </tbody>
</table>

<div class="previewwindow">
    <h1>Preview</h1>
    <iframe id="ElementPreview" src="SlotManager/previewVersion/$ID/Stage" ></iframe>
</div>

</div>