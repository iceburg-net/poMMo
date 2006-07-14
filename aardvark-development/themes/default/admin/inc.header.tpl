<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>{$title}</title>
	<link href="{$url.theme.this}/css/common.admin.css" type="text/css" rel="STYLESHEET">
	
{* Include any requested styling [set in parent PHP scripts (or via SMARTY) as $cssInclude[] *}
{foreach from=$cssInclude item=cssFile}
	<link href="{$url.theme.shared}/css/{$cssFile}" type="text/css" rel="STYLESHEET">
{/foreach} 

{* Include any requested styling [set in parent PHP scripts (or via SMARTY) as $cssInclude[] *}
{foreach from=$jsInclude item=jsFile}
	<script src="{$url.theme.shared}/js/{$jsFile}" type="text/javascript"></script>
{/foreach} 

{* Include HTML FORM styling and javascript from shared theme directory when template
	is prepared to include a form from the parent PHP script *}    
{if $isForm}
	<link href="{$url.theme.shared}/bform.css" type="text/css" rel="STYLESHEET">
	<script src="{$url.theme.shared}/bform.js" type="text/javascript"></script>
{/if}
{* The following fixes transparent PNG issues in IE < 7 *}
	<!--[if lt IE 7.]>
		<script defer type="text/javascript" src="{$url.theme.shared}/js/pngfix.js"></script>
	<![endif]-->
</head>
<body>

<a name="top" id="top"></a>
<center>

<div id="menu">
	<a href="{$url.base}/index.php?logout=TRUE">{t}Logout{/t}</a>
	<a href="{$url.base}/admin/admin.php">{t}Admin Page{/t}</a>
	<a href="{$config.site_url}">{$config.site_name}</a>
</div>

{if $header}
<div id="header">
	<h1>{$header.main}</h1>
	<h2>{$header.sub}</h2>
</div>
{/if}


<div id="content">

