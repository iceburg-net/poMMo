{include file="inc/tpl/user.header.tpl"}

<h2>{t}Subscriber Login{/t}</h2>

<p>{t}In order to check your subscribtion status, update your information, or unsubscribe, you must enter your email address in the field below.{/t}</p>

{include file="inc/tpl/messages.tpl"}

<form method="post" action="">
<fieldset>
<legend>{t}Login{/t}</legend>

<div>
<label for="email"><span class="required"><strong>{t}Your Email:{/t}</strong></span> <span class="error">{validate id="email" message=$formError.email}</span></label>
<input type="text" size="32" maxlength="60" name="Email" id="email" value="{$Email|escape}" />
</div>

</fieldset>

<div class="buttons">

<input type="submit" value="{t}Login{/t}" />

</div>

</form>

{include file="inc/tpl/user.footer.tpl"}