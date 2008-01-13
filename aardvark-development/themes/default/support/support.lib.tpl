{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jqModal.js"></script>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/modal.css" />
{/capture}
{include file="inc/admin.header.tpl"}

<h2>poMMo support v0.02</h2>

<ul>
<li><a href="tests/file.clearWork.php" title="Clear Work Directory" class="modal">Clear Work Directory</a></li>
<li><a href="tests/mailing.test.php" onclick="return !window.open(this.href)">Test Mailing Processor</a></li>
<li><a href="tests/mailing.kill.php" title="Terminate Current Mailing" class="modal">Terminate Current Mailing</a></li>
<li><a href="tests/mailing.runtime.php"  onclick="return !window.open(this.href)">Test Max Runtime (takes 90 seconds)</a></li>
<li><a class="warn" href="util/db.clear.php" title="Reset Database">Reset Database (clears all subscribers, groups, fields)</a></li>
<li><a class="warn" href="util/db.subscriberClear.php" title="Reset Subscribers">Reset Subscribers (clears all susbcribers)</a></li>
<li><a class="warn" href="util/db.sample.php" title="Load Sample Data">Load Sample Data (resets database, loads sample data)</a></li>
</ul>

{literal}
<script type="text/javascript">
$().ready(function() {
	$('a.warn').click(function() {
		var str = this.innerHTML;
		return confirm("{/literal}{t}Confirm your action.{/t}{literal}\n"+str+"?");
	});
	
	$('#modal').jqm({
		trigger: 'a.modal',
		ajax: '@href',
		target: 'div.jqmdMSG',
		modal: true,
		ajaxLoadText: '{/literal}<img src="{$url.theme.shared}images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" />{t escape=js}Please Wait{/t}...{literal}'
	});
	
});
</script>
{/literal}

{capture name=dialogs}
{include file="inc/dialog.tpl" dialogID="modal"}
{/capture}

{include file="inc/admin.footer.tpl"}