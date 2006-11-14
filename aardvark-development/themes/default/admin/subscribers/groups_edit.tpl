{capture name=head}{* used to inject content into the HTML <head> *}
<script src="{$url.theme.shared}js/scriptaculous/prototype.js" type="text/javascript"></script>
<script src="{$url.theme.shared}js/scriptaculous/effects.js" type="text/javascript"></script>
{/capture}
{include file="admin/inc.header.tpl"}

<ul class="inpage_menu">
<li><a href="{$url.base}admin/subscribers/subscribers_groups.php">{t 1=$returnStr}Return to %1{/t}</a></li>
</ul>

<h2>{t}Edit Group{/t}</h2>

<p><img src="{$url.theme.shared}images/icons/groups.png" alt="groups icon" class="articleimg" />{t}They are made up of "filters" that match field values or other groups. For instance, if you collect "age" and "country", you can match subscribers 21 and older living in Japan by creating two filtering critiera; one which matches "age" to a value GREATER THAN 20, and another which matches "country" EQUAL TO "Japan"{/t}.</p>

{include file="admin/inc.messages.tpl"}

<form method="post" action="" id="nameForm" name="nameForm">
<fieldset>
<legend>{$group_name}</legend>

<div>
<label for="group_name">{t}Group name:{/t}</label> <input type="text" title="{t}type new group name{/t}" maxlength="60" size="30" name="group_name" id="group_name"  value="{$group_name}" />
</div>

</fieldset>

<div class="buttons">

<input type="submit" name="rename" value="{t}Rename{/t}" />

</div>

</form>

<form method="post" action="" id="filterForm" name="filterForm">
<fieldset>
<legend>Group filter</legend>

<div id="newFilter">

<div id="field">
<label for="field_id">{t escape=no 1="<strong>" 2="</strong>"}Select a %1 field %2 to filter{/t}</label>

<select name="field_id" id="field_id" onchange="updateLogic()">
<option value="">{t}Choose subscriber field{/t}</option>
{foreach from=$fields key=id item=field}
<option value="{$id}">{$field.name}</option>
{/foreach}
</select>
</div>

{if count($groups) > 1}
<div id="group">
<label for="group_logic">Filter by</label>
<select name="group_logic" id="group_logic" onChange="updateGroup({$group_id})">
<option value="">Choose to Include or Exclude</option>	
<option value="is_in">{t}Include{/t}</option>
<option value="not_in">{t}Exclude{/t}</option>
</select>
</div>
{/if}

<div id="critLogic"></div>

</div>
</fieldset>
</form>

<p>{t escape=no 1="<em>`$filterCount`</em>" 2="<strong>`$tally`</strong>"}%1 filters match a total of %2 subscribers{/t}</p>

<div id="filters">

<span>{t}Delete{/t}</span>
<span style="margin-left: 20px;">{t}Edit{/t}</span>
<span style="text-align:left; margin-left: 20px;">{t}Filter Details{/t}</span>
	
