{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/interface.js"></script>
{literal}
<script type="text/javascript">
$().ready(function(){ 

	$('#filterWindow a.fwClose').click(function() {
		var name = $(this).attr('alt');
		$('#newFilter select').each(function() { $(this).show().val(''); });
		$('#filterWindow').fadeOut(200, function(){$(this).TransferTo({to: name,className:'fwTransfer', duration: 300})});
		return false;
	});
					
	$('#newFilter select').change(function() {
		var name = $(this).name();
		var id = $(this).val();
		var gid = $(this).attr("alt");
		if(id != '') {
			$('#newFilter select').each(function() { $(this).hide(); });
			
			$('#filterWindow div.fwContent').load(
				'ajax_group.php',
				{ID: id, add: name, group: gid},
				function() {
					$('#filterWindow a.fwClose').attr("alt",name);
					$('#'+name).TransferTo({to:'filterWindow',className:'fwTransfer', duration: 300, complete:function(to){$(to).fadeIn(200)}});
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
    top: -200px;
	left: -50px;
	
	background-color: #B5EF59;
	border: 10px solid #6CAF00;
	padding: 10px;
	
	z-index: 100;
	text-align: left;
	display: none;
}

.fwTransfer
{
	border-top: 2px solid #6CAF00;
	border-bottom: 2px solid #6CAF00;
	border-left: 5px dashed #6CAF00;
	border-right: 5px dashed #6CAF00;
}

</style>
{/literal}
{/capture}
{include file="admin/inc.header.tpl"}

<ul class="inpage_menu">
<li><a href="{$url.base}admin/subscribers/subscribers_groups.php">{t 1=$returnStr}Return to %1{/t}</a></li>
</ul>

<h2>{t}Edit Group{/t}</h2>

<p><img src="{$url.theme.shared}images/icons/groups.png" alt="groups icon" class="articleimg" />{t}Groups allow you to mail subsets of your subscribers. They are made up of "filters" that match values to subscriber fields. Groups can also match (include) or not match (exclude) members from other groups. For example, if you collect "age" and "country", you can match those who are 21+ and living in Japan by creating two filtering critiera; one which matches "age" to a value GREATER THAN 20, and another which matches "Japan" IS "country".{/t}</p>

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
<label for="field">{t escape=no 1="<strong><a href=\"`$url.base`admin/setup/setup_fields.php\">" 2="</a></strong>"}Select a %1 field %2 to filter{/t}</label>
<select name="field" id="field" alt="{$group.id}">
<option value="">-- {t}Choose subscriber field{/t} --</option>
{foreach from=$filters key=id item=name}
<option value="{$id}">{$name}</option>
{/foreach}
</select>
</div>

<div>
<label for="group">{t escape=no 1="<strong><a href=\"`$url.base`admin/setup/setup_fields.php\">" 2="</a></strong>"}or, Select a %1 group %2 to{/t}</label>
<select name="group" id="group" alt="{$group.id}">
<option value="">-- {t}include or exclude{/t} --</option>
{foreach from=$gfilters key=id item=name}
<option value="{$id}">{$name}</option>
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