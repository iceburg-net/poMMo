{capture name=head}{* used to inject content into the HTML <head> *}
{* Include in-place editing of subscriber table *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/quicksearch.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/tableEditor/sorter.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/thickbox/thickbox.js"></script>
<script type="text/javascript">{literal}
$().ready(function() {
	$('#orderForm select').change(function() {
		$('#orderForm')[0].submit();
	});
});
{/literal}</script>
{* Styling of table *}
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}js/tableEditor/style.css" />
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}js/thickbox/thickbox.css" />
{/capture}
{include file="admin/inc.header.tpl" sidebar='off'}

<h2>{t}Mailings History{/t}</h2>
<div class="inpage_menu">
<li>
<a href="admin_mailings.php">{t 1=$returnStr}Return to %1{/t}</a>
</li>
</div>

{include file="admin/inc.messages.tpl"}

<form method="post" action="" id="orderForm">
<fieldset class="sorting">
	<legend>{t}Sorting{/t}</legend>
	
	<div class="inpage_menu">
	
	<li>
	<label for="sort">{t}Sort by{/t}</label>
	<select name="sort">
	<option value="subject" {if $state.sort == 'subject'}SELECTED{/if}>{t}Subject{/t}</option>
	<option value="mailgroup" {if $state.sort == 'mailgroup'}SELECTED{/if}>{t}Group{/t}</option>
	<option value="subscriberCount" {if $state.sort == 'subscriberCount'}SELECTED{/if}>{t}Subscriber Count{/t}</option>
	<option value="started" {if $state.sort == 'started'}SELECTED{/if}>{t}Time Created{/t}</option>
	</select>
	</li>
	
	<li>
	<label for="order">{t}Order by{/t}</label>
	<select name="order">
	<option value="asc" {if $state.order == 'asc'}SELECTED{/if}>{t}ascending{/t}</option>
	<option value="desc" {if $state.order == 'desc'}SELECTED{/if}>{t}descending{/t}</option>
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
	
</fieldset>
</form>




<form method="post" action="mailings_mod.php" name="oForm" id="oForm">
<fieldset>
<legend>{t}Mailings{/t}</legend>

<p class="count">({t 1=$tally}%1 mailings{/t})</p>

{if $tally > 0}
<table summary="mailing details" id="subs">
<thead>
<tr>

<th name="key"></th>

<th>{t}Subject{/t}</th>
<th>{t}Group (count){/t}</th>
<th>{t}Sent{/t}</th>
<th>{t}Started{/t}</th>
<th>{t}Finished{/t}</th>
<th>{t}Status{/t}</th>

</tr>
</thead>

<tbody>

{foreach from=$mailings key=id item=o}
<tr>
<td>
<p class="key">{$id}</p>
DELETE 
<br/>
<a href="ajax/mailing_preview.php?mail_id={$id}&height=320&width=480" title="{t}Message Preview{/t}" class="thickbox">{t}View{/t}</a>
<br/>
<a href="ajax/mailing_reload.php?mail_id={$id}" title="{t}Reload Mailing{/t}">{t}Reload{/t}</a>
</td>

<td>{$o.subject}</td>
<td>{$o.group} ({$o.sent})</td>
<td>{$o.tally}</td>
<td>{$o.start}</td>
<td>{$o.end}</td>
<td>
{if $o.status == 0}
	{t}Complete{/t}
{elseif $o.status == 1}
	{t}Processing{/t}
{else}
	{t}Cancelled{/t}
{/if}
</td>
</tr>
{/foreach}

</tbody>
</table>

<div>
{$pagelist}
</div>

{literal}
<script type="text/javascript">
$().ready(function() {	
	$("#subs").tableSorter({
		sortClassAsc: 'headerSortUp', 		// class name for ascending sorting action to header
		sortClassDesc: 'headerSortDown',	// class name for descending sorting action to header
		headerClass: 'header', 				// class name for headers (th's)
		disableHeader: 0					// DISABLE Sorting on edit/delete column
	});
	
	$('#subs tbody tr').quicksearch({
		attached: "#subs",
		position: "before",
		lavelClass: "quicksearch",
		stripeRowClass: ['r1', 'r2', 'r3'],
		labelText: "{/literal}{t}Quick Search{/t}{literal}",
		inputText: "{/literal}{t}search table{/t}{literal}",
		loaderImg: '{/literal}{$url.theme.shared}images/loader.gif{literal}'
	});	
});
</script>
{/literal}
{/if}

{include file="admin/inc.footer.tpl"}