{if $datePicker}{capture name=head}
{* used to inject content into the HTML <head> *}
{include file="`$config.app.path`themes/shared/datepicker/datepicker.tpl"}
{/capture}{/if}
{include file="inc/tpl/user.header.tpl"}

<h3>{t}Subscriber Update{/t}</h3>

{include file="inc/tpl/messages.tpl"}
 	
{include file="subscribe/form.update.tpl"}

<form method="post" action="">
<input type="hidden" name="Email" value="{$Email}" />

<input type="submit" name="logout" value="{t}Logout{/t}" />

<h3>{t}Unsubscribe{/t}</h3>

<div class="buttons">

<button type="submit" name="unsubscribe" value="true">
<img src="{$url.theme.shared}images/icons/nok.png" alt="not ok icon" /> {t}Click to unsubscribe{/t} {$Email}
</button>

</div>

</form>

{include file="inc/tpl/user.footer.tpl"}