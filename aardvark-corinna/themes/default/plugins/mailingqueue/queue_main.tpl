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
		
{*<a class="pommoClose" href="../../admin/admin.php" style="float: left;">
	<img src="{$url.theme.shared}/images/icons/left.png" align="absmiddle" border="0">&nbsp;
	{t}Return to Admins Page{/t}
</a><div style="clear: both; "></div>
<br>*}
		
		


{if $action == 'view' OR $action == 'delete'}

	{assign var='mailingCount' value=1}	{*only temporary*}
	
	{*Diffrent backlink for this actions -> return to the Queue List*}
	{*TODO action = no weg*}
	
		<div style="width:100%;">
			<span style="float: right; margin-right: 30px;">
				<a href="queue_main.php">{t 1=$returnStr}Return to %1{/t}</a><!--?action=no"-->
			</span>
		</div>
		<p style="clear: both;"></p>
		<hr>
	

		<form name="aForm" id="aForm" method="POST" action="">

			
			<p><span style="text-align: center;">
			{if $action == 'view'}
				{t 1=$mailingCount}Displaying %1 mailings.{/t}
			{elseif $action == 'delete'}
				{t 1=$mailingCount}The following %1 mailings will be deleted{/t}:
				<p>
					<input type="submit" name="deleteMailings" value="{t}Delete Mailings{/t}">
				</p>
			{/if}
			</span></p>
			
			{* If there is only one mail to display *} 

					<div style="background-color: #E6ECDA; width: 80%; text-align:left;">
						<table border="0" cellpadding="0" cellspacing="0" style="text-align:left; padding:10px;">
							<tr>
								<td>
									<p><b>{t}From:{/t} </b>{$mailing.fromname} &lt;{$mailing.fromemail}&gt;</p>
									{if $mailing.fromemail != $mailing.frombounce}<p><b>{t}Bounces:{/t} </b>&lt;{$mailing.frombounce}&gt;</p>{/if}
									<p><b>{t}To:{/t} </b>{$mailing.mailgroup} (id: {$mailing.mailgroupid}), <i>{$mailing.subscriberCount}</i> {t}recipients.{/t}</p>
									<p><b>{t}Subject:{/t} {$mailing.subject}</b></p>
								</td>	
							</tr>
						</table>
					</div>
					
					
					<div style="background-color: #F6F8F1;  width: 80%; text-align:left;">
						<table border="0" cellpadding="0" cellspacing="0" style="text-align:left; padding:10px;">
							<tr>
								<td valign="top">
									{if $mailing.ishtml == 'on' OR $mailing.ishtml== 'html'}
										<p>
											<b>{t}HTML Body:{/t} </b>
												 <a href="mailing_preview.php?viewid={$key}" target="_blank">{t escape=no 1='</a>'}Click here %1 to view in a new browser window.{/t}
										</p>
										{if $mailing.altbody}
											<p>
											<b>{t}Alt Body:{/t} </b>
											<br>
											<pre>{$mailing.altbody}</pre>
											</p>
										{/if}
									{else}
										<p>
										<b>{t}Body:{/t} </b>
										<br>
										<pre>{$mailing.body}</pre>
										</p>
									{/if}
				
								</td>
							</tr>
						</table>
						<hr>
					</div>
					<br>
			
			{if $action == 'delete'}
			<p>
				<input type="submit" name="deleteMailings" value="{t}Delete Mailings{/t}">
			</p>
			{/if}
						
			</form>




{* --------------------------- ELSE show Queue Matrix ---------------------------- *}