{foreach from=$filters key=filter_id item=filter}
<div style="border-top: 1px dotted; padding: 5px;">
	<a href="{$smarty.server.PHP_SELF}?filter_id={$filter_id}&delete=TRUE&group_id={$group_id}">
 	 		<img src="{$url.theme.shared}images/icons/delete.png" border="0" align="absmiddle"></a>
	<span style="margin-left: 25px; cursor:pointer; cursor:hand;" onClick="filterUpdate('{$filter_id}','{$group_id}')" >
			<img src="{$url.theme.shared}images/icons/edit.png" border="0" align="absmiddle">
	</span>
	<span style="text-align:left; margin-left: 12px;">
		{if $filter.logic == 'is_in'}
			{t}Include subscribers belonging to{/t} <strong>{$groups[$filter.field_id]}</strong>
		{elseif $filter.logic == 'not_in'}
			{t}Exclude subscribers belonging to{/t} <strong>{$groups[$filter.field_id]}</strong>
		{elseif $filter.logic == 'is_equal'}
			{t escape=no 1="<strong>`$fields[$filter.field_id].name`</strong>" 2="<em>`$filter.value`</em>}Include subscribers who have %1 equal to %2{/t}
		{elseif $filter.logic == 'not_equal'}
			{t escape=no 1="<strong>`$fields[$filter.field_id].name`</strong>" 2="<em>`$filter.value`</em>}Exclude subscribers who have %1 equal to %2{/t}
		{elseif $filter.logic == 'is_more'}
			{t escape=no 1="<strong>`$fields[$filter.field_id].name`</strong>" 2="<em>`$filter.value`</em>}Include subscribers who have %1 greater than %2{/t}
		{elseif $filter.logic == 'is_less'}
			{t escape=no 1="<strong>`$fields[$filter.field_id].name`</strong>" 2="<em>`$filter.value`</em>}Include subscribers who have %1 less than %2{/t}
		{elseif $filter.logic == 'not_true'}
			{t}Exclude subscribers that checked{/t} <strong>{$fields[$filter.field_id].name}</strong>
		{elseif $filter.logic == 'is_true'}
			{t}Include subscribers that checked{/t} <strong>{$fields[$filter.field_id].name}</strong>
		{/if}
	</span>
</div>
{foreachelse}
 	<p><strong>{t}No filters have been assigned.{/t}</strong></p>
{/foreach}

</div>

{if (0) }// Better semantics, but doesn work with current JS. Use when JS changes to jQuery
<div id="filters">

<table summary="list of fiters and controls">
<thead>
<tr>
<th>{t}Filter Details{/t}</th>
<th>{t}Edit{/t}</th>
<th>{t}Delete{/t}</th>
</tr>
</thead>

<tbody>
{foreach from=$filters key=filter_id item=filter}
<tr>
<td>
{if $filter.logic == 'is_in'}
{t}Include subscribers belonging to{/t} <strong>{$groups[$filter.field_id]}</strong>

{elseif $filter.logic == 'not_in'}
{t}Exclude subscribers belonging to{/t} <strong>{$groups[$filter.field_id]}</strong>

{elseif $filter.logic == 'is_equal'}
{t escape=no 1="<strong>`$fields[$filter.field_id].name`</strong>" 2="<em>`$filter.value`</em>}Include subscribers who have %1 equal to %2{/t}

{elseif $filter.logic == 'not_equal'}
{t escape=no 1="<strong>`$fields[$filter.field_id].name`</strong>" 2="<em>`$filter.value`</em>}Exclude subscribers who have %1 equal to %2{/t}

{elseif $filter.logic == 'is_more'}
{t escape=no 1="<strong>`$fields[$filter.field_id].name`</strong>" 2="<em>`$filter.value`</em>}Include subscribers who have %1 greater than %2{/t}

{elseif $filter.logic == 'is_less'}
{t escape=no 1="<strong>`$fields[$filter.field_id].name`</strong>" 2="<em>`$filter.value`</em>}Include subscribers who have %1 less than %2{/t}

{elseif $filter.logic == 'not_true'}
{t}Exclude subscribers that checked{/t} <strong>{$fields[$filter.field_id].name}</strong>

{elseif $filter.logic == 'is_true'}
{t}Include subscribers that checked{/t} <strong>{$fields[$filter.field_id].name}</strong>

{/if}
</td>

<td><button onlick="filterUpdate('{$filter_id}','{$group_id}')"><img src="{$url.theme.shared}images/icons/edit.png" alt="edit icon" /></button></td>

<td><button onlick="window.location.href='{$smarty.server.PHP_SELF}?filter_id={$filter_id}&amp;delete=TRUE&amp;group_id={$group_id}';return false;"><img src="{$url.theme.shared}images/icons/delete.png" alt="delete icon" /></button></td>
</tr>

{foreachelse}
<tr>
<td colspan="3"><strong>{t}No filters have been assigned.{/t}</strong></td>
</tr>
{/foreach}

</tbody>
</table>

</div>
{/if}

<div class="buttons">

<input type="checkbox" id="focusStealer" name="focusStealer" />

</div>

{literal}
<script type="text/javascript">
// <![CDATA[

/* TODO - CREATE AN OBJECT OF THIS MESS */

function updateLogic() {
	poll = new Ajax.Updater(
		'critLogic',
		'ajax_filters.php',
		{
			asynchronous:true,
			parameters: "field_id="+$('field_id').value
		}
	);
	hide();
}

function updateGroup(curGroup) {
	poll = new Ajax.Updater(
		'critLogic',
		'ajax_filters.php',
		{
			asynchronous:true,
			parameters: "group_id="+curGroup+"&group_logic="+$('group_logic').value
		}
	);
	hide();
}

function filterUpdate(filter_id,group_id) {
	poll = new Ajax.Updater(
		'critLogic',
		'ajax_filters.php',
		{
			asynchronous:true,
			parameters: "update=TRUE&filter_id="+filter_id+"&group_id="+group_id
		}
	);
	hide();
}

function hide() {
	Effect.BlindUp('group');
	Effect.BlindUp('field');
	Effect.BlindUp('filters');
	Effect.Appear('critLogic');
	$('field_id').blur();
	$('group_logic').blur();
	$('focusStealer').focus();
}

function reset(val) {
	$('critLogic').innerHTML = "";
	$('group_logic').value = "";
	$('field_id').value = "";
	Effect.BlindDown('field');
	Effect.BlindDown('group');
	Effect.BlindDown('filters');
}

Effect.Fade('critLogic');
// ]]>
</script>
{/literal}

{include file="admin/inc.footer.tpl"}