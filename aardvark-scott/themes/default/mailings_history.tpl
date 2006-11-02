
</div>
<!-- begin content -->

	<h1>{t}Mailings History{/t}</h1>

		{* Display a eventual error message *}
		{if $messages}
			<div class="msgdisplay">
				{foreach from=$messages item=msg}
					<div>* {$msg}</div>
				{/foreach}
			</div>
		{/if}
		{if $errors}
			<div class="errdisplay">
				{foreach from=$errors item=msg}
					<div>* {$msg}</div>
				{/foreach}
			</div>
		{/if}

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
					<option value="subject"{if $state.sortBy == 'subject'} SELECTED{/if}>subject</option>
					<option value="started"{if $state.sortBy == 'started'} SELECTED{/if}>Start Date</option>
					<option value="finished"{if $state.sortBy == 'finished'} SELECTED{/if}>Finish Date</option>
					<option value="mailgroup"{if $state.sortBy == 'mailgroup'} SELECTED{/if}>Mail group</option>
					<option value="sent"{if $state.sortBy == 'sent'} SELECTED{/if}>Mails Sent</option>
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
		<p>&nbsp;</p>

		<div style="text-align: center; width: 100%;" >
			( <em>{t 1=$rowsinset}%1 mailings{/t}</em> )
		</div>

		<!-- Table of Mailings -->
		<div style="text-align: center; width: 100%;" id="mailingtable" >
	
			<table cellspacing="0" cellpadding="5" border="0" style="text-align: left; margin: 10px; margin-left:auto; margin-right:auto; ">

					<!--Table headers-->

					<tr>
							<td nowrap style="text-align:center;">{t}view{/t}</td>
					  		<td nowrap style="text-align:center;"><b>{t}Subject{/t}</b></td>
				  			<td nowrap style="text-align:center;"><b>{t}Date{/t}</b></td>
					</tr>

			
					<!-- The Mailings -->	
				{foreach name=mailloop from=$mailings key=key item=mailitem}
					<tr bgcolor="{cycle values="#EFEFEF,#FFFFFF"}">
							<td style="text-align:center;" nowrap>
									<a href="mailings_mod.php?mailid={$mailitem.mailid}&action=view">{t}view{/t}</a>
							</td>
							<td nowrap><i>{$mailitem.subject}</i></td>
							<td style="text-align:center;" nowrap>{$mailitem.started}</td>
				
					</tr>				
				{foreachelse}
					<tr>
						<td colspan="3">
							{t}No mailing found.{/t}
						</td>
					</tr>
				
				{/foreach}
								
			</table>
		<p>&nbsp;</p>

			{$pagelist}
		</div>

		<!-- End Table of Mailings -->



	<!-- end mainbar -->