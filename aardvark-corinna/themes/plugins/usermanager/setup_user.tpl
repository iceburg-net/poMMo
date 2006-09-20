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

<a class="pommoClose" href="usermanager.php" style="float: left;">
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
	<div class="container" style="margin: left; margin: 20px; width: 760px; padding: 10px; border: 1px solid silver; background-color:#eeeeee; ">

		<input type="hidden" name="old[user_id]" value="{$item.user_id}">
		<input type="hidden" name="old[user_name]" value="{$item.user_name}">
		<input type="hidden" name="old[user_pass]" value="{$item.user_pass}">
		<input type="hidden" name="old[user_group]" value="{$item.user_group}">

		<label for="" style="min-width: 150px; float: left; width: 6em;">Id:</label>
		<input style="width: 20em; display: inline;" name="userdata[{$item.user_id}]" 
			value="{$user.user_id}"><br>
		<label for="" style="min-width: 150px; float: left; width: 6em;">Name:</label>
		<input style="width: 20em; display: inline;" name="userdata[{$item.user_id}]" 
			value="{$user.user_name}"><br>
		<label for="" style="min-width: 150px; float: left; width: 6em;">Password:</label>
		<input style="width: 20em; display: inline;" name="userdata[{$item.user_id}]" 
			value="{$user.user_pass}"><br>
		<label for="" style="min-width: 150px; float: left; width: 6em;">Group:</label>
		<input style="width: 20em; display: inline;" name="userdata[{$item.user_id}]" 
			value="{$user.user_group}"><br>
		<label for="" style="min-width: 150px; float: left; width: 6em;">Rights:</label>
		<div style="min-width: 150px; float: left; width: 6em;">
			Wird eine Checklist blah<br>
			<input type="checkbox" name="blah" checked>Mailen&nbsp;&nbsp;<br>
			<input type="checkbox" name="blah">Mailings erstellen&nbsp;&nbsp;<br>
			<input type="checkbox" name="blah">Subscriber verwalten&nbsp;&nbsp;
		</div>

		<div class="buttons"  style=" text-align: right; clear: both; margin-top:5px;">
				Activate User:
				<input type="radio" name="activeswitch" value="on" id="radio1" {if $plugins.plugin_active==1}checked{/if}><label for="radio1">on</label>
				<input type="radio" name="activeswitch" value="off" id="radio2" {if $plugins.plugin_active==0}checked{/if}><label for="radio2">off</label>
	
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="Update">
			<input type="reset" value="Reset">
		</div>
			
	</div>
	</form>
	<br>
		<div style="float:center;margin:auto;">
			<a href="usermanager.php">&nbsp;&nbsp;&nbsp;&nbsp;&#187;zur&uuml;ck</a>
		</div>
<br><br>

{include file="admin/inc.footer.tpl"}
			