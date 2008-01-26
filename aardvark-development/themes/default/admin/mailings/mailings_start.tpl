{capture name=head}{* used to inject content into the HTML <head> *}
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/default.mailings.css" />

{include file="inc/ui.form.tpl"}
{include file="inc/ui.dialog.tpl"}
{include file="inc/ui.tabs.tpl"}

{* Include the WYSIWYG Javascripts *}
{foreach from=$wysiwygJS item=js}
	<script type="text/javascript" src="{$url.theme.shared}../wysiwyg/{$js}"></script>
{/foreach}
{/capture}
{include file="inc/admin.header.tpl" sidebar='off'}

<ul class="inpage_menu">
<li><a href="admin_mailings.php" title="{t}Return to Subscribers Page{/t}">{t}Return to Mailings Page{/t}</a></li>
</ul>

{include file="inc/messages.tpl"}

<hr />
        
<div id="tabs">
	<ul>
	    <li><a href="mailing/setup.php" title="{t}Setup{/t}"><span>{t}Setup{/t}</span></a></li>
	    <li><a href="mailing/templates.php" title="{t}Templates{/t}"><span>{t}Templates{/t}</span></a></li>
	    <li><a href="mailing/compose.php" title="{t}Compose{/t}"><span>{t}Compose{/t}</span></a></li>
	    <li><a href="mailing/preview.php" title="{t}Preview{/t}"><span>{t}Preview{/t}</span></a></li>
	</ul>
</div>

{literal}
<script type="text/javascript">
$().ready(function(){ 
	
	poMMo.tabs = PommoTabs.init('#tabs');
	
	
	// Setup Modal Dialogs
	//PommoDialog.init(['addTemplate,testMailing,personalize']);
	PommoDialog.init();
	/*
	$('#addTemplate').jqm({ajax: 'mailing/ajax.addtemplate.php'});
	$('#testMailing').jqm({ajax: 'mailing/ajax.mailingtest.php'});
	$('#personalize').jqm({ajax: 'mailing/ajax.personalize.php'});
	*/
});
</script>
{/literal}

{capture name=dialogs}
{include file="inc/dialog.tpl" id=dialog wide=true tall=true}
{include file="inc/dialog.tpl" id="addTemplate" title=$t_saveTemplate wide=true tall=true}
{include file="inc/dialog.tpl" id="testMailing" title=$t_testMailing dialogDrag=true dialogClass="jqmdWide" dialogBodyClass="jqmdTall"}
{/capture}

{include file="inc/admin.footer.tpl"}