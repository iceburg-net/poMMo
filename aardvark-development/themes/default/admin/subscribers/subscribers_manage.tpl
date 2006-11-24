{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
{/capture}
{include file="admin/inc.header.tpl" sidebar='off'}

<form method="post" action="" id="orderForm">

<ul class="inpage_menu">
	<li>
	<a href="AJAX">{t}Add Subscribers{/t}</a>
	</li>
	
	<li>
	<a href="AJAX">{t}Remove Subscribers{/t}</a>
	</li>
	
	<li>
	<a href="AJAX">{t}Search Subscribers{/t}</a>
	</li>
	
	<li>
	<a href="subscribers_export.php?status={$state.status}&amp;group={$state.group}">{t}Export to CSV{/t}</a>
	</li>
</ul>

<fieldset class="sorting">
	<legend>{t}View{/t}</legend>
	
	<div class="inpage_menu">
	
	<li>
	<label for="status">{t}View{/t}</label>
	<select name="status">
	<option value="active" {if $state.status == 'active'}SELECTED{/if}>{t}Active Subscribers{/t}</option>
	<option value="active">------------------</option>
	<option value="inactive" {if $state.status == 'inactive'}SELECTED{/if}>{t}Unsubscribed{/t}</option>
	<option value="pending" {if $state.status == 'pending'}SELECTED{/if}>{t}Pending{/t}</option>
	</select>
	</li>
	
	<li>
	<label for="group">{t}Belonging to Group{/t}</label>
	<select name="group">
	<option value="all" {if $state.group == 'all'}SELECTED{/if}>{t}All Subscribers{/t}</option>
	<option value="all">---------------</option>
	{foreach from=$groups key=id item=g}
	<option value="{$id}" {if $state.group == $id}SELECTED{/if}>{$g.name}</option>
	{/foreach}
	</select>
	</li>
	
	<li>
	<label for="limit">{t}# per page{/t}</label>
	<select name="limit">
	<option value="10" {if $state.limit == '10'}SELECTED{/if}>10</option>
	<option value="50" {if $state.limit == '50'}SELECTED{/if}>50</option>
	<option value="150" {if $state.limit == '150'}SELECTED{/if}>150</option>
	<option value="300" {if $state.limit == '300'}SELECTED{/if}>300</option>
	<option value="500" {if $state.limit == '500'}SELECTED{/if}>500</option>
	</select>
	</li>
	
	</div>
</fieldset>

<fieldset class="sorting">
	<legend>{t}Sorting{/t}</legend>
	
	<div class="inpage_menu">
	
	<li>
	<label for="sort">{t}Sort by{/t}</label>
	<select name="sort">
	<option value="email" {if $state.sort == 'email'}SELECTED{/if}>{t}email{/t}</option>
	<option value="time_registered" {if $state.sort == 'time_registered'}SELECTED{/if}>{t}time registered{/t}</option>
	<option value="time_touched" {if $state.sort == 'time_touched'}SELECTED{/if}>{t}time last updated{/t}</option>
	<option value="ip" {if $state.sort == 'ip'}SELECTED{/if}>{t}IP Address{/t}</option>
	</select>
	</li>
	
	<li>
	<label for="sort">{t}Order by{/t}</label>
	<select name="order">
	<option value="asc" {if $state.order == 'asc'}SELECTED{/if}>{t}ascending{/t}</option>
	<option value="desc" {if $state.order == 'desc'}SELECTED{/if}>{t}descending{/t}</option>
	</select>
	</li>
	
	<li>
	<label for "search">{t}Quick Search:{/t}</label>
	<input type="text" name="search">
	</li>
	
</fieldset>
</form>


<fieldset>
<legend>{t}Subscribers{/t}</legend>

<p class="count">({t 1=$tally}%1 subscribers{/t})</p>


<table summary="subscriber details">
<thead>
<tr>

<th>EDIT/DEL</th>

<th>EMAIL</th>

{foreach from=$fields key=id item=f}
<th>{$f.name}</th>
{/foreach}

<th>Registered</th>
<th>Updated</th>
<th>IP Address</th>

</tr>
</thead>

<tbody>

{foreach from=$subscribers key=sid item=s}
<tr>

<td>
edit/del
</td>

<td>{$s.email}</td>

{foreach name=field from=$fields key=fid item=f}
<td>{$s.data[$fid]}</td>
{/foreach}

<td>{$s.registered}</td>
<td>{$s.touched}</td>
<td>{$s.ip}</td>
</tr>
{/foreach}

</tbody>
</table>

</fieldset>

{$pagelist}

{literal}
<script type="text/javascript">
$().ready(function() {
	$('#orderForm select').change(function() {
		$('#orderForm')[0].submit();
	});
});
</script>
{/literal}

{include file="admin/inc.footer.tpl"}