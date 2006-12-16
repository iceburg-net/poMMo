{capture name=head}
{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript">
 _editor_url  = "{$url.theme.shared}js/xinha/"; 
 _editor_lang = "en";
</script>

{if $ishtml == 'on'}
	{if $editorType == 'text'}
		<script type="text/javascript" language="javascript">
			var xinha_enabled = false;
			function xinhaSubmit() {ldelim}
				document.bForm.submit();
				return true;
			{rdelim}
		</script>
	{else}
		<script type="text/javascript" src="{$url.theme.shared}js/xinha/htmlarea.js"></script>
		<script type="text/javascript" src="{$url.theme.shared}js/xinha/config.js"></script>
		<script type="text/javascript" language="javascript">
			var xinha_enabled = true;
			function xinhaSubmit() {ldelim}
				document.bForm.onsubmit();
				document.bForm.submit();
				return true;
			{rdelim}
			 $(function() {ldelim}
			 	xinha_init();
			 {rdelim});
		</script>

	{/if}
{else}
	<script type="text/javascript" language="javascript">
		var xinha_enabled = false;
	</script>
{/if}

{/capture}
{include file="inc/tpl/admin.header.tpl" sidebar='off'}

<div style="position: relative; width: 100%; z-index: 1;">
<a class="pommoOpen" href="#">{t}Add Personalization{/t}</a>

<div id="selectField" style="z-index: 2; display: none; position: absolute; top: -5px; left: -5px; width: 90%; background-color: #e6eaff; padding: 7px; border: 1px solid;">

<div class="pommoHelp">
<img src="{$url.theme.shared}images/icons/help.png" alt="help icon" style="float: right; margin-left: 10px;" /><strong>{t}Add Personalization{/t}:</strong> <span class="pommoHelp" style="display: none;">{t}Mailings can be personalized by adding subscriber field values to the body. For instance, you can have mailings begin with "Dear Susan, ..." instead of "Dear Subsriber, ...". The syntax for personalization is; [[field_name]] or [[field_name|default_value]]. If 'default_value' is supplied and a subscriber has no value for 'field_name', [[field_name|default_value]] will be replaced by default_value. The "[[..]]" will be erased and replaced with nothing if a default value is not supplied and the subscriber field value does not exist. Thus you can start a mailing with "Dear [[firstName|Friend]] [[lastName]], ..." providing you collect firstName and lastName fields.{/t}</span>

<hr style="clear: both;" />
</div>

<div>
<label for="field">{t}Insert field{/t}:</label>
<select id="field">
<option value="">{t}choose field{/t}</option>
<option value="Email">{t}Email{/t}</option>
{foreach from=$fields key=id item=field}
<option value="{$field.name}">{$field.name}</option>
{/foreach}
</select>
</div>

<div>
<label for="insert">{t}Default value{/t}:</label>
<input type="text" id="default" />
</div>

<div class="buttons">

<input id="insert" type="submit" value="{t}Insert{/t}" />

</div>			

<p><a href="#" class="pommoClose" style="float:right;">
<img src="{$url.theme.shared}images/icons/left.png" alt="back icon" class="navimage" />{t}Close{/t}</a></p>

</div>

</div>

<form method="post" action="" id="bForm" name="bForm">

{if $ishtml == 'on'}
<fieldset>
<legend>{t}Formating options{/t}</legend>

<div>
<label for="editorType">{t}Editor type{/t}:</label>
<select name="editorType" id="editorType" onchange="xinhaSubmit()">
<option value="wysiwyg" title="What You See Is What You Get">WYSIWYG</option>
<option value="text"{if $editorType == 'text'} selected="selected"{/if}>{t}plain text{/t}</option>
</select>
</div>

<div>
<label for="altInclude">{t}Alternative Text Body{/t}</label>
<select name="altInclude" id="altInclude" onchange="xinhaSubmit()">
<option value="yes">{t}Include{/t}</option>
<option value="no"{if $altInclude == 'no'} selected="selected"{/if}>{t}Exclude{/t}</option>
</select>
</div>

</fieldset>

<fieldset>
<legend>{t}HTML Message{/t}</legend>

<div>
<textarea id="body" name="body" rows="10" cols="120" style="width: 100%;">{$body}</textarea>
</div>

</fieldset>

{if $altInclude != 'no'}
<fieldset>
<legend>{t}Text Message{/t}</legend>

<button type="submit" name="altGen" id="altGen" onclick="xinhaSubmit()">
<img src="{$url.theme.shared}images/icons/down.png" alt="down icon" />{t}Copy text from HTML Message{/t}
</button>

<div>
<textarea rows="10" cols="120" name="altbody" id="altbody">{$altbody}</textarea>
</div>

</fieldset>
{/if}

<div class="buttons">

<input type="submit" id="bForm-submit" name="preview" value="{t}Continue{/t}" />

</div>

{else}
<fieldset>

<legend>{t}Mailing Body{/t}</legend>

<div>
<label for="body"><span class="required">{t}Message:{/t}</span></label>
<textarea rows="10" cols="120" id="body" name="body">{$body}</textarea>
</div>

</fieldset>
  
<div class="buttons">

<input type="submit" id="bForm-submit" name="preview" value="{t}Continue{/t}" />
<a href="mailings_send.php">{t}Cancel{/t}</a>

</div>

{/if}
 
</form>

{literal}
<script type="text/javascript">

$(function() {


	/********

	$("#altGen").click(function(){
		$("#altbody").val(xinha_editors.body.getHTML());
		return false;
	});

	
	$("#personalize").click(function() {
		$("#selectField").slideDown('slow', function() {
			$(this).find("a.pommoClose").click(function() {
					$("#selectField").slideUp('slow', function() { $(this).unclick(); });
					return false;
				});
			});
		return false;
		});
	***********/

	$("a.pommoOpen").click(function() { $(this).siblings("div").slideDown(); return false; });
		
	$("a.pommoClose").click(function() { $(this).parent().parent().slideUp(); return false; });

	$("div.pommoHelp img").click(function() {
		$(this).parent().find("span.pommoHelp").toggle(); return false;
		});
		
	$("#insert").click(function() {		
		if ($("#field").val() == '') { 
			alert ('{/literal}{t}You must choose a field{/t}{literal}'); 
			return false; 
			}
		
		// sting to append
		var str = '[['+($("#field").val())+(($("#default").val() == '')? '' : '|'+$("#default").val())+']]';
		
		if (!xinha_enabled) {
			// append to plain text editor (regular textarea)
			$("#body").get(0).value += (str);
		}
		else {
			// append to xinha editor
			xinha_editors.body.insertHTML(str);
		}
		
		
		// hide dialog
		$("#field").add("#default").val("");
		
		$('#selectField').hide();
		
		return false;
	});
});
</script>
{/literal}

{include file="inc/tpl/admin.footer.tpl"}