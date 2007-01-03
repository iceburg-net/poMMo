<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>{$title}</title>

<link type="text/css" rel="stylesheet" href="{$url.theme.this}inc/css/admin.css" />

{* If $head has been captured, print its contents here. Capture $head via templates
	using {capture name=head}..content..{/capture} before including this header file. 
	Useful for properly including javascripts and CSS in the HTML <head> *}
{$smarty.capture.head}
	
</head>
<body>

<div id="header">

<h1><a href="{$config.site_url}"><img src="{$url.theme.this}inc/images/pommo.gif" alt="pommo logo" /> <strong>{$config.site_name}</strong></a></h1>

</div>

<ul id="menu">
<li><a href="{$url.base}index.php?logout=TRUE">{t}Logout{/t}</a></li>
<li class="advanced"><a href="{$url.base}support/support.php">{t}Support{/t}</a></li>
<li><a href="{$url.base}admin/admin.php">{t}Admin Page{/t}</a></li>
</ul>

{if ($sidebar != 'off')}
{include file="inc/tpl/admin.sidebar.tpl"}

<div id="content">

{else}
<div id="content" class="wide">

{/if}