{include file="inc/tpl/admin.header.tpl"}

<h2>{t}Responsible Persons Management{/t}</h2>

<div id="boxMenu">

	{include file="inc/tpl/messages.tpl"}
		
		<div>
			<a class="pommoClose" href="../useradmin.php" style="float: left; line-height:18px;">
			<img src="{$url.theme.shared}/images/icons/left.png" width="21" height="21" align="absmiddle" border="0">&nbsp;
			{t}Return to User Management Menu{/t}
			</a>
		</div>
		<!--<div style="text-align: right; clear: both;width: 1px;"></div>-->
		<br>
		
</div>

<div id="plugincontent">

		{if ($nrlists <= 0) } 
			<i>No Mailing List found.</i>
		{else}

			{foreach key=key item=item from=$list}
	
				<div  style="border: 1px solid red;"> 
				I don't know how to organize this interface yet :/<br>
				{if $item.uname != $prev}
					{assign var="prev" value=$item.uname}
						
						{if empty($item.uname)}
							Noone administrates following list::<br><br>
						{else}
							<b>{$item.uname}</b> [id:{$item.uid}]<br>
							administrates following Mailing Lists:<br><br>
						{/if}
				{/if}

				<table cellpadding="3" cellspacing="0" width="100%" style="font-size: 12px;">
						{*<tr style="text-align:center;">
							<td><b><i>{t}Listname{/t}</i> - List Description</b></td>
							<td><b>{t}Senderinfo{/t}</b></td>
							<td><b>{t}Created{/t}</b></td>
							<td><b>{t}sent mails{/t}</b><br></td>
							<td></td><td></td><td></td>
						</tr>*}
						<tr style="text-align:left; padding-right: 10px; background-color:{cycle values="#eeeeee,#d0d0d0"}">
							<td valign="top"><i>{$item.name}</i> - {$item.desc}</td>
							<td valign="top" style="text-align:center;">{$item.senderinfo}</td>
							<td valign="top" style="text-align:center;">{$item.created}</td>
							<td valign="top" style="text-align:center;">{$item.sent}&nbsp;&raquo;mehr</td>
							<td valign="top">
									{if $plugitem.pactive==1}
											<input type="hidden" name="active" value="0">
											<button class="edit tsToggleEdit" onclick="window.location.href='config_main.php">
												<img alt="deactivate plugin" src="/pommo/aardvark-development/themes/shared/images/icons/yes.png" />
											</button>
									{else}
											<input type="hidden" name="active" value="1">
											<button class="edit tsToggleEdit" onclick="window.location.href='config_main.php">
												<img alt="activate plugin" height="28" src="/pommo/aardvark-development/themes/shared/images/icons/nok.png" />
											</button>
									{/if}
							</td>
							<td>
									<form action="" method="POST" style="padding:0px;margin:0px;">
											<input type="hidden" name="" value="">
											<input type="hidden" name="" value="">
											<button onclick="window.location.href='list_main.php'; return false;">
												<img alt="edit" src="/pommo/aardvark-development/themes/shared/images/icons/edit.png"/>
											</button>
									</form>
							</td>
							<td>
									<form action="" method="POST" style="padding:0px;margin:0px;">
											<input type="hidden" name="" value="">
											<button onclick="window.location.href='list_main.php'; return false;">
												<img alt="delete" src="/pommo/aardvark-development/themes/shared/images/icons/delete.png"/>
											</button>
									</form>
							</td>
						</tr>
					</table>




	
			</div><br><br>
			{/foreach}

		{/if}
				

	
	
	<br><br><br><br><br><br>

		
			{*-------------use-cases---------------*}
			
		{if $showAdd}	
			<div style="border: 1px solid red;">
				<form action="" method="POST">
				<input type="hidden">
					For User: 
						<select name="userid">
							{foreach key=kuser item=useritem from=$user}
							<option name="userid" value="{$useritem.uid}">{$useritem.name}</option>
							{/foreach}
						</select><br>
					Absender Name: <input type="text" name="realname" value=""><br>
					Absender Nachname: <input type="text" name="surname" value=""><br>
					Bounce Mail: <input type="text" name="bounceemail" value=""><br>
					<input type="submit" name="addResp" value="Add Responsible Person">  <input type="reset" name="reset" value="Reset">
				</form><div align="right"><a href="resp_main.php">Cancel Operation</a></div>
			</div>
				
		{elseif $showDel}
		
			<div style="border: 1px solid red;">
				<form action="" method="POST">
				<input type="hidden" name="delid" value="{$del.id}">
					For User: {$del.username}<br>
					Absender Name: {$del.realname}<br>
					Absender Nachname: {$del.surname}<br>
					Bounce Mail: {$del.bounceemail}<br>
					<input type="submit" name="delResp" value="Delete Responsible Person">
				</form><div align="right"><a href="resp_main.php">Cancel Operation</a></div>
			</div>
			
		{elseif $showEdit}
			<div style="border: 1px solid red;">
				<form action="" method="POST">
				<input type="hidden">
					For User: <input type="text" name="editid" value="{$edit.id}"><br>
					Absender Name: <input type="text" name="realname" value="{$edit.username}"><br>
					Absender Nachname: <input type="text" name="surname" value="{$edit.realname}"><br>
					Bounce Mail: <input type="text" name="bounceemail" value="{$edit.surname}"><br>
					<input type="submit" name="editResp" value="Edit Responsible Person">  <input type="reset" name="reset" value="Reset">
				</form><div align="right"><a href="resp_main.php">Cancel Operation</a></div>
			</div>
		{/if}
		
		
			{*-------------matrix------------------*}
		

		<div align="center">
			<i>({t 1=$nrresp}%1 responsible persons{/t})</i>
		</div>

		<table cellpadding="3" cellspacing="0" width="100%" style="font-size: 12px;">
		
			<tr>
				<td><b>userid</b></td>
				<td><b>Name</b></td>
				<td><b>Absender name</b></td>
				<td><b>Absender nachname</b></td>
				<td><b>Bounce email??</b></td>
				<td><b>Responsible for:</b></td>
				<td></td>
				<td></td
			</tr>
			
			{foreach key=key item=item from=$resp}
			<tr>
				<td>{$item.uid}</td>
				<td>{$item.name}</td>
				<td>{$item.realname}</td>
				<td>{$item.surname}</td>
				<td>{$item.bounceemail}</td>
				<td>&nbsp;</td>
				<td><a href="resp_main.php?action=showEdit&editid={$item.uid}">edit</a></td>
				<td><a href="resp_main.php?action=showDel&delid={$item.uid}">delete</a></td>
			</tr>
			{/foreach}

		</table>
		<br>
		<div align="left">
			<a href="resp_main.php?action=showAdd">&raquo; Add new responsible person</a>
		</div>

</div> <!-- plugincontent-->

{include file="admin/inc.footer.tpl"}