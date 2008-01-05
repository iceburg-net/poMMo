{if $redirect}
<script type="text/javascript">
window.location='{$redirect}';
</script>
{else}
<h4>{t}Preview Mailing{/t}</h4>

{include file="inc/messages.tpl"}

<div class="msgpreview">
	<p><strong>{t}Subject:{/t}</strong> <tt>{$subject}</tt></p>
	
	<p><strong>{t}To:{/t}</strong> <span style="color: #EA2B2B; font-size: 120%;">{$group}</span> (<em>{$tally}</em> {t}recipients{/t})</p>
	
	<p><strong>{t}From:{/t}</strong> {$fromname} <tt>&lt;{$fromemail}&gt;</tt></p>
	
	{if $fromemail != $frombounce}
	<p><strong>{t}Bounces:{/t}</strong> <tt>&lt;{$frombounce}&gt;</tt></p>
	{/if}
	
	<p><strong>{t}Character Set:{/t}</strong> <tt>{$list_charset}</tt></p>
</div>


<ul class="inpage_menu">
<li><a href="#" id="e_test"><img src="{$url.theme.shared}images/icons/world_test.png" alt="icon" border="0" align="absmiddle" />{t}Send Test{/t}</a></li>
<li><a href="#" id="e_send"><img src="{$url.theme.shared}images/icons/world.png" alt="icon" border="0" align="absmiddle" />{t}Send Mailing{/t}</a></li>
</ul>

<form id="sendForm" action="{$smarty.server.PHP_SELF}" method="post">
	<input type="hidden" name="sendaway" value="true">
</form>


<h4>{t}Message{/t}</h4>

<div class="msgpreview">
	{if $ishtml == 'on'}
	<strong>{t}HTML Message{/t}</strong> :
	<a href="ajax/mailing_preview.php" title="{t}Preview Message{/t}" onclick="return !window.open(this.href)">{t}Click Here{/t}</a>
	<hr />
	{/if}
	<strong>{t}Text Version{/t}</strong>: <br /><br />
	<div>{$altbody}</div>
</div>


{literal}
<script type="text/javascript">
$().ready(function() {
	
	$('#e_test').click(function() {
		$('#testMailing').jqmShow();
		return false;
	});
	
	$('#sendForm').ajaxForm({
		target: $('#Preview'),
		beforeSubmit: function() { $('#wait').jqmShow(); },
		success: function() { $('#wait').jqmHide(); }
	});
	
	$('#e_send').click(function() {
		$('#sendForm').submit();
		return false;
	});
	
});
</script>
{/literal}
{/if}