<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>{$title}</title>
	<link href="{$url.theme.this}/inc/admin.css" type="text/css" rel="STYLESHEET">
	<link href="/pommo/aardvark-corinna/themes/default/inc/admin.css" type="text/css" rel="STYLESHEET">
	{$smarty.capture.head}
	{if $isForm}
	<link href="{$url.theme.shared}/css/bform.css" type="text/css" rel="STYLESHEET">
	<link href="/pommo/aardvark-corinna/themes/shared/css/bform.css" type="text/css" rel="STYLESHEET">
	{/if}
</head>
<body>
<a name="top" id="top"></a>
<center>
<div id="menu">
	<a href="/pommo/aardvark-corinna/index.php?logout=TRUE">{t}Logout{/t}</a>
	<a href="/pommo/aardvark-corinna/admin/admin.php">{t}Admin Page{/t}</a>
	<a href="{$config.site_url}">{$config.site_name}</a>
	<!--<a href="{$url.base}/index.php?logout=TRUE">{t}Logout{/t}</a>
	<a href="{$url.base}/admin/admin.php">{t}Admin Page{/t}</a>
	<a href="{$config.site_url}">{$config.site_name}</a>-->
</div>
{if $header}
<div id="header">
	<h1>{$header.main}</h1>
	<h2>{$header.sub}</h2>
</div>
{/if}
<div id="content">



{* *******************************************
		//TODO
		If i put in this the $url.theme.this (and base&&share also) is always 
		/pommo/aardvark-corinna/pluginadmin/themes/default
		Why the pluginadmin dir is before /themes/default
{include file="admin/inc.header.tpl"}*}


<h1>{$returnStr}</h1>

<a class="pommoClose" href="plugins.php" style="float: left;">
	<!--<img src="{$url.theme.shared}/images/icons/left.png" align="absmiddle" border="0">{t}Go Back{/t}-->
	<img src="/pommo/aardvark-corinna/themes/shared/images/icons/left.png" align="absmiddle" border="0"> {t}Go Back{/t}
</a><div style="clear: both; "></div>
<br>

{if $messages}
	<div class="msgdisplay">
		{foreach from=$messages item=msg}
			<div>* {$msg}</div>
		{/foreach}
	</div>
{/if}
{if $errors}
	<br>
	<div class="errdisplay">
		{foreach from=$errors item=msg}
			<div>* {$msg}</div>
		{/foreach}
	</div>
{/if}


	<form name="aForm" id="aForm" method="POST" action="">
	<input type="hidden" name="blah" value="update">
	<div class="container" style="margin: left; width: 760px; border: 1px solid silver; background-color:#eeeeee; ">
	
		<div class="standardconf" style="margin: 10px; padding: 10px; border: 1px dashed silver;">
			<i style="min-width: 120px; float: left; width: 6em;">ID:</i> {$plugins.plugin_id}<br>
			<i style="min-width: 120px; float: left; width: 6em;">Name:</i> {$plugins.plugin_name}<br>
			<i style="min-width: 120px; float: left; width: 6em;">Version:</i> {$plugins.plugin_version}<br>
			<i style="min-width: 120px; float: left; width: 6em;">Desc:</i> {$plugins.plugin_desc}<br>
		</div>
		
		<div class="varconf" style="margin: 10px; padding: 5px 5px 5px 8px; border: 1px dashed silver;">
			{foreach name=data key=key item=item from=$data}
				<input type="hidden" name="old[]" value="{$item.data_value}">
				<label for="" style="min-width: 150px; float: left; width: 6em;">{$item.data_name}</label>
				{*<input style="width: 20em; display: inline;" name="plugindata[{$item.data_id}]" value="{$item.data_value}">({$item.data_type})<br>*}
				<input style="width: 20em; display: inline;" name="plugindata[{$item.data_id}]" value="{$item.data_value}">({$item.data_type})<br>
			{/foreach}
		</div>
		<div class="buttons"  style=" text-align: right; clear: both; margin:7px;">
				Activate Plugin:
				<input type="radio" name="activeswitch" value="on" id="radio1" {if $plugins.plugin_active==1}checked{/if}><label for="radio1">on</label>
				<input type="radio" name="activeswitch" value="off" id="radio2" {if $plugins.plugin_active==0}checked{/if}><label for="radio2">off</label>
		
			{*<input type="checkbox" id="act" name="activeswitch" value="checked" {if $plugins.plugin_active==1} checked{elseif $plugins.plugin_active==0} {/if}><label for="act">activate Plugin</label>&nbsp;&nbsp;*}
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="Update">
			<input type="reset" value="Reset">
		</div>
			
	</div>
	</form>
	<br>
		<div style="float:center;margin:auto;">
			<a href="plugins.php">&nbsp;&nbsp;&nbsp;&nbsp;&#187;zur&uuml;ck</a>
		</div>
		
<br><br><h3>{$url.theme.this}<br>{$url.theme.shared}<br>{$url.base}</h3>

{include file="admin/inc.footer.tpl"}
			