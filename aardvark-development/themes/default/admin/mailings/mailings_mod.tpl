{include file="admin/inc.header.tpl"}

</div>
<!-- begin content -->


	<h1>{$actionStr}</h1>
	
		{* Display a eventual error message *}
		{if $messages}
			<div class="msgdisplay">
				{foreach from=$messages item=msg}
					<div>* {$msg}</div>
				{/foreach}
			</div>
		{/if}
		{if $errors}
			<br>
			<div class="errdisplay">
				{foreach from=$errors item=msg}
					<div>* {$msg}</div>
				{/foreach}
			</div>
		{/if}
	
	

	<div style="width:100%;">
		<span style="float: right; margin-right: 30px;">
			<a href="mailings_history.php">{t 1=$returnStr}Return to %1{/t}</a>
		</span>
	</div>

	<p style="clear: both; text-align:center;">
		<hr>
	</p>




	{if $action == 'view'}
	<!----------------- View Mail Action ----------------->

	<form name="aForm" id="aForm" method="POST" action="">
		<input type="hidden" name="action" value="{$action}">
		<input type="hidden" name="order" value="{$order}">
		<input type="hidden" name="orderType" value="{$orderType}">
		<input type="hidden" name="limit" value="{$limit}">
		
		<p>	
			<span style="text-align: center;">{t 1=$numbertodisplay}Displaying %1 mailings.{/t}</span>
		</p>
		
		{foreach name=viewloop from=$mailings key=key item=mailitem}
	
			<!-- Mail display -->
			<div style="background-color: #E6ECDA; width: 80%; text-align:left;">
				<table border="0" cellpadding="0" cellspacing="0" style="text-align:left; padding:10px;">
					<tr>
						<td>
							<p><b>{t}ID:{/t} </b>{$mailitem.id}</p>
							<p><b>{t}From:{/t} </b>{$mailitem.fromname} &lt;{$mailitem.fromemail}&gt;</p>
							{if $mailitem.fromemail != $mailitem.frombounce}<p><b>{t}Bounces:{/t} </b>&lt;{$mailitem.frombounce}&gt;</p>{/if}
							<p><b>{t}To:{/t} </b>{$mailitem.mailgroup}, <i>{$mailitem.subscriberCount}</i> {t}recipients.{/t}</p>
							<p><b>{t}Start time:{/t} </b>{$mailitem.started}</p>
							<p><b>{t}Finished:{/t} </b>{$mailitem.finished}</p>
							{*<p><b>{t}Character Set:{/t} </b>{$mailitem.charset}</p>*}
							<p><b>{t}Subject:{/t} {$mailitem.subject}</b></p>
						</td>
					</tr>
				</table>
			</div>
			<div style="background-color: #F6F8F1;  width: 80%; text-align:left;">
				<table border="0" cellpadding="0" cellspacing="0" style="text-align:left; padding:10px;">

					<tr>
						<td valign="top">
							{if $mailitem.ishtml == 'on'}
								<p>
									<b>{t}HTML Body:{/t} </b>
										 <a href="mailing_preview.php?action=viewhtml&viewid={$mailitem.id}" target="_blank">{t escape=no 1='</a>'}Click here %1 to view in a new browser window.{/t}
								</p>
								{if $mailitem.altbody}
									<p>
									<b>{t}Alt Body:{/t} </b>
									<br>
									<pre>{$mailitem.altbody}</pre>
									</p>
								{/if}
							{else}
								<p>
								<b>{t}Body:{/t} </b>
								<br>
								<pre>{$mailitem.body}</pre>
								</p>
							{/if}

						</td>
					</tr>
				</table>
				<hr>
			</div>

			<br>
		
		
		{/foreach}
		
	</form>



	{elseif $action == 'delete'}
	<!----------------- Delete Mail Action ----------------->

		
	<form name="aForm" id="aForm" method="POST" action="">
		<input type="hidden" name="action" value="{$action}">
		<input type="hidden" name="order" value="{$order}">
		<input type="hidden" name="orderType" value="{$orderType}">
		<input type="hidden" name="limit" value="{$limit}">
		

		<p>	
			<span style="text-align: center;">{t 1=$numbertodisplay}The following %1 mailings will be deleted{/t}:</span>
		</p>

		{if $numbertodisplay > 1}
		<p>
			<input type="submit" name="submitall" value="{t}Delete All{/t}">
		</p>
		{/if}


		<div style="width: 60%;">
		
			{foreach name=delloop from=$mailings key=key item=mailitem}
				<table border="0" cellpadding="0" cellspacing="0" style="background-color: #E6ECDA; width: 80%; text-align:left; padding:10px; margin:10px 10px 0px 10px;">
					<tr>
						<td>

							<input type="hidden" name="deleteEmails[]" value="{$mailitem.id}">
							<input type="hidden" name="mailid[]" value="{$mailitem.id}">
							
							<p><b>{t}ID:{/t} </b>{$mailitem.id}</p>
							<p><b>{t}From:{/t} </b>{$mailitem.fromname} &lt;{$mailitem.fromemail}&gt;</p>
							{if $mailitem.fromemail != $mailitem.frombounce}<p><b>{t}Bounces:{/t} </b>&lt;{$mailitem.frombounce}&gt;</p>{/if}
							<p><b>{t}To:{/t} </b>{$mailitem.mailgroup}, <i>{$mailitem.subscriberCount}</i> {t}recipients.{/t}</p>
							<p><b>{t}Start time:{/t} </b>{$mailitem.started}</p>
							<p><b>{t}Finished:{/t} </b>{$mailitem.finished}</p>
							<p><b>{t}Subject:{/t} {$mailitem.subject}</b></p>
						</td>
						{if $numbertodisplay > 1}
							<td style="vertical-align:bottom; text-align:right;">
									
								{* This is ugly, i had no other idea at the moment :D, maybe AJAX or i remember some trick *}
								<label for="sub">{t}Click to delete email with ID:{/t} <input type="submit" id="sub" name="submitone" value="{$mailitem.id}"></label>

							</td>
						{/if}

					</tr>
				</table>
				<table border="0" cellpadding="0" cellspacing="0" style="background-color: #F6F8F1; width: 80%; text-align:left; padding:10px; margin:0px 10px 10px 10px;">
					<tr>
						<td valign="top">
							{if $mailitem.ishtml == 'on'}
								<p>
									<b>{t}HTML Body:{/t} </b>
										 <a href="mailing_preview.php?action=viewhtml&viewid={$mailitem.id}" target="_blank">{t escape=no 1='</a>'}Click here %1 to view in a new browser window.{/t}
								</p>
								{if $mailitem.altbody}
									<p>
									<b>{t}Alt Body:{/t} </b>
									<br>
									<pre>{$mailitem.altbody}</pre>
									</p>
								{/if}
							{else}
								<p>
								<b>{t}Body:{/t} </b>
								<br>
								<pre>{$mailitem.body}</pre>
								</p>
							{/if}

						</td>
					</tr>
					</div>
				</table>
			
			{/foreach}
		
			<p>
				<div style="text-align:center;"><!--float: right;-->
					{if $numbertodisplay > 1}
						<p>
							<input type="submit" name="submitall" value="{t}Delete All{/t}">
						</p>
					{elseif $numbertodisplay == 1}
						<p>
							<input type="submit" name="submitall" value="{t}Click to Delete{/t}">
						</p>
					{/if}
				</div>
			</p><br>
		</div>
		</ul>

	</form>

	{else}
	<!----------------- In Case something goes wrong ----------------->
	
		<div>
			<span>{t}Problem during processing your request.{/t}
		</div>

	{/if}



{include file="admin/inc.footer.tpl"}

