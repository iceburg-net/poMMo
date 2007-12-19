{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/grid.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/jqModal.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/form.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/validate.js"></script>

<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/jqgrid.css" />
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/modal.css" />
{/capture}


{include file="inc/admin.header.tpl" sidebar='off'}

{include file="inc/messages.tpl"}

<ul class="inpage_menu">
<li><a href="ajax/subscriber_add.php" title="{t}Add Subscribers{/t}" class="addTrigger">{t}Add Subscribers{/t}</a></li>

<li><a href="ajax/subscriber_del.php" title="{t}Remove Subscribers{/t}" class="delTrigger">{t}Remove Subscribers{/t}</a></li>

<li><a href="ajax/subscriber_export.php" title="{t}Export Subscribers{/t}" class="expTrigger">{t}Export Subscribers{/t}</a></li>

<li><a href="admin_subscribers.php" title="{t}Return to Subscribers Page{/t}">{t}Return to Subscribers Page{/t}</a></li>
</ul>

<form method="post" action="" id="orderForm">

	<fieldset class="click">
	<legend class="click">{t}View{/t} &raquo;</legend>
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
	<legend class="click">{t}Search{/t} &raquo;</legend>
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

<a href="ajax/subscriber_del.php" class="delTrigger"><img src="{$url.theme.shared}images/icons/delete.png" alt="{t}Delete{/t}" />{t}Delete Checked Subscribers{/t}</a>
<a href="ajax/subscriber_edit.php" class="editTrigger"><img src="{$url.theme.shared}images/icons/edit.png" alt="{t}Edit{/t}" />{t}Edit Checked{/t}</a>

<script type="text/javascript">
$().ready(function() {ldelim}	
	
	var loadText = "{t}Processing{/t}...";
	
	var recordText = "{if empty($state.search)}{t}Subscriber(s){/t}{else}{t}Match(es){/t}{/if}";
	
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
		{ldelim}name: 'id', index: 'id', hidden: true, width: 1{rdelim},
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
		recordtext: recordText,
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
	
	$('#add, #del, #exp, #edit').jqm({
		modal: true,
		ajax: '@href',
		target: '.jqmdMSG',
		trigger: false
	}).jqDrag('div.jqmdTC');
	
	
	$('#add').jqmAddTrigger('a.addTrigger');
	$('#del').jqmAddTrigger('a.delTrigger');
	$('#exp').jqmAddTrigger('a.expTrigger');
	
	
	$('a.editTrigger').click(function(){
		// prevent edit window from appearing if no row is selected
		if($('#grid').getSelectedRow() !== null)
			$('#edit').jqmShow(this);
		return false;
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


{capture name=dialogs}
{include file="inc/dialog.tpl" dialogID="add" dialogTitle=$testTitle dialogDrag=true dialogClass="jqmdWide" dialogBodyClass="jqmdTall"}
{include file="inc/dialog.tpl" dialogID="edit" dialogTitle=$testTitle dialogDrag=true dialogClass="jqmdWide" dialogBodyClass="jqmdTall"}
{include file="inc/dialog.tpl" dialogID="del" dialogTitle=$testTitle dialogBodyClass="jqmdTall"}
{include file="inc/dialog.tpl" dialogID="exp" dialogTitle=$testTitle dialogBodyClass="jqmdTall"}
{/capture}

{include file="inc/admin.footer.tpl"}