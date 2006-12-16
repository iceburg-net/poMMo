{include file="inc/tpl/admin.header.tpl"}

<h2>{t}Configure{/t}</h2>

<p><img src="{$url.theme.shared}images/icons/settings.png" alt="settings icon" class="articleimg" /> {t}You can change the login information, set website and mailing list parameters, end enable demonstration mode. If you enable demonstration mode, no emails will be sent from the system.{/t}</p>

{include file="inc/tpl/messages.tpl"}

<form method="post" action="">
<fieldset>
<legend>{t}Administrative{/t}</legend>

<div>
<label for="admin_username"><span class="required">{t}Administrator Username:{/t}</span> <span class="error">{validate id="admin_username" message=$formError.admin_username}</span></label>
<input type="text" size="32" maxlength="60" name="admin_username" value="{$admin_username|escape}" id="admin_username" />
<span class="notes">{t}(you will use this to login){/t}</span>
</div>

<div>
<label for="admin_password">{t}Administrator Password:{/t} </label>
<input type="text" size="32" maxlength="60" name="admin_password" value="{$admin_password|escape}" id="admin_password" />
<span class="notes">{t}(you will use this to login){/t}</span>
</div>

<div>
<label for="admin_password2">{t}Verify Password:{/t} <span class="error">{validate id="admin_password2" message=$formError.admin_password2}</span></label>
<input type="text" size="32" maxlength="60" name="admin_password2" value="{$admin_password2|escape}" id="admin_password2" />
<span class="notes">{t}(enter password again){/t}</span>
</div>
<div>

<label for="admin_email"><span class="required">{t}Administrator Email:{/t} <span class="error">{validate id="admin_email" message=$formError.admin_email}</span></span></label>
<input type="text" size="32" maxlength="60" name="admin_email" value="{$admin_email|escape}" id="admin_email" />
<span class="notes">{t}(email address of administrator){/t}</span>
</div>

</fieldset>

<fieldset>
<legend>{t}Website{/t}</legend>

<div>

<label for="site_name"><span class="required">{t}Website Name:{/t}</span> <span class="error">{validate id="site_name" message=$formError.site_name}</span></label>
<input type="text" size="32" maxlength="60" name="site_name" value="{$site_name|escape}" id="site_name" />
<span class="notes">{t}(The name of your Website){/t}</span>
</div>

<div>

<label for="site_url"><span class="required">{t}Website URL:{/t}</span> <span class="error">{validate id="site_url" message=$formError.site_url}</span></label>
<input type="text" size="32" maxlength="60" name="site_url" value="{$site_url|escape}" id="site_url" />
<span class="notes">{t}(Web address of your Website){/t}</span>
</div>

<div>
<label for="site_success">{t}Success URL:{/t} <span class="error">{validate id="site_success" message=$formError.site_success}</span></label>
<input type="text" size="32" maxlength="60" name="site_success" value="{$site_success|escape}" id="site_success" />
<span class="notes">{t}(Webpage users will see upon successfull subscription. Leave blank to display default welcome page.){/t}</span>
</div>

<div>
<label for="site_confirm">{t}Confirm URL:{/t} <span class="error">{validate id="site_confirm" message=$formError.site_confirm}</span></label>
<input type="text" size="32" maxlength="60" name="site_confirm" value="{$site_confirm|escape}" id="site_confirm" />
<span class="notes">{t}(Webpage users will see upon subscription attempt. Leave blank to display default confirmation page.){/t}</span>
</div>

</fieldset>

<div class="buttons">

<input type="submit" value="{t}Update{/t}" />

</div>

<fieldset>
<legend>{t}Mailing List{/t}</legend>

<div>
<label for="demo_mode">{t}Demonstration Mode:{/t} </label>
<input type="radio" name="demo_mode" value="on"{if $demo_mode == 'on'} checked="checked"{/if} /> on
<input type="radio" name="demo_mode" value="off"{if $demo_mode != 'on'} checked="checked"{/if} /> off
<span class="notes">{t}(Toggle Demonstration Mode){/t}</span>
</div>

<div>
<label for="list_confirm">{t}Email Confirmation:{/t} </label>
<input type="radio" name="list_confirm" value="on"{if $list_confirm == 'on'} checked="checked"{/if} /> on
<input type="radio" name="list_confirm" value="off"{if $list_confirm != 'on'} checked="checked"{/if} /> off
<span class="notes">{t}(Set to validate email upon subscription attempt.){/t}</span>
</div>

<div>
<label for="list_name"><span class="required">{t}List Name:{/t}</span> <span class="error">{validate id="list_name" message=$formError.list_name}</span></label>
<input type="text" size="32" maxlength="60" name="list_name" value="{$list_name|escape}" id="list_name" />
<span class="notes">{t}(The name of your Mailing List){/t}</span>
</div>

