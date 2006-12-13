{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/quicksearch.js"></script>
{* Styling of table *}
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}js/tableEditor/style.css" />
{/capture}
{include file="admin/inc.header.tpl" sidebar='off'}

<ul class="inpage_menu">
<li><a href="subscribers_import.php">{t 1=$returnStr}Return to %1{/t}</a></li>
</ul>

<div id="mainbar">

<h2>{t}Import Subscribers{/t}</h2>

<fieldset>
<legend>{t}Assign Fields{/t}</legend>

{* Encasing in a <p> causes the table not to display in FF 2.0 !!?!?! *}
<p>
{t}Below is a preview of your CSV data. You can assign subscriber fields to columns. At the very least, you must assign an email address.{/t}
</p>

<form action="" method="post" id="assign">
<table summary="{t}Assign Fields{/t}" id="subs">

<thead>
<tr>

{section name=columns start=0 loop=$colNum }
<th>
&nbsp;<select name="col{$smarty.section.columns.index}">
<option value="">{t}Ignore Column{/t}</option>
<option value="email">{t}Email{/t}</option>
<option value="">-----------</option>
{foreach from=$fields item=f key=id}
<option value="{$id}">{$f.name}</option>
{/foreach}
</select>&nbsp;
</th>
{/section}

</tr>
</thead>

{foreach from=$preview item=row}
<tr>
{section name=rows start=0 loop=$colNum }
<td>{if $row[$smarty.section.rows.index]}{$row[$smarty.section.rows.index]}{/if}</td>
{/section}
</tr>

{/foreach}

</table>
</form>


<div class="buttons" id="buttons">
<a href="#" id="import"><button>{t}Import{/t}</button></a>
</div>
</fieldset>

<div id="ajax" class="warn hidden">
<img src="{$url.theme.shared}images/loader.gif" alt="Importing..." />... {t}Processing{/t}
</div>

</div>
<!-- end mainbar -->

{literal}
<script type="text/javascript">
$().ready(function(){
	
	$('#subs tbody tr').quicksearch({
		attached: "#subs",
		position: "before",
		labelClass: "quicksearch",
		stripeRowClass: ['r1', 'r2', 'r3'],
		labelText: "{/literal}{t}Quick Search{/t}{literal}",
		inputText: "{/literal}{t}search table{/t}{literal}",
		loaderImg: '{/literal}{$url.theme.shared}images/loader.gif{literal}'
	});	
	
	$('#import').click(function() {
		
		var c = false;
		$('select').each(function() {
			if(this.value == 'email')
				c = true;
		});
		
		if(!c) {
			alert('{/literal}{t}You must assign an email column!{/t}{literal}');
			return false;
		}
		
		return false;
	
	});
});
</script>
{/literal}
{include file="admin/inc.footer.tpl"}