{else}

	{assign var='mailingCount' value=$mailings|@count}

		<div style="width:100%;">
			<span style="float: right; margin-right: 30px;">
				<a href="../../admin/mailings/admin_mailings.php">{t 1=$returnStr}Return to %1{/t}</a>
			</span>
		</div>
		<p style="clear: both;"></p>
		<hr>

    	<!-- Ordering options -->
		<div style="text-align: center; width: 100%;" >
	
			<form name="bForm" id="bForm" method="POST" action="">
		
				{t}Mailings per Page:{/t} 
			
				<SELECT name="limit" onChange="document.bForm.submit()">
					<option value="10"{if $state.limit == '10'} SELECTED{/if}>10</option>
					<option value="20"{if $state.limit == '20'} SELECTED{/if}>20</option>
					<option value="50"{if $state.limit == '50'} SELECTED{/if}>50</option>
					<option value="100"{if $state.limit == '100'} SELECTED{/if}>100</option>
				</SELECT>
		
				<span style="width: 30px;"></span>
	
				{t}Order by:{/t}
				<SELECT name="sortBy" onChange="document.bForm.submit()">
					<option value="subject"{if $state.sortBy == 'subject'} SELECTED{/if}>Subject</option>
					<option value="fromname"{if $state.sortBy == 'fromname'} SELECTED{/if}>Creator</option>
					<option value="date"{if $state.sortBy == 'date'} SELECTED{/if}>Date</option>
					<option value="mailgroup"{if $state.sortBy == 'mailgroup'} SELECTED{/if}>Mail group</option>
					<option value="ishtml"{if $state.sortBy == 'ishtml'} SELECTED{/if}>HTML Mail</option>
				</SELECT>

				<span style="width: 15px;"></span>
	
				<SELECT name="sortOrder" onChange="document.bForm.submit()">
					<option value="ASC"{if $state.sortOrder == 'ASC'} SELECTED{/if}>{t}ascending{/t}</option>
					<option value="DESC"{if $state.sortOrder == 'DESC'} SELECTED{/if}>{t}descending{/t}</option>
				</SELECT>
	
			</form>
			
		</div>
		<!-- End Ordering Options -->

		<br><br>
		<div style="text-align: center; width: 100%;" >
			( <em>{t 1=$rowsinset}%1 mailings{/t}</em> )
		</div>

		<!-- Table of Mailing Queue -->
		<div style="text-align: center; width: 100%;" id="mailingtable" >
	
		<form name="oForm" id="oForm" method="POST" action="queue_main.php"><!--self-->
			<table cellspacing="0" cellpadding="5" border="0" style="text-align: left; margin: 10px; margin-left:auto; margin-right:auto; ">

					<!--Table headers-->

					<tr>
							{*<td nowrap style="text-align:center;">{t}select{/t}</td>*}
							<td nowrap style="text-align:center;">{t}delete{/t}</td>
							<td nowrap style="text-align:center;">{t}view{/t}</td>
							<td nowrap style="text-align:center;">{t}send{/t}</td>
					  		<td nowrap style="text-align:center;"><b>{t}Subject{/t}</b></td>
					  		<td nowrap style="text-align:center;"><b>{t}Group (count){/t}</b></td>
					  		<td nowrap style="text-align:center;"><b>{t}created by{/t}</b></td>
				  			<td nowrap style="text-align:center;"><b>{t}Date{/t}</b></td>
					  		<td nowrap style="text-align:center;"><b>{t}HTML{/t}</b></td>
					</tr>

			
					<!-- The Mailings -->	
				{foreach name=mailloop from=$mailings key=key item=mailitem}
					<tr bgcolor="{cycle values="#EFEFEF,#FFFFFF"}">

							{*<td style="text-align:center;" nowrap>
									<input type="checkbox" name="mailid[]" value="{$mailitem.mailid}">
							</td>*}
						
							<td style="text-align:center;" nowrap>
									<a href="queue_main.php?mailid={$mailitem.mailid}&action=delete"> {t}delete{/t} </a>
							</td>

							<td style="text-align:center;" nowrap>
									<a href="queue_main.php?mailid={$mailitem.mailid}&action=view"> {t}view{/t} </a>
							</td>

							<td style="text-align:center;" nowrap>
									{*<!--<a href="mailings_mod.php?mailid={$mailitem.mailid}&action=reload">-->
									<!--<img src="{$url.theme.shared}/images/icons/reload-small.png" border="0" alt="{t}Send Mail{/t}"></a>-->*}
									{*<!--{t}reload{/t}-->*}
									<a href="queue_main.php?mailid={$mailitem.mailid}&action=send"> {t}edit&send{/t} </a>
							</td>

							<td nowrap><i>{$mailitem.subject}</i></td>
							<td nowrap>{$mailitem.mailgroup} {*({$mailitem.subscriberCount})*}</td>
							{*<td style="text-align:center;" nowrap>{$mailitem.sent}</td>*}
							<td style="text-align:center;" nowrap>{$mailitem.creator}</td>
							<td style="text-align:center;" nowrap>{$mailitem.date}</td>
							<td style="text-align:center;">
							{if $mailitem.ishtml == 'on'}
								<a href="../../admin/mailings/mailing_preview.php?viewid={$mailitem.mailid}" target="_blank">
								<img src="{$url.theme.shared}/images/icons/viewhtml.png" border="0" alt="{t}View HTML in new browser window{/t}"></a>
							{/if}
							</td>

					</tr>				
				{foreachelse}
					<tr>
						<td colspan="11">
							{t}No mailing found.{/t}
						</td>
					</tr>
				
				{/foreach}
				
				

					{*<tr>
							<td colspan="12" style="text-align:left;">
								<b><a href="javascript:SetChecked(1,'mailid[]');">{t}Check All{/t}</a> 
								&nbsp;&nbsp; || &nbsp;&nbsp; 
								<a href="javascript:SetChecked(0,'mailid[]');">{t}Clear All{/t}</a></b>
							</td>
					</tr>*}
				
			</table>
		</div>

		<div style="text-align: center; width: 100%;" >
		
			{*<SELECT name="action">
					<option value="view">{t}View{/t} {t}checked mailings{/t}</option>
					<option value="delete">{t}Delete{/t} {t}checked mailings{/t}</option>
			</SELECT>

			&nbsp;&nbsp;&nbsp; 
			<input type="submit" name="send" value="{t}go{/t}">*}
					
			<br><br>
			{$pagelist}

		</form>
	
		</div>

		<!-- End Table of Mailings -->

