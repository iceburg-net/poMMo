{include file="inc/tpl/admin.header.tpl"}

<h2>{t}Responsible Persons Management{/t}</h2>
Idea for this: Here you should declare responsible persons for subscriber groups,
that are persons who manage the "product management", these persons are inserted in the 
Reply field. example: when a user has questions / needs support from his responsible person,
he replies to his mailing list mail and gets in contact with his responsible person
example german, italian, english administrators, persons are grouped in: english group, it and de and
the responsible persons are the language speaking contacts at the other end of the mailing list.
or: A is responsible for Product A, B for Product B, C for C... We have 3 Mailing lists,
one for each product A,B,C (on sign up there were 3 checkboxes with: Are you interested in product A,B,C 
and the subscriber can choose) and when a subscriber has questions about B he (he does not 
recive mails about A & C except when he is grouped in this other groups too) he can mail 
directly with "his" Manager B.
<div id="boxMenu">

	<div>
		<a class="pommoClose" href="../useradmin.php" style="float: right; line-height:18px;">
		<img src="{$url.theme.shared}/images/icons/left.png" width="21" height="21" align="absmiddle" border="0">&nbsp;
		{t}Return to User Management Menu{/t}
		</a>
	</div><div class="clear"></div>

	{include file="inc/tpl/messages.tpl"}
</div>





<div id="plugincontent">

		<div align="center"><i>({t 1=$nrresp}%1 responsibilities{/t})</i></div>


		<form method="POST" action="" name="addResp">
			<input type="hidden" name="action" value="add">
			<a href="" onClick="document.addList.submit()"><b>&raquo; Add responsible person (Data for responsible persons, realname, bounceemail or so) (AJAX)</b></a><br>
		</form>
		<form method="POST" action="" name="addResp">
			<input type="hidden" name="action" value="add">
			<a href="" onClick="document.addList.submit()"><b>&raquo; Add responsibility (A connection person - group)(AJAX)</b></a><br>
		</form>

		{if ($nrresp <= 0) } 
			<p style="align:center;"><i>No responsible person found.</i></p>
		{else}
		
		
			<table border="0" border="0" cellspacing="1" cellpadding="3" width="100%" style="font-size:12px;">
			
			{foreach key=key item=item from=$resp}
			
				{if $item.uid=="" OR $item.name==""} 
						{* TODO: hmmm here user table can be empty and responsibles in the DB !!! make shure that tables are consistent *}

					{if $titel == NULL}
						{assign var="titel" value=FALSE}

						<tr><td colspan="5" style="height:10px; border-color: white; "></td></tr>
						<tr style="background-color: pink;"><td colspan="5">
							<div>No one is responsible for this groups:</div>
						</td></tr>
					{/if}
					<tr style="background-color: pink;">
						<td>
							{$item.gname} (group id: {$item.gid})
						</td>
						<td colspan="3" align="right" width="30">
								<form action="" method="POST" style="padding:0px;margin:0px;">
										<input type="hidden" name="listid" value="{$item.lid}">
										<input type="hidden" name="action" value="edit">
										<button onclick="window.location.href='resp_main.php'">
											<img alt="edit" src="/pommo/aardvark-development/themes/shared/images/icons/edit.png"/>
										</button>
								</form>
						</td>
						<td width="30">
								<form action="" method="POST" style="padding:0px;margin:0px;">
										<input type="hidden" name="listid" value="{$item.lid}">
										<input type="hidden" name="action" value="delete">
										<button onclick="window.location.href='resp_main.php'">
											<img alt="delete" src="/pommo/aardvark-development/themes/shared/images/icons/delete.png"/>
										</button>
								</form>
						</td>
					</tr>
					
				{else}

					{if $actusr!=$item.name}
						{assign var="actusr" value=$item.name}
						
						<tr><td colspan="5" style="height:10px; border-color: white; "></td></tr>
						<tr style="background-color:#D2D2D2;">
							<td colspan="5">
								<b>{$item.name}</b> (user id: {$item.uid})
							</td>
						</tr>
						<tr style="">
							<td>
								<b>Responsible Person Information:</b><br>
								Real Name / Sender info: {$item.realname}<br>
								Bounce email: {$item.bounceemail}<br>
								Info 3: {$item.sonst}
							</td>
						</tr>
					
					{/if}
					
					<tr style="background-color:#EFEFEF;">
						<td>
							{$item.gname} (group id: {$item.gid})
						</td>
						<td>{*<a onclick="javascript:document.getElementById('klappe{$item.lid}').style.display='block';">&raquo; show info</a><!--hidden, visible-->*}
						</td>
						<td width="30">
								{if $plugitem.pactive==1}
									<input type="hidden" name="active" value="0">
									<button class="edit tsToggleEdit" onclick="window.location.href='resp_main.php'">
										<img alt="deactivate plugin" src="/pommo/aardvark-development/themes/shared/images/icons/yes.png" />
									</button>
								{else}
									<input type="hidden" name="active" value="1">
									<button class="edit tsToggleEdit" onclick="window.location.href='resp_main.php'">
										<img alt="activate plugin" height="28" src="/pommo/aardvark-development/themes/shared/images/icons/nok.png" />
									</button>
								{/if}
						</td>
						<td width="30">
								<form action="" method="POST" style="padding:0px;margin:0px;">
										<input type="hidden" name="listid" value="{$item.lid}">
										<input type="hidden" name="action" value="edit">
										<button onclick="window.location.href='resp_main.php'">
											<img alt="edit" src="/pommo/aardvark-development/themes/shared/images/icons/edit.png"/>
										</button>
								</form>
						</td>
						<td width="30">
								<form action="" method="POST" style="padding:0px;margin:0px;">
										<input type="hidden" name="listid" value="{$item.lid}">
										<input type="hidden" name="action" value="delete">
										<button onclick="window.location.href='resp_main.php'">
											<img alt="delete" src="/pommo/aardvark-development/themes/shared/images/icons/delete.png"/>
										</button>
								</form>
						</td>
					</tr>
					<!-- ausklappen AJAX-->
					<tr style="padding:0px; margin:0px;">
						<td style="padding:0px 0px 0px 0px; margin:0px 0px 0px 0px;" colspan="5">
							<div id="klappe{$item.lid}" style="display:none; padding:0px; margin:0px;">
								<table cellspacing="0" cellpadding="3" width="100%" style="font-size:12px; border: 1px solid #CCCCCC; background-color: #EFEFEF; margin-left: 25px; ">
								<tr>
									<td>Description: </td><td>{$item.desc}</td>
								</tr><tr>
									<td>List Created: </td><td>{$item.created}</td>
								</tr><tr>
									<td>Mailings Sent: </td><td>{$item.setmailings}</td>
								</tr><tr>
									<td>Sender Information: </td><td>{$item.senderinfo}</td>
								</tr><tr>
									<td># Receiver: </td><td># {$item.receiver} <a>&raquo; view receiver list</a></td>
								</tr>
								</tr><tr>
									<td>Groups: </td><td> gruppennamen <a>&raquo; edit group list</a> delete und neue groupen adden AJAX</td>
								</tr>
								<tr>
									<td colspan="2" align="right">
										<a onclick="javascript:document.getElementById('klappe{$item.lid}').style.display='none';">&raquo; close</a>
									</td>
								</tr>
								</table><br>
							</div>
						</td>
					</tr>
					
				{/if}
			
			{/foreach}
		
			</table>

		<br>
		<form method="POST" action="" name="alloff">
			<input type="hidden" name="setallresponsiblesoff" value="TRUE">
			<a href="#" onClick="document.alloff.submit()">&raquo Deactivate all responsibilities</a><br>
		</form>
		<br><br>
		
		{/if} {* some responsibles found *}






