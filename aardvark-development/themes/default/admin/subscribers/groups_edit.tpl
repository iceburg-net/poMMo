{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/interface.js"></script>
{literal}
<script type="text/javascript">
$().ready(function(){ 

	$('#filterWindow a.fwClose').click(function() {
		var name = $(this).attr('alt');
		$('#filterWindow').fadeOut(200, function(){$(this).TransferTo({to: name,className:'transferer1', duration: 300})});
		return false;
	});
					
	$('#newFilter select').change(function() {
		var name = $(this).name();
		var id = $(this).val();
		if(id != '') {
			$('#filterWindow div.fwContent').load(
				'ajax_group.php',
				{name: id},
				function() {
					$('#filterWindow a.fwClose').attr("alt",name);
					$('#'+name).TransferTo({to:'filterWindow',className:'transferer1', duration: 300, complete:function(to){$(to).fadeIn(200)}});
				}
			);
		}
	});
});
</script>

<style>
#newFilter
{
	position: relative;
}
#filterWindow
{
	position: absolute;
    top: -100px;
	
	background-color: #B5EF59;
	border: 10px solid #6CAF00;
	padding: 10px;
	
	text-align: left;
	display: none;
}

.transferer1
{
	border: 1px solid #000;
}
.transferer2
{
	border: 1px solid #BBEF68;
	background-color: #ffc;
}
.transferer3
{
	border-top: 2px solid #6CAF00;
	border-bottom: 2px solid #6CAF00;
	border-left: 5px dashed #6CAF00;
	border-right: 5px dashed #6CAF00;
}

</style>
{/literal}
{/capture}
{include file="admin/inc.header.tpl"}{debug}

<ul class="inpage_menu">
<li><a href="{$url.base}admin/subscribers/subscribers_groups.php">{t 1=$returnStr}Return to %1{/t}</a></li>
</ul>

<h2>{t}Edit Group{/t}</h2>

<p><img src="{$url.theme.shared}images/icons/groups.png" alt="groups icon" class="articleimg" />{t}Groups allow you to mail subsets of your subscribers -- for instance, those who have checked "volunteer". They are made up of "filters" that match values to subscriber fields. You can also include or exclude members from other groups. For example, if you collect "age" and "country", you can match those who are 21 or over and living in Japan by creating two filtering critiera; one which matches "age" to a value GREATER THAN 20, and another which matches "country" TO "Japan"{/t}.</p>

{include file="admin/inc.messages.tpl"}

<form method="post" action="" id="nameForm" name="nameForm">
<fieldset>
<legend>{t}Change Name{/t}</legend>

<div>
<label for="group_name">{t}Group name:{/t}</label> <input type="text" title="{t}type new group name{/t}" maxlength="60" size="30" name="group_name" id="group_name"  value="{$group.name|escape}" />
</div>

<div class="buttons">
<input type="submit" name="rename" value="{t}Rename{/t}" />
</div>

</fieldset>
</form>

<form method="post" action="" id="filterForm" name="filterForm">
<fieldset>
<legend>{t}Add Filter{/t}</legend>



<div id="newFilter">

{* filterWindow popup *}
<div id="filterWindow">
<a href="#" class="fwClose" alt="field_id">Close window</a>
<div class="fwContent"></div>
</div>

<div>
<label for="field_id">{t escape=no}Filter subscribers by;{/t}</label>
<select name="field_id" id="field_id">
<option value="">-- {t}Choose subscriber field{/t} --</option>
{foreach from=$criteria key=id item=logic}
<option value="{$id}">{$fields[$id].name}</option>
{/foreach}
</select>
</div>

<div>
<label for="group_id">{t escape=no}Or, by group; {/t}</label>
<select name="group_id" id="group_id">
<option value="">-- {t}Choose group to include/exclude{/t} --</option>
{foreach from=$groups key=id item=group}
<option value="{$id}">{$group.name}</option>
{/foreach}
</select>
</div>

</div>
</fieldset>

<fieldset>
<legend>{t}Group Filters{/t}</legend>

<div>
list..
</div>
</fieldset>

</form>

<p>{t escape=no 1="<em>`$filterCount`</em>" 2="<strong>`$tally`</strong>"}%1 filters match a total of %2 active subscribers{/t}</p>


{include file="admin/inc.footer.tpl"}