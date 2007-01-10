{include file="inc/tpl/admin.header.tpl"}

<ul class="inpage_menu">
<li><a href="{$url.base}admin/setup/setup_configure.php">{t 1=$returnStr}Return to %1{/t}</a></li>
</ul>

<h2>{t}Configure{/t} - {t}SMTP Relays{/t}</h2>

<p><img src="{$url.theme.shared}images/icons/settings.png" class="navimage right" alt="settings icon" />{t}Sent mail can relay mail through up to 4 SMTP servers simutaneously. Throttle settings can either be shared or individually controlled per SMTP relay (for maximum thoroughput).{/t}</p>

{include file="inc/tpl/messages.tpl"}

<form method="post" action=""id="form" name="form">
<fieldset>
<legend>{t}SMTP Throttling{/t}</legend>

<div>
<label for="throttle_SMTP">{t}Throttle Controller:{/t}</label>
<select name="throttle_SMTP" id="throttle_SMTP" onChange="document.form.submit()">
<option value="individual"{if $throttle_SMTP == 'individual'} selected="selected"{/if}>{t}Individual Throttler per Server{/t}</option>
<option value="shared"{if $throttle_SMTP == 'shared'}  selected="selected"{/if}>{t}Share a Global Throttler{/t}</option>
</select>
<div class="notes">{t}(throttle control can be shared or individual){/t}</div>
</div>
</fieldset>

{foreach from=$smtpStatus key=id item=status}
<fieldset>
<legend>{t 1=$id}SMTP #%1{/t}</legend>

<div>
<label>{t}SMTP Status:{/t}</label>

{if $status}
<img src="{$url.theme.shared}images/icons/ok.png" alt="ok icon" />{t}Connected to SMTP server{/t}

{else}
<img src="{$url.theme.shared}images/icons/nok.png" alt="not ok icon" />{t}Unable to connect to SMTP server{/t}
{/if}
</div>

<div>
<label for="host{$id}">{t}SMTP Host:{/t}</label>
<input type="text" size="32" maxlength="60" name="host[{$id}]" id="host{$id}" value="{$smtp[$id].host|escape}"  />
<div class="notes">{t}(IP Address or Name of SMTP server){/t}</div>
</div>

<div>
<label for="port{$id}">{t}Port Number:{/t}</label>
<input type="text" size="32" maxlength="60" name="port[{$id}]" id="port{$id}" value="{$smtp[$id].port|escape}"  />
<div class="notes">{t}(Port # of SMTP server [usually 25]){/t}</div>
</div>

<div>
<label for="auth{$id}">{t}SMTP Authentication:{/t}</label>
<input type="radio" name="auth[{$id}]" id="auth{$id}" value="on"{if $smtp[$id].auth == 'on'} checked="checked"{/if} /> on
<input type="radio" name="auth[{$id}]" value="off"{if $smtp[$id].auth != 'on'} checked="checked"{/if} /> off
<div class="notes">{t}(Toggle SMTP Authentication [usually off]){/t}</div>
</div>

<div>
<label for="user{$id}">{t}SMTP Username:{/t} </label>
<input type="text" size="32" maxlength="60" name="user[{$id}]" id="user{$id}" value="{$smtp[$id].user|escape}" />
<div class="notes">{t}(optional){/t}</div>
</div>

<div>
<label for="pass{$id}">{t}SMTP Password:{/t} </label>
<input type="text" size="32" maxlength="60" name="pass[{$id}]" id="pass{$id}" value="{$smtp[$id].pass|escape}" />
<div class="notes">{t}(optional){/t}</div>
</div>

</fieldset>

<div class="buttons">

<input type="submit" name="updateSmtpServer[{$id}]" id="updateSmtpServer{$id}" value="{t 1=$id}Update Relay #%1{/t}" />

<span>
{if $id == 1}
{t}This is your default relay{/t}
{else}

<input type="submit" name="deleteSmtpServer[{$id}]" id="deleteSmtpServer{$id}" value="{t 1=$id}Remove Relay #%1{/t}">
{/if}
</span>	

</div>
{/foreach}

{if $addServer}
<div class="buttons">

<input type="submit" name="addSmtpServer[{$addServer}]" id="addSmtpServer{$addServer}" value="{t}Add Another Relay{/t}" />

</div>
{/if}

</form>

{include file="inc/tpl/admin.footer.tpl"}