{* USE CASES *}
{if $showDelete OR $showEdit OR $showAdd}
<div id="pluginaction" style="float: top; border: 1px solid red; background-color:pink; padding:20px;">
	->ajax
	{if $showDelete AND $listdata}
	
		<h3> Delete a mailing list </h3>
		Do you really want to delete this mailing list?
		List ID: {$listdata.list_id}<br>
		Name: {$listdata.list_name}<br>
		Description: {$listdata.list_desc}<br>
		Recipients: #<br>
		Manager: {$listdata.user_id}<br><br>
		<form action="">
			<input type="submit" name="deleteList" value="ReallyDelete">
			<button onclick="window.location.href='resp_main.php'">
				No i don't.
			</button>
		</form>
	{/if}

	{if $showEdit}
	
		{*Mailgroups und listdata*}
		<h3> Edit a mailing list </h3>
		<form action="">
			ID: <input type="text" name="listid" value="{$listdata.lid}"><br>
			Name: <input type="text" name="listname" value="{$listdata.lname}"><br>
			Description: <input type="text" name="listdesc" value="{$listdata.ldesc}"><br>
			Sender info: <input type="text" name="senderemail" value="{$listdata.lsenderinfo}"><br>		
			User: drop down responsible <input type="text" name="userarray" value="{*$listdata.*}"><button>+</button>	<br>
			Group: drop down <input type="text" name="grouparray" value="$mailgroups"><button>+</button>	<br>	
			{$mailgroups}<br>
			<input type="submit" name="editList" value="Edit"><input type="reset" name="reset" value="Reset">
		</form>
	{/if}

	{if $showAdd}
		<h3> Add a Mailing List </h3>
		<form action="">
			Name: <input type="text" name="listname" value=""><br>
			Description: <input type="text" name="listdesc" value=""><br>
			Sender info: <input type="text" name="senderemail" value=""><br>		
			User: drop down responsible <input type="text" name="userarray" value="testemail@blah.com"><button>+</button>	<br>
			Group: drop down <input type="text" name="grouparray" value="3"><button>+</button>	<br>	
		
			<input type="submit" name="addList" value="Add"><input type="reset" name="reset" value="Reset">
		</form>
	{/if}
</div>
{/if}
{*END USE CASES*}





	</div> <!-- plugincontent -->

{include file="inc/tpl/admin.footer.tpl"}