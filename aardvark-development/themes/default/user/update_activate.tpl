{include file="inc/tpl/user.header.tpl"}

<h2>{t}Update Activation{/t}</h2>

<p><a href="{$config.site_url}"><img src="{$url.theme.shared}images/icons/back.png" alt="back icon" class="navimage" />{t website=$config.site_name}Return to %1{/t}</a></p>

{include file="inc/tpl/messages.tpl"}

{t}Hello!{/t} {t}Before you can update your records on unsubscribe, you must first verify your email address{t}

<form method="get" action="">
<input type="hidden" name="Email" value="{$email}" />

<div>
<input type="submit" name="send" value="{t}Send a verification email{/t}">
</div>

<fieldset>
<legend>{t}Activation Code{/t}</legend>

{t}If you have recieved your verification email, enter the activation code below;{/t}
<input type="text" name="code" />

<div class="buttons">
<input type="submit" name="codeTry" value="{t}Submit{/t}" />
</div>

</fieldset>

{/if}

</form>


{include file="inc/tpl/user.footer.tpl"}