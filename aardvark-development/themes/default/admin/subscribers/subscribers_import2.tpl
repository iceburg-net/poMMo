{include file="admin/inc.header.tpl"}

<ul class="inpage_menu">
<li><a href="admin_subscribers.php">{t 1=$returnStr}Return to %1{/t}</a></li>
<li><a href="subscribers_import.php">{t}Upload a different file{/t}</a></li>
</ul>

{if $page == 'preview'}	

<h2>{t}Preview Import{/t}</h2>

{t escape=no 1="<strong>`$totalImported`</strong>" 2="<strong>`$totalImported`</strong>"}%1 Subscribers will be imported. Of these, %2 will be flagged for update due to invalidity.{/t}

{t escape=no 1="<strong>`$totalDuplicate`</strong>"}%1 Duplicate{/t}.

{include file="admin/confirm.tpl"}

{include file="admin/inc.messages.tpl"}

{elseif $page == 'import'}

<h2>{t}Import Complete!{/t}</h2>

<p><a href="{$url.base}admin/subscribers/admin_subscribers.php"><img src="{$url.theme.shared}images/icons/back.png" alt="back icon" class="navimage" />{t 1=$returnStr}Return to %1{/t}</a></p>

{elseif $page == 'assign'}

<h2>{t}Upload Success{/t}</h2>

{t escape=no 1='<strong>' 2='</strong>'}Optionally, you may match the values to a subscriber field. If an imported subscriber is missing a value for a required field, they will be %1 flagged %2 to update their information.{/t}

<form method="post" action="">
<fieldset>
<legend>Data merge</legend>

<table summary="CSV inport rules">
<thead>
<tr>
<th>&nbsp;</th>
{section name="fieldloop" start=1 loop=$numFields}
<th class="{cycle values="bg1,bg2"}">{t 1=$smarty.section.fieldloop.index}Field #%1{/t}</th>
{/section}
</tr>

<tr>
<th>line #</th>
{section name="field" start=0 loop=$numFields}
<th class="{cycle values="bg1,bg2"}">

{if $smarty.section.field.index == $emailField}
<em>email</em><input type="hidden" name="field[{$smarty.section.field.index}]" value="email" />
{else}
<select name="field[{$smarty.section.field.index}]">
<option value="ignore">{t}Ignore Field{/t}</option>
<option value="ignore">----------------</option>
{foreach from=$fields key=key item=item}
<option value="{$key}">{$item.name}</option>
{/foreach}
</select>
{/if}

</th>
{/section}

</tr>
</thead>

<tbody>
{* output from file now... use data from lineWithMostFields *}
<tr>
<td>
{$csvArray.lineWithMostFields+1}
</td>

{section name="field" start=0 loop=$numFields}
<td>
{$entry[$smarty.section.field.index]}
</td>
{/section}
</tr>
</tbody>
</table>

</fieldset>

<p>{t 1=$csvArray.csvFile|@count}%1 subscribers to import.{/t}</p>

<div class="buttons">

<input type="submit" name="preview" value="{t}Click to Preview{/t}" />

</div>

</form>
{/if}

{include file="admin/inc.footer.tpl"}