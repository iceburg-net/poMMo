{capture name=head}{* used to inject content into the HTML <head> *}
{* Include in-place editing of subscriber table *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/quicksearch.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/tableEditor/sorter.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/tableEditor/editor.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/thickbox/thickbox.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/validate.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/form.js"></script>
<script type="text/javascript">{literal}
$().ready(function() {
	$('#orderForm select').change(function() {
		$('#orderForm')[0].submit();
	});
});
{/literal}</script>
{* Styling of subscriber table *}
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}js/tableEditor/style.css" />
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}js/thickbox/thickbox.css" />
{/capture}
{include file="admin/inc.header.tpl" sidebar='off'}

<form method="post" action="" id="orderForm">

<ul class="inpage_menu">
	<li>
	<!-- <a href="#TB_inline?height=400&width=500&inlineId=addSubs" title="{t}Add Subscribers{/t}" class="thickbox">{t}Add Subscribers{/t}</a> -->
	<a href="ajax/subscriber_add.php?height=400&width=500" title="{t}Add Subscribers{/t}" class="thickbox">{t}Add Subscribers{/t}</a>
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
	<option value="1" {if $state.status == 1}SELECTED{/if}>{t}Active Subscribers{/t}</option>
	<option value="1">------------------</option>
	<option value="0" {if $state.status == 0}SELECTED{/if}>{t}Unsubscribed{/t}</option>
	<option value="2" {if $state.status == 2}SELECTED{/if}>{t}Pending{/t}</option>
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
	<label for="order">{t}Order by{/t}</label>
	<select name="order">
	<option value="asc" {if $state.order == 'asc'}SELECTED{/if}>{t}ascending{/t}</option>
	<option value="desc" {if $state.order == 'desc'}SELECTED{/if}>{t}descending{/t}</option>
	</select>
	</li>
	
	<li>
	<label for="info">{t}Extended Info{/t}</label>
	<select name="info">
	<option value="show" {if $state.info == 'show'}SELECTED{/if}>{t}show{/t}</option>
	<option value="hide" {if $state.info == 'hide'}SELECTED{/if}>{t}hide{/t}</option>
	</select>
	</li>
	
</fieldset>
</form>

<p class="count">({t 1=$tally}%1 subscribers{/t})</p>

{if $tally > 0}
<table summary="subscriber details" id="subs">
<thead>
<tr>

<th name="key"></th>

<th name="email" class="pvV pvEmail pvEmpty">EMAIL</th>

{foreach from=$fields key=id item=f}
<th name="{$id}" class="pvV{if $f.required == 'on'} pvEmpty{/if}{if $f.type == 'number'} pvNumber{/if}{if $f.type == 'date'} pvDate{/if}">{$f.name}</th>
	{if $f.type == 'multiple'}
	<select style="display: none;" id="seM{$id}">{foreach name=inner from=$f.array item=option}<option value="{$option|escape}">{$option}</option>{/foreach}</select>
	{/if}
{/foreach}

{if $state.info == 'show'}
<th name="registered" class="noEdit">Registered</th>
<th name="touched" class="noEdit">Updated</th>
<th name="ip" class="noEdit">IP Address</th>
{/if}

</tr>
</thead>

<tbody>

{foreach from=$subscribers key=sid item=s}
<tr>
<td>
{* edit button -- this switches to {$url.theme.shared}images/icons/yes.png when clicked *}
<button class="edit"><img src="{$url.theme.shared}images/icons/edit.png"></button>

<p class="key">{$sid}</p>
</td>

<td>{$s.email}</td>

{foreach name=inner from=$fields key=fid item=f}
{if $fields[$fid].type == 'checkbox'}
<td><input type="checkbox" disabled {if $s.data[$fid] == 'on'}checked{/if}/></td>
{elseif $fields[$fid].type == 'multiple'}
<td class="seMultiple" rel="seM{$fid}">{$s.data[$fid]}</td>{* Add class multiple+field ID so editable column is converted to a select input in pre_edit function *}
{elseif $fields[$fid].type == 'date'}
<td>{$s.data[$fid]|date_format:"%m/%d/%Y"}</td>
{else}
<td>{$s.data[$fid]}</td>
{/if}
{/foreach}

{if $state.info == 'show'}
<td>{$s.registered}</td>
<td>{$s.touched}</td>
<td>{$s.ip}</td>
{/if}

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
	}).tableEditor({
		SAVE_HTML: '<img src="{/literal}{$url.theme.shared}images/icons/yes.png{literal}">',
		EDIT_HTML: '<img src="{/literal}{$url.theme.shared}images/icons/edit.png{literal}">',
		EVENT_LINK_SELECTOR: 'button.edit',
		COL_APPLYCLASS: true,
		ROW_KEY_SELECTOR: 'p.key',
		FUNC_PRE_EDIT: 'preEdit',
		FUNC_POST_EDIT: 'postEdit',
		FUNC_UPDATE: 'updateTable'
	});
	
	$('table#subs tbody tr').quicksearch({
		attached: "#subs",
		position: "before",
		lavelClass: "quicksearch",
		stripeRowClass: ['r1', 'r2', 'r3'],
		labelText: "{/literal}{t}Quick Search{/t}{literal}",
		inputText: "{/literal}{t}search table{/t}{literal}",
		loaderImg: '{/literal}{$url.theme.shared}images/loader.gif{literal}'
	});
});

// convert multiple choice fields to their appropriate select
function preEdit(o) { 
	o.row.each(function() {
		if ($(this).is(".seMultiple")) {
			var o = $('#'+$(this).attr("rel"));
			var select = $('#'+$(this).attr("rel")).clone();
			select.removeAttr('id'); // remove the id=seM<num>
			select.val($(this).html()).show(); // set value of select, unhide
			$(this).html('').append(select); // replace cell with select
		}
	});
}

// inject validation
function postEdit(o) {
	PommoValidate.reset(); // TODO -- validate must be scoped to this ROW. Modify validate.js
	PommoValidate.init('input.pvV, select.pvV','../td button.edit', true, o.row);
	
	// remove the preserve class [ added by tableEditor makeEditable() method ]
	o.row.each(function() {
		if ($(this).is(".seMultiple"))
			$(this).find("select").removeClass("tsPreserve");
	});
}


function updateTable(o) {
	// check if changed is empty
	var empty = true;
	for (key in o.changed) {
		if (o.changed.hasOwnProperty(key)) {
			empty = false; break;
		}
	}
	if (empty)
		return;

	$.post("ajax/subscriber_update.php?key="+o.key, o.changed, function(json) {
		eval("var args = " + json);
		if (typeof(args.success) == 'undefined')
			alert('ajax error!');
			
		if (!args.success) {
			alert(args.msg);
			// restore row
			$.tableEditor.lib.restoreRow(o.row,o.original);
		}
	});
}
</script>
{/literal}
{/if}

{include file="admin/inc.footer.tpl"}