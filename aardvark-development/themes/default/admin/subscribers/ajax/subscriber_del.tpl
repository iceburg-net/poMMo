<div id="delOut" class="error"></div>
<div class="warn"></div>

<p>{t escape='no'}Enter email addresses of subscribers in the box below. Seperate emails with commas, spaces, or line breaks.{/t}</p>

<form method="post" action="ajax/subscriber_del2.php" id="delForm">

<fieldset>
<legend>{t}Remove Subscribers{/t}</legend>

<div>
<label for="emails"><strong class="required">{t}Email Addresses:{/t}</strong></label>
<textarea name="emails" cols="40" rows="8">{t}Enter Emails...{/t}</textarea>
</div>

</fieldset>

<div class="buttons">

<input type="submit" value="{t}Remove Subscribers{/t}" />

<input type="hidden" name="status" value="{$status}" />

<input type="reset" value="{t}Reset{/t}" />

</div>

</form>

{literal}
<script type="text/javascript">
$().ready(function(){

	var box = $('#delForm textarea');
	var orig = box.val();

	box.focus(function() {
		if ($(this).val() == orig)
			$(this).val("");
	});

	box.blur(function() {
		var val = $(this).val();
		val.replace(/^\s*|\s*$/g,"");
		if (val == "")
			$(this).val(orig);
	});
	
	var selected = $('#grid').getMultiRow();
	if(selected.length > 0) {
		var emails = new Array();
		var row = null;
		for (i=0; i<selected.length; i++) {
			row = $('#grid').getRowData(selected[i]);
			emails.push(row.email);
		}
		box.val(emails.join("\n"));
		
	}
	
	 $('#delForm').ajaxForm({ 
        dataType:  'json', 
        success: function(ret) { 
        	$('#delOut').html(ret.msg);
        	if(ret.success) {
    			console.log(ret.ids);    		
        		for (i=0; i<ret.ids.length; i++) {
    				$('#grid').delRowData(ret.ids[i]);
        		}
        	}
        	box.val("");
        }
    }); 
    

});
</script>
{/literal}