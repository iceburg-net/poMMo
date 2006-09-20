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

{php} echo "<h2>URL: "; print_r($url); echo "</h2>"; {/php}

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
		If i put in this the $url.theme.this is always 
		/pommo/aardvark-corinna/pluginadmin/themes/default
		Why the pluginadmin dir is before /themes/default
<br><br>
{include file="admin/inc.header.tpl"}*}


<h1>{$returnStr}</h1>

<a class="pommoClose" href="../admin/admin.php" style="float: left;">
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
	<div class="container" style="margin: left; width: 760px; ">
	<div class="table" style="margin:0px;">
		{foreach name=aussen key=nr item=plugin from=$plugins}
			<div class="row" style="float:top;background-color:{cycle values="#eeeeee,#d0d0d0"};margin:0px; padding: 12px;">
				
				<div class="cell" style="border:1px dashed silver; float: left; text-align: center; padding: 3px 10px 3px 10px; min-width: 35px;">{$plugin.plugin_id}</div>
				<div class="cell" style="border:1px dashed silver; float: left; text-align: left; padding: 3px 10px 3px 10px; min-width: 135px;"><b>{$plugin.plugin_name}</b></div>
				<div class="cell" style="border:1px dashed silver; float: left; text-align: center; padding: 3px 10px 3px 10px; min-width: 35px; ">{$plugin.plugin_version}</div>
				<div class="cell" style="border:1px dashed silver; float: left; text-align: left; padding: 3px 10px 3px 10px; min-width: auto;">{$plugin.plugin_desc|truncate:100}</div>
				
				<div style="text-align: right; clear: both;width: 1px;"></div>
				
				<div class="buttons" style="float: right; ">
					<div class="cell" style="border:1px dotted silver; float: left; text-align: center; 
					padding: 3px 10px 3px 10px; min-width: 50px; ">
						<div style="text-align:center;">
								{if $plugin.plugin_active==1}
									<b><span style="color: green">(Plugin is active.)</span></b>
									<a href="setup_plugin.php?pluginid={$plugin.plugin_id}&onlyactivate=true&setto=0">&#187; deactivate</a>
								{elseif $plugin.plugin_active==0}
									<b><span style="color: red">(Plugin is <b>not</b> active.)</span></b>
									<a href="setup_plugin.php?pluginid={$plugin.plugin_id}&onlyactivate=true&setto=1">&#187; activate</a>
								{/if}
						</div>
					</div>
					<div class="cell" style="border:1px dotted silver; float: left; text-align: center; 
					padding: 3px 10px 3px 10px; min-width: 100px;">
						<div style="">
							<a href="setup_plugin.php?pluginid={$plugin.plugin_id}">&nbsp;&nbsp;edit this plugin</a>
						</div>
					</div>
				</div>
				<div style="text-align: right; clear: both;width: 1px;"></div>
			</div>
		{/foreach}
	</div>
	</div>

<br><br><h3>{$url.theme.this}<br>{$url.theme.shared}<br>{$url.base}</h3>

{include file="admin/inc.footer.tpl"}


