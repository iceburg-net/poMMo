{include file="admin/inc.header.tpl"}

{include file="admin/inc.messages.tpl"}

<form method="post" action="">

<fieldset>
<legend>{t}Mailing Parameters{/t}</legend>

<p>{t escape=no 1="<strong class=\"required\">" 2="</strong>"}Fields in %1bold%2 are required{/t}</p>

<div>
<label for="subject"><span class="required">{t}Subject:{/t}</span> <span class="error">{validate id="subject" message=$formError.subject}</span></label>
<input type="text" size="60" maxlength="60" name="subject" value="{$subject|escape}" id="subject" />
<div class="notes">{t}(maximum of 60 characters){/t}</div>
</div>

<div>
<label for="ishtml"><span class="required">{t}Mail Format:{/t}</span> <span class="error">{validate id="ishtml" message=$formError.ishtml}</span></label>
<select name="ishtml" id="ishtml">
<option value="on"{if $ishtml == 'on'} selected="selected"{/if}>{t}HTML Mailing{/t}</option>
<option value="off"{if $ishtml == 'off'} selected="selected"{/if}>{t}Plain Text Mailing{/t}</option>
</select>
<div class="notes">{t}(Select the format of this mailing){/t}</div>
</div>

<div>
<label for="mailgroup"><span class="required">{t}Send Mail To:{/t}</span> <span class="error">{validate id="mailgroup" message=$formError.mailgroup}</span></label>
<select name="mailgroup" id="mailgroup">
<option value="all"{if $mailgroup == 'all'} selected="selected"{/if}>{t}All subscribers{/t}</option>
{foreach from=$groups item=group key=key}
<option value="{$key}"{if $mailgroup == $key} selected="selected"{/if}>{$group.name}</option>
{/foreach}
</select>
<div class="notes">{t}(Select who should recieve the mailing){/t}</div>
</div>

<div>
<label for="fromname"><span class="required">{t}From Name:{/t}</span> <span class="error">{validate id="fromname" message=$formError.fromname}</span></label>
<input type="text" size="60" maxlength="60" name="fromname" value="{$fromname|escape}" id="fromname" />
<div class="notes">{t}(maximum of 60 characters){/t}</div>
</div>

<div>
<label for="fromemail"><span class="required">{t}From Email:{/t}</span> <span class="error">{validate id="fromemail" message=$formError.fromemail}</span></label>
<input type="text" size="60" maxlength="60" name="fromemail" value="{$fromemail|escape}" id="fromemail" />
<div class="notes">{t}(maximum of 60 characters){/t}</div>
</div>

<div>
<label for="frombounce"><span class="required">{t}Return:{/t}</span> <span class="error">{validate id="frombounce" message=$formError.frombounce}</span></label>
<input type="text" size="60" maxlength="60" name="frombounce" value="{$frombounce|escape}" id="frombounce" />
<div class="notes">{t}(maximum of 60 characters){/t}</div>
</div>

<div>
<label for="charset"><span class="required">{t}Character Set:{/t}</span> <span class="error">{validate id="charset" message=$formError.charset}</span></label>
<select name="charset" id="charset">
<option value="UTF-8"{if $charset == 'UTF-8'} selected="selected"{/if}>{t}UTF-8 (recommended){/t}</option>
<option value="ISO-8859-1"{if $charset == 'ISO-8859-1'} selected="selected"{/if}>{t}western (ISO-8859-1){/t}</option>
<option value="ISO-8859-2"{if $charset == 'ISO-8859-2'} selected="selected"{/if}>{t}Central/Eastern European (ISO-8859-2){/t}</option>
<option value="ISO-8859-7"{if $charset == 'ISO-8859-7'} selected="selected"{/if}>{t}Greek (ISO-8859-7){/t}</option>
<option value="ISO-8859-15"{if $charset == 'ISO-8859-15'} selected="selected"{/if}>{t}western (ISO-8859-15){/t}</option>
<option value="cp1251"{if $charset == 'cp1251'} selected="selected"{/if}>{t}cyrillic (Windows-1251){/t}</option>
<option value="KOI8-R"{if $charset == 'KOI8-R'} selected="selected"{/if}>{t}cyrillic (KOI8-R){/t}</option>
<option value="GB2312"{if $charset == 'GB2312'} selected="selected"{/if}>{t}Simplified Chinese (GB2312){/t}</option>
<option value="EUC-JP"{if $charset == 'EUC-JP'} selected="selected"{/if}>{t}Japanese (EUC-JP){/t}</option>
</select>
<div class="notes">{t}(Select Character Set of Mailings){/t}</div>
</div>


</fieldset>

<div class="buttons">

<input type="submit" id="submit" name="submit" value="Continue" />

</div>

</form>

{include file="admin/inc.footer.tpl"}