{capture name=head}{* used to inject content into the HTML <head> *}
{include file="inc/ui.form.tpl"}
{include file="inc/ui.dialog.tpl"}
{include file="inc/ui.tabs.tpl"}
{include file="inc/ui.slider.tpl"}
{/capture}

{include file="inc/admin.header.tpl" sidebar='off'}

<ul class="inpage_menu">
<li><a href="admin_setup.php" title="{t}Return to Setup Page{/t}">{t}Return to Setup Page{/t}</a></li>
</ul>

<h2>{t}Configure{/t}</h2>

<p><img src="{$url.theme.shared}images/icons/settings.png" alt="settings icon" class="navimage right" /> {t}You can change the login information, set website and mailing list parameters, end enable demonstration mode. If you enable demonstration mode, no emails will be sent from the system.{/t}</p>

{include file="inc/messages.tpl"}

<br class="clear">

<div id="tabs">
	<ul>
	    <li><a href="config/users.php" title="{t}Users{/t}"><span>{t}Users{/t}</span></a></li>
	    <li><a href="config/general.php" title="{t}General{/t}"><span>{t}General{/t}</span></a></li>
	    <li><a href="config/mailings.php" title="{t}Mailings{/t}"><span>{t}Mailings{/t}</span></a></li>
	    <li><a href="config/messages.php" title="{t}Messages{/t}"><span>{t}Messages{/t}</span></a></li>
	</ul>
</div>

<br class="clear">
<br class="clear">&nbsp;

<!-- end content (no footer)-->

{include file="inc/ui.dialog.tpl" dialogID="throttleWindow" dialogTitle=$throttleTitle dialogDrag=true dialogBodyClass="jqmdTall"}
{include file="inc/ui.dialog.tpl" dialogID="smtpWindow" dialogTitle=$smtpTitle dialogDrag=true dialogBodyClass="jqmdTall"}
{include file="inc/ui.dialog.tpl" dialogID="testWindow" dialogTitle=$testTitle dialogDrag=true}

{literal}
<script type="text/javascript">
$().ready(function(){ 

	poMMo.tabs = PommoTabs.init('#tabs');
	
	$('div.jqmDialog').jqm({
		trigger:false,
		overlay:0,
		ajax:'@href',
		target:'.jqmdMSG',
		onLoad: function(hash){
			var ajaxForm = $('form.ajax',hash.w)[0] || false;
			if (ajaxForm)
				poMMo.form.init(ajaxForm);		
		}
	});

	switch(location.hash) {
		case '#users': $('#tabs li a:eq(0)').click();
			break;
		case '#general':  $('#tabs li a:eq(1)').click();
			break;
		case '#mailings':  $('#tabs li a:eq(2)').click();
			break;
		case '#messages':  $('#tabs li a:eq(3)').click();
			break;
	}
	
});

</script>
{/literal}