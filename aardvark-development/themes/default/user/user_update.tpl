{include file="user/inc.header.tpl"}

<h2>{t}Subscriber Update{/t}</h2>

<p><a href="{$config.site_url}"><img src="{$url.theme.shared}images/icons/back.png" alt="back icon" class="navimage" /> {t website=$config.site_name}Return to %1{/t}</a></p>

{include file="admin/inc.messages.tpl"}
 	
{include file="subscribe/form.update.tpl"}

<form method="post" action="">
<img src="{$url.theme.shared}images/icons/nok.png" alt="not ok icon" />
<input type="hidden" name="Email" value="{$Email}" />

<div class="buttons">

<input type="submit" name="unsubscribe" value="{t}Click to Unsubscribe{/t}" />

</div>

</form>

{include file="user/inc.footer.tpl"}