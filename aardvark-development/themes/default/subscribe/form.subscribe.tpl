<div id="subscribeForm">

<form method="post" action="{$url.base}user/process.php">
<fieldset>
<legend>Join newsletter</legend>

{if $referer}
<input type="hidden" name="bmReferer" value="{$referer}" />
{/if}

<div class="notes">

<p>{t escape=no 1="<span class=\"required\">" 2="</span>"}Fields in %1bold%2 are required{/t}</p>

</div>

<div>
<label class="required" for="email">{t}Your Email:{/t}</label>
<input type="text" class="text" size="32" maxlength="60" name="Email" id="email" value="{$Email|escape}" />
</div>

{foreach name=fields from=$fields key=key item=field}
<div>
<label {if $field.required == 'on'}class="required"{/if} for="field{$key}">{$field.prompt}:</label>

{if $field.type == 'text' || $field.type == 'number'}
<input type="text" class="text" size="32" name="d[{$key}]" id="field{$key}"{if isset($d.$key)} value="{$d.$key|escape}"{elseif $field.normally} value="{$field.normally|escape}"{/if} />

{elseif $field.type == 'checkbox'}
<input type="hidden" name="chkSubmitted" value="TRUE" />
<input type="checkbox" name="d[{$key}]" id="field{$key}"{if $d.$key == "on"} checked="checked"{elseif !isset($chkSubmitted) && $field.normally == "on"} checked="checked"{/if} />

{elseif $field.type == 'multiple'}
<select name="d[{$key}]" id="field{$key}">
<option value="">{t}Choose Selection{/t}</option>
{foreach from=$field.array item=option}
<option{if $d.$key == $option} selected="selected"{elseif !isset($d.$key) && $field.normally == $option} selected="selected"{/if}>{$option}</option>
{/foreach}
</select>

{elseif $field.type == 'date'}
<input type="text" class="text datepicker" size=12 name="d[{$key}]" id="field{$key}" value={if isset($d.$key)}"{$d.$key|escape}"{elseif $field.normally}"{$field.normally|escape}"{else}"{t}mm/dd/yyyy{/t}"{/if} />


{else}
{t}Unsupported field type{/t}
{/if}

</div>

{/foreach}

</fieldset>

<div class="buttons">

<input type="hidden" name="pommo_signup" value="true" />
<input type="submit" name="pommo_signup" value="{t}Subscribe{/t}" />

</div>
		
</form>

</div>