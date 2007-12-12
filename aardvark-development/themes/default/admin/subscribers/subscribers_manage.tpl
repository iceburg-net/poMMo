{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/grid.js"></script>

<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/jqgrid.css" />

<!-- REMOVED....
<script type="text/javascript" src="{$url.theme.shared}js/jq/form.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/tableEditor/sorter.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/tableEditor/editor.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/thickbox/thickbox.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/validate.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/table.js"></script>
-->
{/capture}


{include file="inc/admin.header.tpl" sidebar='off'}

{include file="inc/messages.tpl"}

<ul class="inpage_menu">
<li><a href="ajax/subscriber_add.php?height=400&amp;width=500" title="{t}Add Subscribers{/t}" class="thickbox">{t}Add Subscribers{/t}</a></li>

<li><a href="ajax/subscriber_export.php?height=400&amp;width=500" title="{t}Export Subscribers{/t}" class="thickbox">{t}Export Subscribers{/t}</a></li>

<li><a href="admin_subscribers.php" title="{t}Return to Subscribers Page{/t}">{t}Return to Subscribers Page{/t}</a></li>
</ul>

<form method="post" action="" id="orderForm">

	<fieldset class="click">
	<legend class="click">{t}View{/t}</legend>
	<ul class="inpage_menu view">
	
		<li>
		<label>{t}View{/t} 
		<select name="status">
		<option value="1"{if $state.status == 1} selected="selected"{/if}>{t}Active Subscribers{/t}</option>
		<option value="1">------------------</option>
		<option value="0"{if $state.status == 0} selected="selected"{/if}>{t}Unsubscribed{/t}</option>
		<option value="2"{if $state.status == 2} selected="selected"{/if}>{t}Pending{/t}</option>
		</select></label>
		</li>
		
		<li>
		<label>{t}Belonging to Group{/t} 
		<select name="group">
		<option value="all"{if $state.group == 'all'} selected="selected"{/if}>{t}All Subscribers{/t}</option>
		<option value="all">---------------</option>
		{foreach from=$groups key=id item=g}
		<option value="{$id}"{if $state.group == $id} selected="selected"{/if}>{$g.name}</option>
		{/foreach}
		</select></label>
		</li>
		
	</ul>
	</fieldset>
</form>

<form method="post" action="" id="searchForm">
	
	<fieldset class="click">
	<legend class="click">{t}Search{/t}</legend>
	<ul class="inpage_menu search">
	
		<li>
		<label>{t}Find Subscribers where{/t}
		<select name="searchField">
		<option value="email"{if $state.search.field == 'email'} selected="selected"{/if}>{t}email{/t}</option>
		{foreach from=$fields key=id item=f}
		<option value="{$id}"{if $state.search.field == $id} selected="select"{/if}>{$f.name}</option>
		{/foreach}
		<option value="time_registered"{if $state.search.field == 'time_registered'} selected="selected"{/if}>{t}time registered{/t}</option>
		<option value="time_touched"{if $state.search.field == 'time_touched'} selected="selected"{/if}>{t}time last updated{/t}</option>
		<option value="ip"{if $state.search.field == 'ip'} selected="selected"{/if}>{t}IP Address{/t}</option>
		</select>
		</label>
		</li>
	</ul>
	
	<ul class="inpage_menu search">
		<li>
		<label>{t}is like{/t}
		<input type="text" name="searchString" value="{$state.search.string|escape}" />
		</label>
		</li>
		
		<li>
		<input type="submit" name="submit" value="{t}Search{/t}" />
		</li>
		
		{if !empty($state.search)}
		<li>
		<input type="submit" name="searchClear" value="{t}Reset{/t}" />
		</li>
		{/if}
		
	</ul>
	</fieldset>
</form>

{if $tally < 1}

{t}No subscribers found.{/t}

{else}

<table id="grid" class="scroll" cellpadding="0" cellspacing="0"></table>
<div id="gridPager" class="scroll" style="text-align:center;"></div>

<script type="text/javascript">
$().ready(function() {ldelim}	
	
	var loadText = "{t}Processing{/t}...";
	
	var imgPath = "{$url.theme.shared}/images/grid";
	
	var colNames = [
		'ID',
		'Email',
		{foreach from=$fields key=id item=f}'{$f.name|escape}',{/foreach}
		'{t}Registered{/t}',
		'{t}Updated{/t}',
		'{t}IP Address{/t}',
	];
	
	var limit = {$state.limit};
	
	var colModel = [
		{ldelim}name: 'id', index: 'id', key: true, hidden: true, width: 1{rdelim},
		{ldelim}name: 'email', width: 150{rdelim},
		{foreach from=$fields key=id item=f}{ldelim}name: 'd{$id}', width: 120{rdelim},{/foreach}
		{literal}{name: 'registered', width: 130},
		{name: 'touched', width: 130},
		{name: 'ip', width: 90}
	];
	
	$('#grid').jqGrid({
		url:'ajax/subscriber_list.php',
		datatype: 'json',
		colNames: colNames, 
		colModel: colModel,
		rowNum: limit,
		pager: jQuery('#gridPager'),
		imgpath: imgPath, 
		viewrecords: true,
		loadtext: loadText,
		multiselect: true,
		height: 270,
		width: 670,
		shrinkToFit: false,
		jsonReader: {repeatitems: false}
	});
});
</script>
{/literal}
{/if}

{literal}
<script type="text/javascript">
$().ready(function() {
	$('#orderForm select').change(function() {
		$('#orderForm')[0].submit();
	});
	
	$('legend.click').click(function(){ 
		$(this).siblings('ul').slideToggle(); 
	});	
});
</script>
{/literal}

{if !empty($state.search)}
{literal}
<script type="text/javascript">
$().ready(function() {
	$('ul.search').slideDown();
});
</script>
{/literal}
{/if}

{if $state.group != 'all' || $state.status != 1}
{literal}
<script type="text/javascript">
$().ready(function() {
	$('ul.view').slideDown('slow');
});
</script>
{/literal}
{/if}

{include file="inc/admin.footer.tpl"}