<hr>
<input type="hidden" id="fwGroupID" name="group_id" value="{$group_id}">
<input type="hidden" id="fwMatchID" name="field_id" value="{$field.id}">

<div>{t}Add new filter:{/t}</div>

<table cellpadding="5" border="0">
<tr>
<td valign="top">
{t}Match subscribers where{/t} <strong>{$field.name}</strong> 
<select name="logic" id="fwLogic">
{foreach from=$logic key=val item=desc}
<option value="{$val}">{$desc}</option>
{/foreach}
</select>
</td>

{if $field.type == 'multiple'}

<td valign="top" id="fwValue">
<select name="v">
{foreach from=$field.array item=option}
<option>{$option}</option>
{/foreach}
</select>
<input type="button" value="+" id="fwAddValue">
</td>

{elseif $field.type != 'checkbox'}

<td valign="top" id="fwValue">
<input type="text" name="v">
<input type="button" value="+" id="fwAddValue">
</td>

{/if}


</tr>
</table>

<div class="buttons">
	<input type="button" value="{t}Add{/t}" id="fwSubmit">
</div>
<hr>


{literal}
<script type="text/javascript">
// need these for ie -- IE can't find functions declared here due to scoping??
$('#fwAddValue').click(function() {
	x = $('*:first-child', $(this).parent()).get(0);
	$(this).parent().append('<br>or ').append($(x).clone());
});
$('#fwSubmit').oneclick(function() {
	var _logic = $('#fwLogic').val();
	var _group = $('#fwGroupID').val();
	var _match = $('#fwMatchID').val();
	var _value = $('#fwValue input[@type=text], #fwValue select').serialize();

	$.post("ajax_filter_update.php",
		{ logic: _logic, group: _group, match: _match, value: _value },
		function(out) {
			alert(out);
			var name = $('#filterWindow a.fwClose').attr('alt');
			$('#newFilter select').each(function() { $(this).show().val(''); });
			$('#filterWindow').fadeOut(200, function(){$(this).TransferTo({
				to: name,
				className:'fwTransfer',
				duration: 300,
				complete: function() { location.reload(true); }
			})});
		}
	);
});

</script>
{/literal}