{/if}


<br><br>

	<!-- end mainbar -->
{*
	{literal}
	<script type="text/javascript">
	// <![CDATA[

	/* The following code is to "check all/check none" NOTE: form name must properly be set */
	var form='oForm' //Give the form name here
	function SetChecked(val,chkName) {
		dml=document.forms[form];
		len = dml.elements.length;
		var i=0;
		for( i=0 ; i<len ; i++) {
			if (dml.elements[i].name==chkName) {
				dml.elements[i].checked=val;
			}
		}
	}
	// ]]>
	</script>
	{/literal}
*}

{include file="admin/inc.footer.tpl"}








{* TODO Exclude this for now, maybe later multiple mail view and deleting *}

			{*{if ($mailing|@count == 0 )} from above *}
			{* <!--There are more mails to display--> Do not display multiple view/delete mailings for now 
			{else}
			
				{foreach from=$mailing key=key item=mailing}
					<input type="hidden" name="delid[]" value="{$mailing.id}">
					<div style="background-color: #E6ECDA; width: 80%; text-align:left;">
						<table border="0" cellpadding="0" cellspacing="0" style="text-align:left; padding:10px;">
							<tr>
								<td>
									<p><b>{t}From:{/t} </b>{$mailing.fromname} &lt;{$mailing.fromemail}&gt;</p>
									{if $mailing.fromemail != $mailing.frombounce}<p><b>{t}Bounces:{/t} </b>&lt;{$mailing.frombounce}&gt;</p>{/if}
									<p><b>{t}To:{/t} </b>{$mailing.mailgroup}, <i>{$mailing.subscriberCount}</i> {t}recipients.{/t}</p>
									<p><b>{t}Subject:{/t} {$mailing.subject}</b></p>
								</td>	
							</tr>
						</table>
					</div>
					
					
					<div style="background-color: #F6F8F1;  width: 80%; text-align:left;">
						<table border="0" cellpadding="0" cellspacing="0" style="text-align:left; padding:10px;">
							<tr>
								<td valign="top">
									{if $mailing.ishtml == 'on'}
										<p>
											<b>{t}HTML Body:{/t} </b>
												 <a href="mailing_preview.php?viewid={$key}" target="_blank">{t escape=no 1='</a>'}Click here %1 to view in a new browser window.{/t}
										</p>
										{if $mailing.altbody}
											<p>
											<b>{t}Alt Body:{/t} </b>
											<br>
											<pre>{$mailing.altbody}</pre>
											</p>
										{/if}
									{else}
										<p>
										<b>{t}Body:{/t} </b>
										<br>
										<pre>{$mailing.body}</pre>
										</p>
									{/if}
				
								</td>
							</tr>
						</table>
						<hr>
					</div>
				
					<br>
				{/foreach}
			{/if}*} 