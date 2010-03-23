<% control Elements %>
 <% if Hidden != true %>
  <div class="$parentClass $ClassName<% if ExtraClass %> $ExtraClass<% end_if %>"<% if ExtraStyles %> style="$ExtraStyles"<% end_if %> id="$HTMLID">
   $Prefix
   $Me
   $Suffix
  </div>
 <% else %>
  $Me
 <% end_if %>
<% end_control %>
