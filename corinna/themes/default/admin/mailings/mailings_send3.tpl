{include file="admin/inc.header.tpl"}

<div id="preview">

<h2>{t}Preview Mailing{/t}</h2>

{include file="admin/inc.messages.tpl"}

<div class="msgheaders">

<p class="edit"><a href="mailings_send.php"><img src="{$url.theme.shared}images/icons/left.png" alt="back arrow icon" />{t}edit{/t}</a></p>

<p><strong>{t}Subject:{/t}</strong> <tt>{$subject}</tt></p>

<p><strong>{t}To:{/t}</strong> {$group} (<em>{$tally}</em> {t}recipients{/t})</p>

<p><strong>{t}From:{/t}</strong> {$fromname} <tt>&lt;{$fromemail}&gt;</tt></p>

{if $fromemail != $frombounce}
<p><strong>{t}Bounces:{/t}</strong> <tt>&lt;{$frombounce}&gt;</tt></p>
{/if}

{if $advanced}
<p><strong>{t}Character Set:{/t}</strong> <tt>{$charset}</tt></p>
{/if}

</div>

<div class="msgpreview">

<p class="edit"><a href="mailings_send2.php"><img src="{$url.theme.shared}images/icons/left.png" alt="back arrow icon" />{t}edit{/t}</a></p>

{if $ishtml == 'on'}

<p><strong>{t}HTML Body:{/t}</strong> <a href="mailing_preview.php" target="_blank">{t}View in a new browser window{/t}</a></p>

{if $altbody}
<p><strong>{t}Alt Body:{/t}</strong></p>

<pre>
{$altbody}
</pre>

{/if}

{else}

<p><strong>{t}Body:{/t}</strong></p>

<pre>
{$body}
</pre>

{/if}
</div>

<form method="post" action="" name="test">
<fieldset>
<legend>{t}Test mailing{/t}</legend>

<div>
<label for="testTo">{t}Test address:{/t}</label>
<input type="text" name="testTo" id="testTo" size="50" value="{$config.admin_email}" maxlength="60" />
</div>

</fieldset>

<div class="buttons">

<input type="submit" name="testMail" value="{t}Send Test{/t}" />

</div>

</form>

<form method="get" action="">

<div class="buttons">

<button type="submit" name="sendaway" value="TRUE"><img src="{$url.theme.shared}images/icons/send.png" alt="broadcast icon" />{t}Send Mailing{/t}</button>

</div>

</form>

</div>

{include file="admin/inc.footer.tpl"}