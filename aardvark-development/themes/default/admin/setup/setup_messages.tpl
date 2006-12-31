{include file="inc/tpl/admin.header.tpl"}

<ul class="inpage_menu">
<li><a href="{$url.base}admin/setup/setup_configure.php">{t 1=$returnStr}Return to %1{/t}</a></li>
</ul>

<h2>{t}Message Settings{/t}</h2>

<p><img src="{$url.theme.shared}images/icons/settings.png" class="navimage right" alt="settings icon" /> {t}You can configure the messages sent when users try to subscribe, unsubscribe, or update their records. You can also configure the messages displayed when the user successfully completes this task.{/t}</p>

<p><strong>{t}Note:{/t}</strong> {t escape='no' 1='<tt>' 2='</tt>'}Using %1[[url]]%2 in the message body will reference the confirmation link.{/t}</p>

{include file="inc/tpl/messages.tpl"}

<form action="" method="post">

<fieldset>
<legend>{t}Subscribe{/t}</legend>

<div>
<div class="error">{validate id="subscribe_sub" message=$formError.empty}</div>
<label for="Subscribe_sub"><span class="required">{t}Subject:{/t}</span></label>
<input type="text" size="32" maxlength="60" name="Subscribe_sub" value="{$Subscribe_sub|escape}" id="subscribe_sub" />
<div class="notes">{t}(Subject of Sent Email){/t}</div>
</div>

<div>
<div class="error">{validate id="subscribe_msg" message=$formError.url}</div>
<label for="Subscribe_msg"><span class="required">{t}Message:{/t}</span></label>
<textarea name="Subscribe_msg" id="subscribe_msg" rows="5">{$Subscribe_msg|escape}</textarea>
<div class="notes">{t escape='no' 1='<tt>' 2='</tt>'}(Use %1[[url]]%2 for the confirm link at least once){/t}</div>
</div>

<div>
<div class="error">{validate id="subscribe_suc" message=$formError.empty}</div>
<label for="Subscribe_suc"><span class="required">{t}Success:{/t}</span></label>
<textarea name="Subscribe_suc" id="subscribe_suc" rows="2">{$Subscribe_suc|escape}</textarea>
<div class="notes">{t}(Message displayed upon success){/t}</div>
</div>

<div class="buttons">
<input type="submit" value="{t}Update{/t}" />
<input type="submit" name="restore[subscribe]" value="{t}Restore to Defaults{/t}" />
</div>

</fieldset>


<fieldset>
<legend>{t}Activate Records{/t}</legend>

<div>
<div class="error">{validate id="activate_sub" message=$formError.empty}</div>
<label for="Activate_sub"><span class="required">{t}Subject:{/t}</span></label>
<input type="text" size="32" maxlength="60" name="Activate_sub" value="{$Activate_sub|escape}" id="activate_sub" />
<div class="notes">{t}(Subject of Sent Email){/t}</div>
</div>

<div>
<div class="error">{validate id="activate_msg" message=$formError.url}</div>
<label for="Activate_msg"><span class="required">{t}Message:{/t}</span></label>
<textarea name="Activate_msg" id="activate_msg" rows="5">{$Activate_msg|escape}</textarea>
<div class="notes">{t escape='no' 1='<tt>' 2='</tt>'}(Use %1[[url]]%2 for the confirm link at least once){/t}</div>
</div>

<div class="buttons">
<input type="submit" value="{t}Update{/t}" />
<input type="submit" name="restore[activate]" value="{t}Restore to Defaults{/t}" />
</div>

</fieldset>


<fieldset>
<legend>{t}Unsubscribe{/t}</legend>

<div>
<div class="error">{validate id="unsubscribe_suc" message=$formError.empty}</div>
<label for="Unsubscribe_suc"><span class="required">{t}Success:{/t}</span></label>
<textarea name="Unsubscribe_suc" id="unsubscribe_suc" rows="2">{$Unsubscribe_suc|escape}</textarea>
<div class="notes">{t}(Message displayed upon success){/t}</div>
</div>

<div class="buttons">
<input type="submit" value="{t}Update{/t}" />
<input type="submit" name="restore[unsubscribe]" value="{t}Restore to Defaults{/t}" />
</div>

</fieldset>

</form>

{include file="inc/tpl/admin.footer.tpl"}