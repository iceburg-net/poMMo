{capture name=head}{* used to inject content into the HTML <head> *}
{* Include in-place editing of subscriber table *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/tableEditor/sorter.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/tableEditor/editor.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/validate.js"></script>

{* Styling of subscriber table *}
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}js/tableEditor/style.css" />
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
	<label for="search">{t}Quick Search:{/t}</label>
	<input type="text" name="search">
	</li>
	
</fieldset>
</form>


<fieldset>
<legend>{t}Subscribers{/t}</legend>

<p class="count">({t 1=$tally}%1 subscribers{/t})</p>

<table summary="subscriber details" id="subs">
<thead>
<tr>

<th name="key">{t}EDIT{/t}</th>

<th name="email" class="pvV pvEmail">EMAIL</th>

{foreach from=$fields key=id item=f}
<th name="{$id}" class="pvV{if $f.required == 'on'} pvEmpty{/if}{if $f.type == 'number'} pvNumber{/if}{if $f.type == 'date'} pvDate{/if}">{$f.name}</th>
	{if $f.type == 'multiple'}
	<select style="display: none;" id="seM{$id}">{foreach name=inner from=$f.array item=option}<option>{$option}</option>{/foreach}</select>
	{/if}
{/foreach}

<th name="registered" class="noEdit">Registered</th>
<th name="touched" class="noEdit">Updated</th>
<th name="ip" class="noEdit">IP Address</th>

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
{else}
<td>{$s.data[$fid]}</td>
{/if}
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

common = {
	// cleanly prints an array/object for the alert(). TODO; REMOVE -- ONLY FOR DEMO.
	dump: function (arr,level) {
		var dumped_text = "";
		if(!level) level = 0;
		
		//The padding given at the beginning of the line.
		var level_padding = "";
		for(var j=0;j<level+1;j++) level_padding += "    ";
		
		if(typeof(arr) == 'object') { //Array/Hashes/Objects
		 for(var item in arr) {
		  var value = arr[item];
		 
		  if(typeof(value) == 'object') { //If it is an array,
		   dumped_text += level_padding + "'" + item + "' ...\n";
		   dumped_text += dump(value,level+1);
		  } else {
		   dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
		  }
		 }
		} else { //Stings/Chars/Numbers etc.
		 dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
		}
		return dumped_text;
	},
	
	sanitize: function(str) {
		if (typeof str != 'string')
			return '';
		str = str.replace(/[^a-zA-Z 0-9]+/g,'');
		if (str.length > 10)
			str = str.substr(0,9);
		return str;
	},
		
	serialize: function(o) { 
		var a = [];
		o.find('input, textarea').each(function() {
			var n = this.name || this.id;
		   var t = this.type;
		   
		   if ( (t == 'checkbox') && !this.checked )
		   	return;
		  
		   a.push({name: n, value: this.value});
		   	
		}).end();
		return a;
	},
	
	trim: function(str) {
		return str.replace(/^\s*|\s*$/g,"");
	}
};

$().ready(function() {
	$('#orderForm select').change(function() {
		$('#orderForm')[0].submit();
	});
	
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
	// add validation & non empty validator to row's input cells
	o.row.each(function() {
		var classes = $(this).attr('class');
		$(this).find('select, input').addClass(classes).end();
	});
	PommoValidate.reset(); // TODO -- validate must be scoped to this ROW. Modify validate.js
	PommoValidate.init('input.pvV, select.pvV','../td button.edit', true, o.row);
}


function updateTable(o) {
alert("Update function called!\n === Debug of Passed Object === \n"+
	"o.row: jQ Object of size: "+o.row.size()+"\n"+
	"o.key: "+common.dump(o.key)+"\n"+
	"o.changed: "+common.dump(o.changed)+"\n"+
	"o.original: "+common.dump(o.original));
}
</script>
{/literal}

{include file="admin/inc.footer.tpl"}