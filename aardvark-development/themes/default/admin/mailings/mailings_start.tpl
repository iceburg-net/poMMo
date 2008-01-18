{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jqModal.js"></script>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/modal.css" />
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/default.mailings.css" />

{include file="inc/ui.form.tpl"}
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
	
	// initialize all dialogs
	$('#wait, #addTemplate, #testMailing, #personalize').jqm({
		trigger: false,
		target: 'div.jqmdMSG',
		ajaxLoadText: '{/literal}<img src="{$url.theme.shared}images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" />{t escape=js}Please Wait{/t}...{literal}',
		onLoad: function(hash){
			var ajaxForm = $('form.ajax',hash.w)[0] || false;
			if (ajaxForm)
				poMMo.form.init(ajaxForm);		
		}
	}).jqDrag('div.jqmdTC');
	
	// tailor the dialogs
	$('#wait').jqm({modal: true, overlay: 0});
	$('#addTemplate').jqm({ajax: 'mailing/ajax.addtemplate.php'});
	$('#testMailing').jqm({ajax: 'mailing/ajax.mailingtest.php'});
	$('#personalize').jqm({ajax: 'mailing/ajax.personalize.php'});
	
});
</script>
{/literal}

{capture name=dialogs}
{include file="inc/ui.dialog.tpl" dialogID="wait" dialogNoClose=true dialogBodyClass="jqmdShort"}
{include file="inc/ui.dialog.tpl" dialogID="personalize" dialogTitle=$t_personalize dialogDrag=true dialogClass="jqmdWide" dialogBodyClass="jqmdTall"}
{include file="inc/ui.dialog.tpl" dialogID="addTemplate" dialogTitle=$t_saveTemplate dialogDrag=true dialogClass="jqmdWide" dialogBodyClass="jqmdTall"}
{include file="inc/ui.dialog.tpl" dialogID="testMailing" dialogTitle=$t_testMailing dialogDrag=true dialogClass="jqmdWide" dialogBodyClass="jqmdTall"}
{/capture}

{include file="inc/admin.footer.tpl"}