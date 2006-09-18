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

<a class="pommoClose" href="../../admin/admin.php" style="float: left;">
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

			//TODO
			Ein Standard administrator ausgegraut + unänderbar anzeigen??<br>
			This user is currently logged in<br><br>

	<div class="container" style="margin: left; width: 760px;>
	<div class="table" style="margin:0px; padding: 12px;">

			<div class="row" style="float:top;background-color:{cycle values="#eeeeee,#d0d0d0"}">
				<div class="cell" style="float: left; text-align: center; padding: 5px 10px 5px 10px; min-width: 15px;"><b>ID</b></div>
				<div class="cell" style="float: left; text-align: left; padding: 5px 10px 5px 10px; min-width: 100px;"><b>Name</b></div>
				<div class="cell" style="float: left; text-align: left; padding: 5px 10px 5px 10px; min-width: 100px;"><b>Pass: to md5</b></div>
				<div class="cell" style="float: left; text-align: left; padding: 5px 10px 5px 10px; min-width: 100px;"><b>Group</b></div>

				<div style="text-align: right; clear: both;width: 1px;"></div>
			</div>
		
		{foreach name=aussen key=nr item=user from=$user}
			<div class="row" style="float:top;background-color:{cycle values="#eeeeee,#d0d0d0"}">
				<div class="cell" style="float: left; text-align: center; padding: 5px 10px 5px 10px; min-width: 15px;">{$user.user_id}</div>
				<div class="cell" style="float: left; text-align: left; padding: 5px 10px 5px 10px; min-width: 100px;">{$user.user_name}</div>
				<div class="cell" style="float: left; text-align: left; padding: 5px 10px 5px 10px; min-width: 100px;">{$user.user_pass}</div>
				<div class="cell" style="float: left; text-align: left; padding: 5px 10px 5px 10px; min-width: 100px;">{$user.user_group}</div>
				<div class="cell" style="float: left; text-align: center; padding: 5px 10px 5px 10px; min-width: 60px;"><a href="setup_user.php?userid={$user.user_id}">edit</a></div>		
				<div class="cell" style="float: left; text-align: center; padding: 5px 10px 5px 10px; min-width: 60px;"><a href="usermanager.php?userid={$user.user_id}">delete</a></div>
				<div class="cell" style="float: left; text-align: center; padding: 5px 10px 5px 10px; min-width: 60px;"><a href="">bef&ouml;rdern</a></div>
				<div class="cell" style="float: left; text-align: center; padding: 5px 10px 5px 10px; min-width: 60px;"><a href="">eine option</a></div>		
				<div style="text-align: right; clear: both;width: 1px;"></div>
			</div>
		{/foreach}
	</div>
	</div>

<br><br>

{include file="admin/inc.footer.tpl"}

