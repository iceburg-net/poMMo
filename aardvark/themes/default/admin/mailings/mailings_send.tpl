{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}

<div id="mainbar">

 {if $messages}
    <div class="msgdisplay">
    {foreach from=$messages item=msg}
   	 <div>* {$msg}</div>
    {/foreach}
    </div>
 {/if}

<form action="" method="POST">

  <fieldset>
    <legend>{t}Mailing Patameters{/t}</legend>

		<div class="field">
			<div class="error">{validate id="fromname" message=$formError.fromname}</div>
			<label for="fromname"><span class="required">{t}From Name:{/t}</span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="fromname" value="{$fromname|escape}" id="fromname" />
			<div class="notes">{t}(maximum of 60 characters){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="fromemail" message=$formError.fromemail}</div>
			<label for="fromemail"><span class="required">{t}From Email:{/t}</span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="fromemail" value="{$fromemail|escape}" id="fromemail" />
			<div class="notes">{t}(maximum of 60 characters){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="frombounce" message=$formError.frombounce}</div>
			<label for="frombounce"><span class="required">{t}Bounce/Returnl:{/t}</span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="frombounce" value="{$frombounce|escape}" id="frombounce" />
			<div class="notes">{t}(maximum of 60 characters){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="subject" message=$formError.subject}</div>
			<label for="subject"><span class="required">{t}Mailing Subject:{/t}</span></label>
			<input type="text" class="text" size="32" maxlength="60"
			  name="subject" value="{$subject|escape}" id="subject" />
			<div class="notes">{t}(maximum of 60 characters){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="mailtype" message=$formError.mailtype}</div>
			<label for="mailtype"><span class="required">{t}Mail Format:{/t}</span></label>
			<select name="mailtype" id="mailtype">
				<option value="plain" {if $mailtype == 'plain'}SELECTED{/if}>{t}Plain Text Mailing{/t}</option>
				<option value="html" {if $mailtype == 'html'}SELECTED{/if}>{t}HTML Mailing{/t}</option>
			</select>
			<div class="notes">{t}(Select the format of this mailing){/t}</div>
		</div>
		
		<div class="field">
			<div class="error">{validate id="group_id" message=$formError.group_id}</div>
			<label for="group_id"><span class="required">{t}Send Mail To:{/t}</span></label>
			<select name="group_id" id="group_id">
				<option value="all" {if $group_id == 'all'}SELECTED{/if}>{t}All subscribers{/t}</option>
				{foreach from=$groups item=group_name key=key}
					<option value="{$key}" {if $group_id == $key}SELECTED{/if}>{$group_name}</option>
				{/foreach}
			</select>
			<div class="notes">{t}(Select who should recieve the mailing){/t}</div>
		</div>
	</fieldset>
	
<div>
	<input  type="submit" class="button" id="submit" name="submit" value="Continue" />
</div>
<div style="margin-left: 5%; margin-top: 5px;">
	{t escape=no 1="<span class=\"required\">" 2="</span>"}Fields in %1bold%2 are required{/t}
</div>
</form>

	
</div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}