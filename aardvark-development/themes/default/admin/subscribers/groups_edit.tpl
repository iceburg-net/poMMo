{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/interface.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/validate.js"></script>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/modal.css" />
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/table.css" />
{/capture}
{include file="inc/admin.header.tpl"}

<ul class="inpage_menu">
<li><a href="{$url.base}admin/subscribers/subscribers_groups.php">{t 1=$returnStr}Return to %1{/t}</a></li>
</ul>

<h2>{t}Edit Group{/t}</h2>

<p>
<img src="{$url.theme.shared}images/icons/groups.png" alt="groups icon" class="navimage right" />
{t escape=no 1="<a href=\"`$url.base`admin/setup/setup_fields.php\">" 2="</a>"}To add subscribers to a group you must create matching rules. Subscribers are automatically added to a group if their %1subscriber field%2 values "match" a Group's rules. For example, if you collect "AGE" and "COUNTRY" as %1subscriber fields%2, you can match those who are 21+ and living in Japan by creating two rules; one which matches "AGE" to greater than 20, and another which matches "Japan" to "COUNTRY". You can also include or exclude members of other groups.{/t}
</p>



{include file="inc/messages.tpl"}

<form method="post" action="" id="nameForm" name="nameForm">
<fieldset>
<legend>{t}Change Name{/t}</legend>

<div>
<label for="group_name">{t}Group name:{/t}</label> <input type="text" title="{t}type new group name{/t}" maxlength="60" size="30" name="group_name" id="group_name"  value="{$group.name|escape}" />
</div>

</fieldset>

<div class="buttons">
<input type="submit" name="rename" value="{t}Rename{/t}" />
</div>

</form>

<form method="post" action="" id="filterForm" name="filterForm">
<fieldset>
<legend>{t}Add Rule{/t}</legend>

<div id="newFilter">

{* filterWindow popup *}
<div id="filterWindow">
<a href="#" class="fwClose" alt="field_id">{t}Close window{/t}</a>
<div class="fwContent"></div>
</div>

<div>
<label for="field">{t escape=no 1="<strong><a href=\"`$url.base`admin/setup/setup_fields.php\">" 2="</a></strong>"}Select a %1 field %2 to filter{/t}</label>
<select name="field" id="field" alt="{$group.id}">
<option value="">-- {t}Choose Subscriber Field{/t} --</option>
{foreach from=$new key=id item=name}
<option value="{$id}">{$fields[$id].name}</option>
{/foreach}
</select>
</div>

<div>
<label for="group">{t escape=no 1="<strong><a href=\"`$url.base`admin/subscribers/subscribers_groups.php\">" 2="</a></strong>"}or, Select a %1 group %2 to include or exclude{/t}</label>
<select name="group" id="group" alt="{$group.id}">
<option value="">-- {t}Choose Group{/t} --</option>
{foreach from=$gnew key=id item=name}
<option value="{$id}">{$name}</option>
{/foreach}
</select>
</div>

</div>
</fieldset>

<fieldset>
<legend>{t}Group Rules{/t}</legend>

<table summary="list of rules">
<thead>
<tr>
<th>{t}Delete{/t}</th>
<th>{t}Edit{/t}</th>
<th>{t}Field{/t}</th>
<th>{t}Logic{/t}</th>
<th>{t}Value(s){/t}</th>
</tr>
</thead>
<tbody>
{foreach name=outter from=$filters key=field_id item=logicArray}
	{foreach name=inner from=$logicArray key=logic item=valArray}
	{if $field_id == 0}{* group match *}
	<tr class="{cycle values="r1,r2,r3" advance=false}">
	<td colspan="3"></td><td colspan="2">{$english[$logic]}</td>
	</tr>
	{foreach name=v from=$valArray item=value}
		<tr class="{cycle values="r1,r2,r3"}">
		
		<td>
		<button onclick="window.location.href='{$smarty.server.PHP_SELF}?group_id={$group.id}&amp;groupDelete={$value}&amp;logic={$logic|escape}'; return false;">
		<img src="{$url.theme.shared}images/icons/delete.png" alt="delete icon" />
		</button>
		</td>
		
		<td></td>
		
		<td colspan="2">
		<td>{$groups[$value].name}</td>
		
		</tr>
	{/foreach}
	{else}
	<tr class="{cycle values="r1,r2,r3"}">
	<td>
		<button onclick="window.location.href='{$smarty.server.PHP_SELF}?group_id={$group.id}&amp;fieldDelete={$field_id}&amp;logic={$logic|escape}'; return false;">
		<img src="{$url.theme.shared}images/icons/delete.png" alt="delete icon" />
		</button>
	</td>
	
	<td>
		{if $logic != 'true' && $logic != 'false'}{* DO NOT ALLOW EDITING OF CHECKBOXES *}
		<button onclick="fwAjaxCall({$field_id},'field',{$group.id},'{$logic}'); return false;">
		<img src="{$url.theme.shared}images/icons/edit.png" alt="edit icon" />
		</button>
		{/if}
	</td>
	
	<td>{$fields[$field_id].name}</td>
	
	<td>{$english[$logic]}</td>
	
	<td>
		<ul>
		{foreach from=$valArray item=value}<li>{$value}</li>{/foreach}
		</ul>
	</td>
	
	</tr>
	{/if}
	{/foreach}
{foreachelse}
<tr>
<td colspan="3"><strong>{t}No groups have been assigned.{/t}</strong></td>
</tr>
{/foreach}

</tbody>
</table>

</fieldset>

</form>

<p>{t escape=no 1="<em>`$filterCount`</em>" 2="<strong>`$tally`</strong>"}%1 rules match a total of %2 active subscribers{/t}</p>

{literal}
<script type="text/javascript">
function fwAjaxCall(id, name, gid, l) {
	$('#newFilter select').each(function() { $(this).hide(); });

	var p = (typeof(l) == 'undefined') ?
		{ID: id, add: name, group: gid} :
		{ID: id, add: name, group: gid, logic: l};

	$('#filterWindow div.fwContent').load(
		'ajax/group_edit.php',
		p,
		function() {
			$('#filterWindow a.fwClose').attr("alt",name);
			$('#'+name).TransferTo({to:'filterWindow',className:'fwTransfer', duration: 300, complete:function(to){$(to).fadeIn(200)}});
			PommoValidate.reset();
			PommoValidate.init('#fwValue input[@name=v]', '#fwSubmit', false);
		}
	);
	return false; // don't follow href
}
$().ready(function(){ 

	$('#filterWindow a.fwClose').click(function() {
		var name = $(this).attr('alt');
		$('#newFilter select').each(function() { $(this).show().val(''); });
		$('#filterWindow').fadeOut(200, function(){$(this).TransferTo({to: name,className:'fwTransfer', duration: 300})});
		return false;
	});

	$('#newFilter select').change(function() {
		var name = $(this).name(); // "field" or "group"
		var id = $(this).val(); // group or field ID
		var gid = $(this).attr("alt"); // this group's ID
		if(id != '') 
			fwAjaxCall(id, name, gid);
	});
});
</script>
{/literal}

{include file="inc/admin.footer.tpl"}