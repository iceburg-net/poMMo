{capture name=head}{* used to inject content into the HTML <head> *}
{* Styling of CSS table *}
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/grid.css" />
{/capture}{include file="inc/tpl/admin.header.tpl"}

<h2>{t}Groups Page{/t}</h2>

<p><img src="{$url.theme.shared}images/icons/groups.png" class="navimage right" alt="groups icon" />
{t escape=no 1="<a href=\"`$url.base`admin/setup/setup_fields.php\">" 2="</a>"}Subscriber Groups allow you to mail subsets of your subscribers instead of the entire list. Groups are defined by customizable matching rules, and members are dynamically assigned based off %1subscriber field%2 values.{/t}
</p>

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
</form>


<fieldset>
<legend>{t}Groups{/t}</legend>

<div id="grid">

<div class="header">
<span>{t}Delete{/t}</span>
<span>{t}Edit{/t}</span>
<span>{t}Group Name{/t}</span>	
</div>

{foreach from=$groups key=id item=group}
<div class="{cycle values="r1,r2,r3"} sortable" id="id{$id}">
<span>
<button onclick="window.location.href='{$smarty.server.PHP_SELF}?group_id={$id}&amp;delete=TRUE'; return false;"><img src="{$url.theme.shared}images/icons/delete.png" alt="delete icon" /></button>
</span>

<span>
<button onclick="window.location.href='groups_edit.php?group_id={$id}'; return false;"><img src="{$url.theme.shared}images/icons/edit.png" alt="edit icon" /></button>
</span>

<span>
{$group.name}
</span>
</div>
{/foreach}

</div>



</fieldset>
{include file="inc/tpl/admin.footer.tpl"}