<div>
<label for="list_fromname"><span class="required">{t}From Name:{/t}</span> <span class="error">{validate id="list_fromname" message=$formError.list_fromname}</span></label>
<input type="text" size="32" maxlength="60" name="list_fromname" value="{$list_fromname|escape}" id="list_fromname" />
<span class="notes">{t}(Default name mails will be sent from){/t}</span>
</div>

<div>
<label for="list_fromemail"><span class="required">{t}From Email:{/t}</span> <span class="error">{validate id="list_fromemail" message=$formError.list_fromemail}</span></label>
<input type="text" size="32" maxlength="60" name="list_fromemail" value="{$list_fromemail|escape}" id="list_fromemail" />
<span class="notes">{t}(Default email mails will be sent from){/t}</span>
</div>

<div>
<label for="list_frombounce"><span class="required">{t}Bounce Address:{/t}</span> <span class="error">{validate id="list_frombounce" message=$formError.list_frombounce}</span></label>
<input type="text" size="32" maxlength="60" name="list_frombounce" value="{$list_frombounce|escape}" id="list_frombounce" />
<span class="notes">{t}(Returned emails will be sent to this address){/t}</span>
</div>

</fieldset>

<fieldset>
<legend>{t}Advanced{/t}</legend>

<div>
<label for="list_charset"><span class="required">{t}Character Set:{/t}</span> <span class="error">{validate id="list_charset" message=$formError.list_charset}</span></label>
<select name="list_charset" id="list_charset">
<option value="UTF-8"{if $list_charset == 'UTF-8'} selected="selected"{/if}>{t}UTF-8 (recommended){/t}</option>
<option value="ISO-8859-1"{if $list_charset == 'ISO-8859-1'} selected="selected"{/if}>{t}western (ISO-8859-1){/t}</option>
<option value="ISO-8859-2"{if $list_charset == 'ISO-8859-2'} selected="selected"{/if}>{t}Central/Eastern European (ISO-8859-2){/t}</option>
<option value="ISO-8859-7"{if $list_charset == 'ISO-8859-7'} selected="selected"{/if}>{t}Greek (ISO-8859-7){/t}</option>
<option value="ISO-8859-15"{if $list_charset == 'ISO-8859-15'} selected="selected"{/if}>{t}western (ISO-8859-15){/t}</option>
<option value="cp1251"{if $list_charset == 'cp1251'} selected="selected"{/if}>{t}cyrillic (Windows-1251){/t}</option>
<option value="KOI8-R"{if $list_charset == 'KOI8-R'} selected="selected"{/if}>{t}cyrillic (KOI8-R){/t}</option>
<option value="GB2312"{if $list_charset == 'GB2312'} selected="selected"{/if}>{t}Simplified Chinese (GB2312){/t}</option>
<option value="EUC-JP"{if $list_charset == 'EUC-JP'} selected="selected"{/if}>{t}Japanese (EUC-JP){/t}</option>
</select>
<span class="notes">{t}(Select Default Character Set of Mailings){/t}</span>
</div>

<div>
<label for="list_exchanger"><span class="required">{t}Mail Exchanger:{/t}</span> </label>
<select name="list_exchanger" id="list_exchanger">
<option value="sendmail"{if $list_exchanger == 'sendmail'} selected="selected"{/if}>Sendmail</option>
<option value="mail"{if $list_exchanger == 'mail'} selected="selected"{/if}>{t}PHP Mail Function{/t}</option>
<option value="smtp"{if $list_exchanger == 'smtp'} selected="selected"{/if}>SMTP Relay</option>
</select>
<span class="notes">{t}(Select Mail Exchanger){/t}</span>
</div>

{if $list_exchanger == 'smtp'}
<div>
<a href="setup_smtp.php"><img src="{$url.theme.shared}images/icons/right.png" alt="back icon" class="navimage" />Setup your SMTP Servers relays</a>
<span class="notes">{t}(configure SMTP relays){/t}</span>
</div>
{/if}

<div>
<a href="setup_messages.php"><img src="{$url.theme.shared}images/icons/right.png" alt="back icon" class="navimage" /> Customize mailed messages</a>
<span class="notes">{t}(define the email messages sent during subscription, updates, etc.){/t}</span>
</div>

<div>
<a href="setup_throttle.php"><img src="{$url.theme.shared}images/icons/right.png" alt="back icon" class="navimage" /> Set mail throttle values</a>
<span class="notes">{t}(controls mails per second, bytes per second, and domain limits){/t}</span>
</div>

</fieldset>

<div class="buttons">

<input type="submit" value="{t}Update{/t}" />

</div>

</form>

{include file="inc/tpl/admin.footer.tpl"}