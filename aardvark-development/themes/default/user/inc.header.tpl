<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>{$title}</title>
<link type="text/css" rel="stylesheet" href="{$url.theme.this}inc/user.css" />

{* If $head has been captured, print its contents here. Capture $head via templates
using {capture name=head}..content..{/capture} before including this header file. 
Useful for properly including javascripts and CSS in the HTML <head> *}

{$smarty.capture.head}

{* Include HTML FORM styling and javascript from shared theme directory when template
is prepared to include a form from the parent PHP script *}

{if $isForm}
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/bform.css" />

<script type="text/javascript" src="{$url.theme.shared}js/bform.js"></script>
{/if}
</head>
<body>

<div id="header">

<h1><a href="{$config.site_url}">{$config.site_name}</a></h1>

</div>

<div id="content">