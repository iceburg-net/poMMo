{include file="admin/inc.header.tpl"}

<ul class="inpage_menu">
<li><a href="subscribers_manage.php">{t 1=$returnStr}Return to %1{/t}</a></li>
</ul>

<form method="post" action="">
<fieldset>
<input type="hidden" name="order" value="{$order}" />
<input type="hidden" name="orderType" value="{$orderType}" />
<input type="hidden" name="limit" value="{$limit}" />
<input type="hidden" name="table" value="{$table}" />
<input type="hidden" name="group_id" value="{$group_id}" />
<input type="hidden" name="action" value="{$action}" />
<input type="hidden" name="sid[]" value="{$sid}" />

{if $action == 'edit'}
<legend>Edit subscribers</legend>

<table summary="list of subscribers to edit">
<thead>
<tr>
<th>{t}email{/t}</th>
{foreach from=$fields key=key item=item}
<th>{$item.name}</th>
{/foreach}
</tr>
</thead>

<tbody>
{foreach name=sub from=$subscribers key=key item=item}
<tr>
<td>
<input type="hidden" name="editId[]" value="{$key}" />
<input type="hidden" name="date[{$key}]" value="{$item.date}" />
<input type="hidden" name="oldEmail[{$key}]" value="{$item.email}" />
<input type="text" name="email[{$key}]" value="{$item.email}" maxlength="60" /></td>

{foreach name=demo from=$fields key=demo_id item=demo}
<td>

{if $demo.type == 'text' || $demo.type == 'number'}
<input type="text" name="d[{$key}][{$demo_id}]" maxlength="60" value="{$item.data.$demo_id}" />

{elseif $demo.type == 'checkbox'}
<input type="checkbox" class="multiple" name="d[{$key}][{$demo_id}]"{if $item.data.$demo_id == 'on'} checked="checked"{/if} />

{elseif $demo.type == 'multiple'}
<select name="d[{$key}][{$demo_id}]">
{foreach name=option from=$demo.options item=option}
<option{if $item.data.$demo_id == $option} selected="selected"{/if}>{$option}</option>
{/foreach}
</select>
{else}

<span class="warn">{t}Unsupported field type{/t}</span>

{/if}

</td>
{/foreach}

</tr>
{/foreach}
</tbody>
</table>

</fieldset>

<div class="buttons">

<input type="submit" name="submit" value="{t}Update{/t}" />

</div>	

{elseif $action == 'delete'}

<div id="errormsg" class="error">

<p><strong>{t}The following will be deleted{/t}.</strong></p>

</div>

<legend>Delete subscribers</legend>

<div class="error">

<ul>
{foreach from=$emails item=email}
<li>{$email} <input type="hidden" name="deleteEmails[]" value="{$email}" /></li>
{/foreach}
</ul>

</div>

</fieldset>

<div class="buttons">

<input type="submit" name="submit" value="{t}Delete{/t}" />

</div>

{elseif $action == 'add'}

<legend>Add subscribers</legend>

<p>{t}The following will be added as subscribers{/t}.</p>

<ul>
{foreach from=$emails item=email}
<li>{$email} <input type="hidden" name="addEmails[]" value="{$email}" /></li>
{/foreach}
</ul>

</fieldset>

<div class="buttons">

<input type="submit" name="submit" value="{t}Add{/t}" />

</div>

{/if}
</form>

{include file="admin/inc.footer.tpl"}