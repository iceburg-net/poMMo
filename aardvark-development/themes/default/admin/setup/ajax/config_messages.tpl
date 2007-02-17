{* Field Validation - see docs/template.txt documentation *}
{fv form='messages'}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="subscribe_sub"}
{fv validate="subscribe_msg"}
{fv validate="subscribe_suc"}
{fv validate="activate_sub"}
{fv validate="activate_msg"}
{fv validate="unsubscribe_suc"}

{*
<p>NOTIFICATION (email address(es))
		(check) PENDING
		(check) SUBSCRIBE
		(check) UNSUBSCRIBE
		(check) UPDATE</p> *}

<form action="{$smarty.server.PHP_SELF}" method="post">

<fieldset>
<legend>{t}subscribe{/t}</legend>

<div>
<label for="subscribe_sub"><span class="required">{t}Subject:{/t}</span>{fv message="subscribe_sub"}</label>
<input type="text" name="subscribe_sub" value="{$subscribe_sub|escape}" />
<div class="notes">{t}(Subject of Sent Email){/t}</div>
</div>

<div>
<label for="subscribe_msg"><span class="required">{t}Message:{/t}</span>{fv message="subscribe_msg"}</label>
<textarea name="subscribe_msg" rows="8" cols="44">{$subscribe_msg|escape}</textarea>
<div class="notes">{t escape='no' 1='<tt>' 2='</tt>'}(Use %1[[url]]%2 for the confirm link at least once){/t}</div>
</div>

<div>
<label for="subscribe_suc"><span class="required">{t}Success:{/t}</span>{fv message="subscribe_suc"}</label>
<textarea name="subscribe_suc" rows="3" cols="44">{$subscribe_suc|escape}</textarea>
<div class="notes">{t}(Message displayed upon success){/t}</div>
</div>

<div class="buttons">
<input type="submit" value="{t}Update{/t}" />
<input type="submit" name="restore[subscribe]" value="{t}Restore to Defaults{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</div>

<div class="output alert">{if $output}{$output}{/if}</div>

</fieldset>


<fieldset>
<legend>{t}activate Records{/t}</legend>

<div>
<label for="activate_sub"><span class="required">{t}Subject:{/t}</span>{fv message="activate_sub"}</label>
<input type="text" name="activate_sub" value="{$activate_sub|escape}" />
<div class="notes">{t}(Subject of Sent Email){/t}</div>
</div>

<div>
<label for="activate_msg"><span class="required">{t}Message:{/t}</span>{fv message="activate_msg"}</label>
<textarea name="activate_msg" rows="8" cols="44">{$activate_msg|escape}</textarea>
<div class="notes">{t escape='no' 1='<tt>' 2='</tt>'}(Use %1[[url]]%2 for the confirm link at least once){/t}</div>
</div>

<div class="buttons">
<input type="submit" value="{t}Update{/t}" />
<input type="submit" name="restore[activate]" value="{t}Restore to Defaults{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</div>

<div class="output alert">{if $output}{$output}{/if}</div>

</fieldset>


<fieldset>
<legend>{t}Unsubscribe{/t}</legend>

<div>
<label for="unsubscribe_suc"><span class="required">{t}Success:{/t}</span>{fv message="unsubscribe_suc"}</label>
<textarea name="unsubscribe_suc" rows="3" cols="44">{$unsubscribe_suc|escape}</textarea>
<div class="notes">{t}(Message displayed upon success){/t}</div>
</div>

<div class="buttons">
<input type="submit" value="{t}Update{/t}" />
<input type="submit" name="restore[unsubscribe]" value="{t}Restore to Defaults{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</div>

<div class="output alert">{if $output}{$output}{/if}</div>

</fieldset>

</form>