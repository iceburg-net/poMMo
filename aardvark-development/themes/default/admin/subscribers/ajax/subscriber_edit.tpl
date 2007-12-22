<div id="edOut" class="error"></div>
<div class="warn"></div>

<form method="post" action="ajax/subscriber_edit2.php" id="edForm">
<fieldset>
<legend>{t}Edit Subscriber{/t}</legend>

<div>
<label for="email"><strong class="required">{t}Email:{/t}</strong></label>
<input type="text" class="pvEmail pvEmpty" size="32" maxlength="60" name="email" />
</div>

{foreach name=fields from=$fields key=key item=field}
<div>
<label for="field{$key}">{if $field.required == 'on'}<strong class="required">{/if}{$field.name}:{if $field.required == 'on'}</strong>{/if}</label>

{if $field.type == 'checkbox'}
<input type="checkbox" name="d[{$key}]"{if $field.normally == "on"} checked="checked"{/if}{if $field.required == 'on'} class="pvEmpty"{/if} />

{elseif $field.type == 'multiple'}
<select name="d[{$key}]">
{foreach from=$field.array item=option}
<option{if $field.normally == $option} selected="selected"{/if}>{$option}</option>
{/foreach}
</select>

{elseif $field.type == 'date'}
<input type="text" class="pvDate{if $field.required == 'on'} pvEmpty{/if}" size="12" name="d[{$key}]" value={if $field.normally}"{$field.normally|escape}"{else}"{$config.app.dateformat}"{/if} />

{elseif $field.type == 'number'}
<input type="text" class="pvNumber{if $field.required == 'on'} pvEmpty{/if}" size="12" name="d[{$key}]" value="{if $field.normally}{$field.normally|escape}{/if}" />

{else}
<input type="text" size="32"{if $field.required == 'on'} class="pvEmpty"{/if} name="d[{$key}]" value="{if $field.normally}{$field.normally|escape}{/if}" />
{/if}

</div>

{/foreach}

</fieldset>

<fieldset>
	<input type="checkbox" name="force" />{t}Force (bypasses validation){/t}
</fieldset>

<div class="buttons">

<input type="hidden" name="id" value="0" />

<input type="submit" value="{t}Update Subscriber{/t}" />


</div>

<p>{t escape=no 1="<span class=\"required\">" 2="</span>"}Fields marked like %1 this %2 are required.{/t}</p>

</form>

{literal}
<script type="text/javascript">


$().ready(function(){

	// populate form with first selected row... TODO; add support for multiple subscriber editing at a time.
	var data = $('#grid').getRowData($('#grid').getSelectedRow());
	
	for (var i in data)  {
		
		// skip empty values/data
		var value = data[i].replace(/^\s*|\s*$/g,""); // trim value
		if(value == '')
			continue;
			
		// transform "d#" to "d[#]"
		var name = (i.match(/^d\d+$/)) ? 'd['+i.substr(1)+']' : i;
		$(':input[@name="'+name+'"]',$('#edForm')).each(function(){
			if($(this).attr('type') == 'checkbox')
				this.checked = (data[i] == 'on') ? true : false;
			else
				$(this).val(""+data[i]+"");
		}); 
	}
	
	PommoValidate.reset(); // TODO -- validate must be scoped to this ROW. Modify validate.js
	PommoValidate.init(':input','input[@type="submit"]', true, $('#edForm'));

	$('input[@name="force"]').click(function(){
		if(this.checked)
			PommoValidate.enable();
	});
		
	
    $('#edForm').ajaxForm({ 
        dataType:  'json', 
        success: function(ret) { 
        	$('#edOut').html(ret.message);
        	console.log(ret);
        	if(ret.success) {
        		
        		$('#grid').setRowData(ret.key,ret.subscriber);
        	}
        }
    }); 
	
	

});
</script>
{/literal}