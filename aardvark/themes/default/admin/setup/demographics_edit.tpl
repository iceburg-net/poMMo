{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}

<div id="mainbar">

<h1>{t}Edit Demographic{/t}</h1>

<img src="{$url.theme.shared}/images/icons/demographics.png" class="articleimg">

{if $intro}<p>{$intro}</p>{/if}

<a href="{$url.base}/admin/setup/setup_demographics.php">
		<img src="{$url.theme.shared}/images/icons/back.png" align="middle" class="navimage" border='0'>
		{t}Return to Demographics Page{/t}</a>
		<h2>{$demographic_name} &raquo;</h2>
  
{if $messages}
    <div class="msgdisplay">
    {foreach from=$messages item=msg}
   	 <div>* {$msg}</div>
    {/foreach}
    </div>
 {/if}
 
 <form action="" method="POST">
 <input type="hidden" name="demographic_id" value="{$demographic.id}">
  
<fieldset>
    <legend>Parameters</legend>

	<div class="field">
		<div class="error">{validate id="demographic_name" message=$formError.demographic_name}</div>
		<label for="demographic_name"><span class="required">{t}Name:{/t} </span></label>
		<input type="text" class="text" maxlength="60" size="32"
		  name="demographic_name" value="{$demographic_name|escape}" id="demographic_name" />
		<div class="notes">{t}A descriptive name to help you identify this demographic. End users will never see this.{/t}</div>
	</div>
	
	<br />
		
	<div class="field">
		<div class="error">{validate id="demographic_prompt" message=$formError.demographic_prompt}</div>
		<label for="demographic_prompt"><span class="required">{t}Prompt:{/t} </span></label>
		<input type="text" class="text" maxlength="60" size="32"
		  name="demographic_prompt" value="{$demographic_prompt|escape}" id="demographic_prompt" />
		<div class="notes">{t}The prompt for this demographic on the subscription form. ie. 'Type your city'{/t}</div>
	</div>
	
	<br />

	<div class="field">
		<label for="demographic_required">{t}Required:{/t} </label>
		<input type="checkbox" class="checkbox" {if $demographic_required}checked{/if}
		  name="demographic_required" id="demographic_required"  />
		<div class="notes">{t}Check to require this field on the subscription form. ie. user cannot leave blank.{/t}</div>
	</div>

	<br />

	<div class="field">
		<label for="demographic_active">{t}Active:{/t} </label>
		<input type="checkbox" class="checkbox" {if $demographic_active}checked{/if}
		 name="demographic_active" id="demographic_active"/>
	<div class="notes">{t}If not checked, this demographic will remain in the system, but will not display on the subscription form.{/t}</div>
	</div>
	
	<br />

	{if $demographic.type == 'text' || $demographic.type == 'number' || $demographic.type == 'date'}
	
	<div class="field">
		<label for="demographic_normally">{t}Default:{/t} </label>
		<input type="text" class="text" maxlength="60" size="32"
		name="demographic_normally" value="{$demographic_normally|escape}"  id="demographic_normally" />
		<div class="notes">{t}If provided, this value will be pre-filled in on the subscription form{/t}</div>
	</div>
	
	{elseif $demographic.type == 'checkbox'}
	
	<div class="field">
		<label for="demographic_normally">{t}Default:{/t} </label>
		<select name="demographic_normally" id="demographic_normally" />
			<option value="on" {if $demographic_normally}SELECTED{/if}>Checked</option>
			<option value="off" {if !$demographic_normally}SELECTED{/if}>Not Checked</option>
		</select>
		<div class="notes">{t}The initial state of the checkbox on the subscription form{/t}</div>
	</div>
	
	{elseif $demographic.type == 'multiple'}
	
	<div class="field">
		<label for="demographic_normally">{t}Default:{/t} </label>
		<select name="demographic_normally" id="demographic_normally" />
			<option value="">Select default choice</option>
			 {if $demographic.options}
    			{foreach from=$demographic.options item=option}
    				<option {if $demographic_normally == $option}SELECTED{/if}>{$option}</option>
    			{/foreach}
 			{/if}
		</select>
		<div class="notes">{t}Initial value selected on the subscription form{/t}</div>
	</div>
	
	{/if}
	
	<br />
</fieldset>
 	
 <div>
 	<input class="button" type="submit" value="{t}Update{/t}" />
 </div>
</form>

{if $demographic.type == 'multiple'}

<form id="dVal" name="dVal" action="" method="POST">
	<input type="hidden" name="demographic_id" value="{$demographic.id}">
	
	<div class="field" align="right">
		<input type="text" class="text" name="addOption" id="addOption" 
		  title="{t}type option(s){/t}" value="" size="50" />
		<input class="button" id="dVal-add" name="dVal-add" type="submit" value="{t}Add Option(s){/t}" />
	</div>
	<div class="msgdisplay">
		{t}NOTE: You can multiple options by separating each with a comma. To include special characters such as quote marks and commas, prefix them with a backslash (\\).{/t}
	</div>
	<br><br>

	<div class="field" align="right">
		<select name="delOption" id="delOption"/>
			{if $demographic.options}
    			{foreach from=$demographic.options item=option}
    				<option>{$option}</option>
    			{/foreach}
 			{/if}
		</select>
		<input class="button" id="dVal-del" name="dVal-del" type="submit" value="{t}Remove Selected Option{/t}" />
	</div>
</form>
{/if}


</div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}