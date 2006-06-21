{include file="admin/inc.header.tpl"}

</div>
<!-- begin content -->


	<h1>{t}Mailings History{/t}</h1>

		{* Display a eventual error message *}
		{if $errorstr}
		<div class="msgdisplay">
			{$errorstr}
		</div>
		{/if}

	{if $nomailing == true } 

		<div style="width:100%;">

		{*<!--Decide what actions -> export,...-->*}
			<span style="float: right; margin-right: 30px;">
				<a href="admin_mailings.php">{t 1=$returnStr}Return to %1{/t}</a>
			</span>
			<span style="float: right; margin-right: 30px;">
				<a href="mailings_history.php">{t 1=$returnStr2}Return to %1{/t}</a> 
			</span>
			
		</div>
		<p style="clear: both;"></p>
		<hr>
		<br>	
		<p><b>{t}No mailing found.{/t}</b></p><br>

	
	{else}
		{*<!--Decide what can we do with the mailings, export,...-->*}
		{*<!--<span style="float: right;">
				<a href="subscribers_export.php?table={$table}&group_id={$group_id}">{t}Export to CSV{/t}</a> 
			</span>-->*}

		<div style="width:100%;">
			<span style="float: right; margin-right: 30px;">
				<a href="admin_mailings.php">{t 1=$returnStr}Return to %1{/t}</a>
			</span>
		</div>
		<p style="clear: both;"></p>
		<hr>


    	<!-- Ordering options -->
		<div style="text-align: center; width: 100%;" >
	
			<form name="bForm" id="bForm" method="POST" action="">
		
				{t}Subscribers per Page:{/t} 
			
				<SELECT name="limit" onChange="document.bForm.submit()">
					<option value="10"{if $limit == '10'} SELECTED{/if}>10</option>
					<option value="20"{if $limit == '20'} SELECTED{/if}>20</option>
					<option value="50"{if $limit == '50'} SELECTED{/if}>50</option>
					<option value="100"{if $limit == '100'} SELECTED{/if}>100</option>
				</SELECT>
		
				<span style="width: 30px;"></span>
	
				{t}Order by:{/t}
				<SELECT name="order" onChange="document.bForm.submit()">
					<option value="id"{if $order == 'id'} SELECTED{/if}>id</option>
					<option value="fromname"{if $order == 'fromname'} SELECTED{/if}>From Name</option>
					<option value="fromemail"{if $order == 'fromemail'} SELECTED{/if}>From Email</option>
					<option value="frombounce"{if $order == 'frombounce'} SELECTED{/if}>From Bounce</option>
					<option value="started"{if $order == 'started'} SELECTED{/if}>Start Date</option>
					<option value="finished"{if $order == 'finished'} SELECTED{/if}>Finish Date</option>
					<option value="ishtml"{if $order == 'ishtml'} SELECTED{/if}>is html</option>
					<option value="sent"{if $order == 'sent'} SELECTED{/if}>Send ok</option>
					<option value="mailgroup"{if $order == 'mailgroup'} SELECTED{/if}>Mail group</option>
				</SELECT>

				<span style="width: 15px;"></span>
	
				<SELECT name="orderType" onChange="document.bForm.submit()">
					<option value="ASC"{if $orderType == 'ASC'} SELECTED{/if}>{t}ascending{/t}</option>
					<option value="DESC"{if $orderType == 'DESC'} SELECTED{/if}>{t}descending{/t}</option>
				</SELECT>
	
			</form>
			
		</div>
		<!-- End Ordering Options -->

		<br><br>
		<div style="text-align: center; width: 100%;" >
			( <em>{t 1=$rowsinset}%1 mailings{/t}</em> )
		</div>

		<!-- Table of Mailings -->
		<div style="text-align: center; width: 100%;" id="mailingtable" >
	
		<form name="oForm" id="oForm" method="POST" action="mailings_mod.php">
			<input type="hidden" name="order" value="{$order}">
			<input type="hidden" name="orderType" value="{$orderType}">
			<input type="hidden" name="limit" value="{$limit}">

			<table cellspacing="0" cellpadding="5" border="0" style="text-align: left; margin: 10px; margin-left:auto; margin-right:auto; ">

					<!--Table headers-->

					<tr>
							<td nowrap style="text-align:center;">{t}select{/t}</td>
							<td nowrap style="text-align:center;">{t}view{/t}</td>
							<td nowrap style="text-align:center;">{t}delete{/t}</td>

					  	{*{foreach from=$mailings item=item key=key}
						<!--	<td nowrap style="text-align:center;"><b>{$key}:{$item}</b></td>-->
					  	{/foreach}*}
				  	
					  		<td nowrap style="text-align:center;"><b>{t}ID{/t}</b></td>
					  		<td nowrap style="text-align:center;"><b>{t}From{/t}</b></td>
				  			<td nowrap style="text-align:center;"><b>{t}Email{/t}</b></td>
				  			<td nowrap style="text-align:center;"><b>{t}Bounce{/t}</b></td>
					  		{*<!--<td nowrap style="text-align:center;"><b>{t}Subject{/t}</b></td>-->*}
					  		{*<!--<td nowrap style="text-align:center;"><b>{t}Body{/t}</b></td>-->*}
					  		<td nowrap style="text-align:center;"><b>{t}Is HTML{/t}</b></td>
					  		<td nowrap style="text-align:center;"><b>{t}Mail Group{/t}</b></td>
					  		<td nowrap style="text-align:center;"><b>{t}Subscribers{/t}</b></td>
				  			<td nowrap style="text-align:center;"><b>{t}Started{/t}</b></td> <!--<a href="mailings_history.php?order=started"></a>-->
				  			<td nowrap style="text-align:center;"><b>{t}Finished{/t}</b></td>
					  		<td nowrap style="text-align:center;"><b>{t}Sent{/t}</b></td>
 	
					</tr>


			
					<!-- The Mailings -->	
				{foreach name=mailloop from=$mailings key=key item=mailitem}
					<tr bgcolor="{cycle values="#EEEEEE,#FFFFFF"}">				

							<td style="text-align:center;" nowrap>
									<input type="checkbox" name="mailid[]" value="{$mailitem.mailid}">
							</td>
						
							<td nowrap>
									<a href="mailings_mod.php?mailid={$mailitem.mailid}&action=view&limit={$limit}&order={$order}&orderType={$orderType}">{t}view{/t}</a>
							</td>

							<td nowrap>
									<a href="mailings_mod.php?mailid={$mailitem.mailid}&action=delete&limit={$limit}&order={$order}&orderType={$orderType}">{t}delete{/t}</a>
							</td>

							{*<td nowrap>
								<strong>{$mailitem}</strong>
							</td>*}
						{foreach name=propsloop from=$mailitem key=key item=item}
							<td nowrap>{$item}</td> {*{$key}:{$item}*}
						{/foreach}
				
				
					</tr>				
				{/foreach}

					<tr>
							<td colspan="12" style="text-align:left;">
								<b><a href="javascript:SetChecked(1,'mailid[]');">{t}Check All{/t}</a> 
								&nbsp;&nbsp; || &nbsp;&nbsp; 
								<a href="javascript:SetChecked(0,'mailid[]');">{t}Clear All{/t}</a></b>
							</td>
					</tr>
				
			</table>
		</div>

		<div style="text-align: center; width: 100%;" >
		
			<SELECT name="action">
					<option value="view">{t}View{/t} {t}checked mailings{/t} (Decide actions here)</option>
					<option value="delete">{t}Delete{/t} {t}checked mailings{/t}</option>
			</SELECT>

			&nbsp;&nbsp;&nbsp; 
			<input type="submit" name="send" value="{t}go{/t}">
					
			<br><br>
			{$pagelist}

		</form>
	
		</div>

		<!-- End Table of Mailings -->



	<!-- end mainbar -->

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
	

{/if} {* END if $nomailing == true *}



{include file="admin/inc.footer.tpl"}




