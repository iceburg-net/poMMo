{include file="admin/inc.header.tpl"}

<form id="bForm" name="bForm" action="" method="POST">

{if $mailtype == 'html'}
<input type="hidden" name="mailtype" value="html">

	{if $editorType == 'text'}
		<script type="text/javascript" language="javascript">
		function xinhaSubmit() {ldelim}
			document.bForm.submit();
			return true;
		{rdelim}
		</script>
	{else}
		<script type="text/javascript" language="javascript">
		function xinhaSubmit() {ldelim}
			document.bForm.onsubmit();
			document.bForm.submit();
			return true;
		{rdelim}
		</script>
		
		<script type="text/javascript">
			 _editor_url  = "{$url.theme.shared}/xinha/"; 
			 _editor_lang = "en";
		</script>
		<script type="text/javascript" src="{$url.theme.shared}/xinha/htmlarea.js"></script>
		<script type="text/javascript" src="{$url.theme.shared}/xinha/config.js"></script>
	{/if}


	<SELECT name="editorType" onChange="xinhaSubmit()">
		<option value="wysiwyg">{t}Use WYSIWYG Editor{/t}</option>
		<option value="text" {if $editorType == 'text'}SELECTED{/if}>{t}Use Plain Text Editor{/t}</option>
	</SELECT>
	....... {t}Include alternative text body?{/t}
	<SELECT name="altInclude" onChange="xinhaSubmit()">
		<option value="yes">{t}Yes{/t}</option>
		<option value="no" {if $altInclude == 'no'}SELECTED{/if}>{t}No{/t}</option>
	</SELECT>
	...... &nbsp; <input class="button" id="bForm-submit" name="preview" type="submit" value="{t}Continue{/t}" />
	
	<hr>
	
	<fieldset>
		<legend>{t}HTML Message{/t}</legend>
		<textarea id="body" name="body" rows="10" cols="80" style="width: 100%;">{$body}</textarea>
	</fieldset>
	
	<br>
	
	{if $altInclude != 'no'}
	<fieldset>
		<legend>{t}Text Message{/t}</legend>
		
		<img src="{$url.theme.shared}/images/icons/down.png" align="absmiddle">&nbsp; &nbsp; 
		<input type="submit" name="altGen" value="{t}Copy text from HTML Message{/t}">
		
		<textarea  rows="10" cols="80" name="altbody" id="altbody">{$altbody}</textarea>
	</fieldset>
	{/if}

{else}
  <fieldset>
    <legend>{t}Mailing Body{/t}</legend>

		<div class="field">
			<label for="body"><span class="required">{t}Message:{/t}</span></label>
			<textarea  rows="10" cols="80"  id="body" name="body" />{$body}</textarea>
		</div>
  </fieldset>
  
 <div>
	<input class="button" id="bForm-submit" name="preview" type="submit" value="{t}Continue{/t}" />
</div>

{/if}
 
</form>

{include file="admin/inc.footer.tpl"}