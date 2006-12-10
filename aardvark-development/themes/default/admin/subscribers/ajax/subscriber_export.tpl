<div id="addOut" class="error"></div>
<div class="warn"></div>

<p>{t}Welcome to subscriber export! You can export subscribers in the current view as a .TXT file of email addresses or as a .CSV file containing all field data. Further, you can choose to export the "current page" (only subscribers listed in the table below) or "all pages" (all matching subscribers).{/t}</p>

<form method="post" action="ajax/subscriber_export2.php" id="pForm">

<fieldset>
<legend>{t}Export Subscribers{/t}</legend>

<div>
<label class="required" for="emails">{t}Export Type:{/t}</label>
<select name="type">
<option value="txt">{t}.TXT - Only Email Addresses{/t}</option>
<option value="csv">{t}.CSV - All subscriber Data{/t}</option>
</select>
</div>

<div>
<label class="required" for="emails">{t}Export Who?{/t}</label>
<select name="who">
<option value="all">{t}All Pages{/t}</option>
<option value="cur">{t}Current Page{/t}</option>
</select>
</div>

</fieldset>

<div class="buttons">
<input type="submit" value="{t}Export Subscribers{/t}" />
</div>

<input type="hidden" name="ids" value="" id="ids">
</form>

{literal}
<script type="text/javascript">
$().ready(function(){

	$('#autoFill').click(function() {
		box.val("");
		var emails = new Array();

		$('#subs tbody/tr:visible').find('td:eq(1)').each(function() {
			emails.push($(this).html());
		});

		box.val(emails.join("\n"));

		return false;
	});

	$('#pForm').submit(function() {
		
		if ($("select[@name='who']", this).val() == 'cur') {
			var ids = new Array();
			$('#subs tbody/tr:visible').find('td:eq(0)').each(function() {
				ids.push($("p.key", this).html());
			});
			
			$('#ids',this).val(ids.join());
		}
		else {
			$('#ids',this).val("");
		}

		return true;
	});

});
</script>
{/literal}