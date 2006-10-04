{* Include form CSS styling *}
<link href="{$url.theme.this}inc/subscribe_form.css" type="text/css" rel="STYLESHEET">

{* Include javascript to "stripe" the table by alternating row background color. 
	This can be also done via CSS on alternating table rows w/o the need for javascript... *}
<script src="{$url.theme.this}inc/stripe_table_rows.js" type="text/javascript"></script>

{literal}
<style>
	#subscribeForm .prompt {
		width: 35%;
		text-align: right;
	}
</style>
{/literal}

<div id="subscribeForm">

<form action="" method="POST">
<input type="hidden" name="updateForm" value="true">
<input type="hidden" name="original_email" value="{$original_email}">

	<fieldset style="width: 75%; margin: 0px; padding: 0px;">
		<legend>{t}Your Information{/t}</legend>
	
	<table id="stripeMe" border="0" width="100%" cellspacing="0" cellpadding="3">
	<tr>
		<td class="prompt">
			<label class="required">{t}Your Email:{/t}</label>
		</td>
		<td>
			<input type="text" class="text" size="32" maxlength="60" name="bm_email" id="bm_email"
			  value="{$bm_email}">
		</td>
	</tr>
		
	<tr>
		<td class="prompt">
			<label class="required">{t}Verify Email:{/t}</label>
		</td>
		<td>
			<input type="text" class="text" size="32" maxlength="60" name="email2" id="email2"
			value="{$email2}">
		</td>
	</tr>

	{foreach name=demos from=$fields key=key item=demo}
	<tr>
		<td class="prompt">
			<label {if $demo.required}class="required"{/if}>{$demo.prompt}</label>
		</td>
		<td>
			{if $demo.type == 'text'}
				<input type="text" class="text" size="32" name="d[{$key}]" id="d[{$key}]" 
				{if isset($d.$key)}value="{$d.$key}"{elseif $demo.normally}value="{$demo.normally}"{/if}>
					
			{elseif $demo.type == 'checkbox'}
				<input type="hidden" name="chkSubmitted" value="TRUE">
				<input type="checkbox" name="d[{$key}]" id="d[{$key}]"
				{if $d.$key == "on"}checked{elseif !isset($chkSubmitted) && $demo.normally == "on"}checked{/if}>
					
			{elseif $demo.type == 'multiple'}
				<select name="d[{$key}]" id="d[{$key}]">
						<option value="">{t}Choose Selection{/t}</option>
					{foreach from=$demo.options item=option}
   						<option {if $d.$key == $option}SELECTED{elseif !isset($d.$key) && $demo.normally == $option}SELECTED{/if}>{$option}</option>
   					{/foreach}
   				</select>
   					
   			{else}
   				{t}Unsupported Field Type.{/t}
   				
   			{/if}
   		</td>
   	</tr>
   	{/foreach}
	</table>
	
</fieldset>

<div style="margin-left: 5%; margin-top: 5px;">
	{t escape=no 1="<span class=\"required\">" 2="</span>"}Fields in %1bold%2 are required{/t}
</div>

<div style="margin-left: 15%; margin-top: 15px;">
	<input class="button" type="submit" name="update" value="{t}Update Records{/t}" />
</div>
		
</form>
</div>

<script type="text/javascript">
	stripe('stripeMe','#E5F6F2', '#F3FBF9');
</script>