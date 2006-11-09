{include file="admin/inc.header.tpl"}

<ul class="inpage_menu">

{if $table == 'subscribers'}
<li><a href="subscribers_manage.php?table=pending">{t}View Pending{/t}</a></li>
{else}
<li><a href="subscribers_manage.php?table=subscribers">{t}View Subscribed{/t}</a></li>
{/if}

<li><a href="subscribers_export.php?table={$table}&amp;group_id={$group_id}">{t}Export to CSV{/t}</a></li>
</ul>

<form method="post" action="" name="bForm" id="bForm">
<fieldset class="sorting">
<legend>{t}Sorting{/t}</legend>

<div>
<label for="group_id">{t}Belonging to Group:{/t}</label>
<select name="group_id" id="group_id" onChange="document.bForm.submit()">
<option value="all">{t}All Subscribers{/t}</option>
{foreach from=$groups key=key item=item}
<option value="{$key}"{if $group_id == $key} selected="selected"{/if}>{$item}</option>
{/foreach}
</select>
</div>

<div>
<label for="order">{t}Order by:{/t}</label>
<select name="order" id="order" onChange="document.bForm.submit()">
<option value="email">{t}email{/t}</option>
{foreach from=$fields key=key item=item}
<option value="{$key}"{if $order == $key} selected="selected"{/if}>{$item.name}</option>
{/foreach}
</select>
<select name="orderType" onChange="document.bForm.submit()">
<option value="ASC"{if $orderType == 'ASC'} selected="selected"{/if}>{t}ascending{/t}</option>
<option value="DESC"{if $orderType == 'DESC'} selected="selected"{/if}>{t}descending{/t}</option>
</select>
</div>

<div>
<label for="limit">{t}Subscribers per page:{/t}</label>
<select name="limit" id="limit" onChange="document.bForm.submit()">
<option value="10"{if $limit == '10'} selected="selected"{/if}>10</option>
<option value="50"{if $limit == '50'} selected="selected"{/if}>50</option>
<option value="150"{if $limit == '150'} selected="selected"{/if}>150</option>
<option value="300"{if $limit == '300'} selected="selected"{/if}>300</option>
<option value="500"{if $limit == '500'} selected="selected"{/if}>500</option>
</select>
</div>

</fieldset>
</form>

<form method="post" action="subscribers_mod.php" name="oForm" id="oForm">
<fieldset>
<legend>{t}Subscribers{/t}</legend>

<p class="count">({t 1=$groupCount}%1 subscribers{/t})</p>

<input type="hidden" name="order" value="{$order}" />
<input type="hidden" name="orderType" value="{$orderType}" />
<input type="hidden" name="limit" value="{$limit}" />
<input type="hidden" name="table" value="{$table}" />
<input type="hidden" name="group_id" value="{$group_id}" />

<table summary="subscriber details">
<thead>
<tr>

<th>{t}select{/t}</th>

<th>
{if $table == 'subscribers'}
{t}edit{/t}
{else}
{t}add{/t}
{/if}
</th>

<th>{t}delete{/t}</th>

<th>{t}email{/t}</th>

{foreach from=$fields key=key item=item}
<th>{$item.name}</th>
{/foreach}

</tr>
</thead>

<tbody>
{foreach name=sub from=$subscribers key=key item=item}
<tr>
<td class="multiple"><input type="checkbox" name="sid[]" value="{$item.email}" /></td>

<td>
{if $table == 'subscribers'}
<a href="subscribers_mod.php?sid={$item.email}&amp;action=edit&amp;table={$table}&amp;limit={$limit}&amp;order={$order}&amp;orderType={$orderType}&amp;group_id={$group_id}">{t}edit{/t}</a>
{else}
<a href="subscribers_mod.php?sid={$item.email}&amp;action=add&amp;table={$table}&amp;limit={$limit}&amp;order={$order}&amp;orderType={$orderType}&amp;group_id={$group_id}">{t}add{/t}</a>
{/if}
</td>

<td><a href="subscribers_mod.php?sid={$item.email}&amp;action=delete&amp;table={$table}&amp;limit={$limit}&amp;order={$order}&amp;orderType={$orderType}&amp;group_id={$group_id}">{t}delete{/t}</a></td>

<td>{$item.email}</td>

{foreach name=demo from=$fields key=demo_id item=demo}
<td>{$item.data.$demo_id}</td>
{/foreach}
<td>{$item.date}</td>
</tr>
{/foreach}

</tbody>
</table>

</fieldset>

<fieldset class="controls">
<legend>{t}Controls{/t}</legend>

<ul>
<li><strong><a href="javascript:SetChecked(1,'sid[]')">{t}Check All{/t}</a></strong></li>
<li><strong><a href="javascript:SetChecked(0,'sid[]')">{t}Clear All{/t}</a></strong></li>
</ul>

<select name="action">
<option value="" selected="selected">{t}Ignore{/t} {t}checked subscribers{/t}</option>
<option value="delete">{t}Delete{/t} {t}checked subscribers{/t}</option>
	{if $table == 'subscribers'}
<option value="edit">{t}Edit{/t} {t}checked subscribers{/t}</option>
	{else}
<option value="add">{t}Add{/t} {t}checked subscribers{/t}</option>
	{/if}
</select>

</fieldset>

<div class="buttons">

<input type="submit" name="send" value="{t}action{/t}" />

</div>

</form>

{$pagelist}

{literal}
<script type="text/javascript">
// <![CDATA[

/* The following code is to "check all/check none" NOTE: form name must properly be set */
var form = 'oForm' //Give the form name here
function SetChecked(val, chkName) {
	dml = document.forms[form];
	len = dml.elements.length;
	for (i = 0; i < len; i++) {
		if (dml.elements[i].name == chkName) {
			dml.elements[i].checked = val;
		}
	}
}
// ]]>
</script>
{/literal}

{include file="admin/inc.footer.tpl"}