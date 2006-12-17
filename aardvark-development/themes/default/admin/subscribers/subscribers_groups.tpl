{include file="inc/tpl/admin.header.tpl"}

<h2>{t}Groups Page{/t}</h2>

<p><img src="{$url.theme.shared}images/icons/groups.png" class="navimage right" alt="groups icon" />{t}Create groups of subscribers based off the values of subscriber fields. You can then mail subscribers belonging to a group instead your entire list.{/t}</p>

<form method="post" action="">

{include file="inc/tpl/messages.tpl"}

<fieldset>
<legend>{t}New group{/t}</legend>

<div>
<label for="group_name">{t}Group name{/t}</label>
<input type="text" title="{t}type new group name{/t}" name="group_name" id="group_name" maxlength="60" size="30" />
</div>

<div class="buttons">

<input type="submit" value="{t}Add{/t}" />

</div>

</fieldset>

<fieldset>
<legend>{t}Groups{/t}</legend>

<table summary="list of groups and controls">
<thead>
<tr>
<th>{t}Group Name{/t}</th>
<th>{t}Edit{/t}</th>
<th>{t}Delete{/t}</th>
</tr>
</thead>

<tbody>
{foreach from=$groups key=id item=group}
<tr>
<td>{$group.name}</td>
<td><button onclick="window.location.href='groups_edit.php?group_id={$id}'; return false;"><img src="{$url.theme.shared}images/icons/edit.png" alt="edit icon" /></button></td>
<td><button onclick="window.location.href='{$smarty.server.PHP_SELF}?group_id={$id}&amp;delete=TRUE'; return false;"><img src="{$url.theme.shared}images/icons/delete.png" alt="delete icon" /></button></td>
</tr>

{foreachelse}
<tr>
<td colspan="3"><strong>{t}No groups have been assigned.{/t}</strong></td>
</tr>
{/foreach}

</tbody>
</table>

</fieldset>
</form>

{include file="inc/tpl/admin.footer.tpl"}