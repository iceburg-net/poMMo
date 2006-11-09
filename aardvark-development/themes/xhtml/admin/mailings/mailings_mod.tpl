{include file="admin/inc.header.tpl"}
{assign var='mailingCount' value=$mailings|@count}

<h2>{$actionStr}</h2>

{include file="admin/inc.messages.tpl"}

<ul class="inpage_menu">
<li><a href="mailings_history.php">{t 1=$returnStr}Return to %1{/t}</a></li>
</ul>
{if $action == 'view'}
<p class="count">{t 1=$mailingCount}Displaying %1 mailings{/t}</p>

{elseif $action == 'delete'}
<form method="post" action="" name="aForm" id="aForm">
<fieldset>
<legend>{t}Delete past mailings{/t}</legend>

<p id="warnmsg" class="warn">{t 1=$mailingCount}The following %1 mailings will be deleted{/t}!</p>
{/if}

{foreach from=$mailings key=key item=mailing}

{if $action == 'delete'}
<input type="hidden" name="delid[]" value="{$mailing.id}" />
{/if}

<div class="msgheaders">

<p><strong>{t}Subject:{/t}</strong> <tt>{$mailing.subject}</tt></p>

<p><strong>{t}To:{/t}</strong> {$mailing.mailgroup} (<em>{$mailing.subscriberCount}</em> {t}recipients{/t})</p>

<p><strong>{t}From:{/t}</strong> {$mailing.fromname} <tt>&lt;{$mailing.fromemail}&gt;</tt></p>

{if $mailing.fromemail != $mailing.frombounce}
<p><strong>{t}Bounces:{/t}</strong> <tt>&lt;{$mailing.frombounce}&gt;</tt></p>
{/if}

</div>

<div class="msgpreview">

{if $mailing.ishtml == 'on'}

<p><strong>{t}HTML Body:{/t}</strong> <a href="mailing_preview.php?viewid={$key}" target="_blank">{t}View in a new browser window{/t}</a></p>

{if $mailing.altbody}
<p><strong>{t}Alt Body:{/t}</strong></p>

<pre>
{$mailing.altbody}
</pre>

{/if}

{else}

<p><strong>{t}Body:{/t}</strong></p>

<pre>
{$mailing.body}
</pre>

{/if}
</div>

{/foreach}

{if $action == 'delete'}
</fieldset>

<div class="buttons">

<input type="submit" name="deleteMailings" value="{t}Delete Mailings{/t}" />

</div>
			
</form>
{/if}

{include file="admin/inc.footer.tpl"}