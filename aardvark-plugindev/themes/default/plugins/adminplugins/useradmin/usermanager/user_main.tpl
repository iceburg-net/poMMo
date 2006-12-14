{include file="admin/inc.header.tpl"}

<div id="mainbar">

<h1>{t}poMMo User Manager{/t}</h1>

	{include file="admin/inc.messages.tpl"}
		
		<div>
			<a class="pommoClose" href="../useradmin.php" style="float: left; line-height:18px;">
			<img src="{$url.theme.shared}/images/icons/left.png" width="21" height="21" align="absmiddle" border="0">&nbsp;
			{t}Return to User Management Menu{/t}
			</a>
		</div>
		<div style="text-align: right; clear: both;width: 1px;"></div>
		<br>


	{* -------------------------- [user] USE CASES ----------------------------------- *}
	
	{if $showAddForm}
		<div class="actioncontainer"  style="margin: left; border: 1px solid silver; background-color:#eeeeee; padding:8px;">
			<h4>{$actionStr}</h4>
			<form action="" method="POST">
				<table style="font-size: 12px;" cellpadding="3" cellspacing="0">
					<tr>
						<td>Username:</td>
						<td><input type="text" name="username" value="{$username}"></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><input type="password" name="userpass" value="{$userpass}"></td>
					</tr>
					<tr>
						<td>Retype password:</td>
						<td><input type="password" name="userpasscheck" value="{$userpasscheck}"></td>
					</tr>
					<tr>
						<td>Permission Group:</td>
						<td>
							<select name="usergroup">
								<!--<option>--Select permission group--</option>-->
									{foreach key=nr item=groupitem from=$permgroups}
										<option name="name" value="{$groupitem.id}">{$groupitem.name}</option>
									{/foreach}
								
							</select>
						</td>
					</tr>
					{* Created is generated in DB, last_login will be written in the future *}
					<tr>
						<td colspan="2"><input type="submit" name="AddUser" value="Add User">  <input type="reset" name="reset" value="Reset"></td>
					</tr>
				</table>
			</form>
		</div>

	{elseif $showEditForm}
		<div class="actioncontainer"  style="margin: left; border: 1px solid silver; background-color:#eeeeee; padding:8px;">
			<h4>{$actionStr}</h4>
			<form action="" method="POST">
				<input type="hidden" name="userid" value="{$userinfo.id}">
				<table style="font-size: 12px;" cellpadding="3" cellspacing="0">
					<tr>
						<td>Username:</td>
						<td><input type="text" name="username" value="{$userinfo.name}"></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><input type="password" name="userpass" value="{$userinfo.pass}"></td>
					</tr>
					<tr>
						<td>Group:({$userinfo.perm})</td>
						<td><select name="usergroup"><!--TODO --wert-- werte funzen net-->
									<option name="group_name" value="nogroup" {if $userinfo.perm==""}selected{/if}>--no group--</option>
							{foreach key=nr item=groupitem from=$permgroups}
									<option name="group_name" value="{$groupitem.id}" 
										{if $userinfo.perm==$groupitem.name}selected{/if}>{$groupitem.name}</option>
							{/foreach}
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2"><input type="submit" name="EditUser" value="Save Changes">  <input type="reset" name="reset" value="Reset"></td>
					</tr>
				</table>
			</form>
		</div>

	{elseif $showDeleteForm}
		<div class="actioncontainer"  style="margin: left; border: 1px solid silver; background-color:#eeeeee; padding:8px;">
			<h4>{$actionStr}</h4>
			<form action="" method="POST">
				Do you really want to delete this user:<br>
				<input type="hidden" name="userid" value="{$userinfo.id}">
				Userid: {$userinfo.id}<br>
				Username: {$userinfo.name}<br>
				Permission Group: {$userinfo.perm}<br>
				Created: {$userinfo.created}<br>
				Last Login: {$userinfo.lastlogin}<br>
				<input type="submit" name="DeleteUser" value="Delete">
			</form>
		</div>
	{/if}

	
	
	{if $showEditForm OR $showAddForm OR $showDeleteForm}
		<div  style="text-align:right;">
			<a href="user_main.php">Cancel Process</a>
		</div>
	{/if}


	<br>
	
	
	
	
	{* ---------------------- [user] MATRIX ------------------------ *}
	
		<i>({t 1=$nrusers}%1 users{/t})</i>
		
		<table style=" float: left; font-size: 12px;" cellpadding="3" cellspacing="1" width="100%">

				<tr class="row" style="background-color:#AAAAAA">
					<td class="cell" style="text-align: center;"><b>ID</b></td>
					<td class="cell" style="text-align: center;"><b>Name</b></td>
					<td class="cell" style="text-align: center;"><b>Pass: to md5</b></td>
					<td class="cell" style="text-align: center;"><b>Group</b></td>
					<td class="cell" style="text-align: center;"><b>created</b></td>
					<td class="cell" style="text-align: center;"><b>last login</b></td>
					<td class="cell" style="text-align: center;"><b>login tries</b></td>
					<td class="cell" style="text-align: center;"><b>last edited</b></td>
					<td class="cell" style="text-align: center;"><b>active</b></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
		
			{foreach name=aussen key=nr item=user from=$user}
				<tr class="row"  valign="top" style="float:top;background-color:{cycle values="#eeeeee,#d0d0d0"}">
					<td class="cell" style="text-align: center;">{$user.id}</td>
					<td class="cell" style="text-align: left;">{$user.name}</td>
					<td class="cell" style="text-align: left;">{$user.pass}</td>
					<td class="cell" style="text-align: left;">{$user.perm}</td>
					<td class="cell" style="text-align: center;">{$user.created|date_format:"%d.%m.%Y"}</td>{*:"%A, %B %e, %Y"*}
					<td class="cell" style="text-align: center;">{$user.lastlogin|date_format:"%d.%m.%Y %H:%M"}</td>{*%x*}
					<td class="cell" style="text-align: center;">{$user.logintries}</td>
					<td class="cell" style="text-align: center;">{$user.lastedit}</td>
					<td class="cell" style="text-align: center;">{$user.active}</td>
					<td>
							<form action="" method="POST" style="padding:0px;margin:0px;">
									<input type="hidden" name="action" value="edit">
									<input type="hidden" name="userid" value="{$user.id}">
									<button onclick="window.location.href='user_main.php'">
										<img alt="edit" src="/pommo/aardvark-development/themes/shared/images/icons/edit.png"/>
									</button>
							</form>
					</td>
					<td>
							<form action="" method="POST" style="padding:0px;margin:0px;">
									<input type="hidden" name="action" value="delete">
									<input type="hidden" name="userid" value="{$user.id}">
									<button onclick="window.location.href='user_main.php'">
										<img alt="delete" src="/pommo/aardvark-development/themes/shared/images/icons/delete.png"/>
									</button>
							</form>
					</td>
					{*<td class="cell" style="text-align: center;"><a href="user_main.php?action=edit&userid={$user.id}">edit</a></td>		
					<td class="cell" style=" text-align: center;"><a href="user_main.php?action=delete&userid={$user.id}">delete</a></td>*}
				</tr>
			{/foreach}
		
		</table>
		
		{* --- ADD USER BUTTON -> ICON??? --- *}
		<div style="text-align: right; clear: both; width: 1px;"></div>
			<form action="" method="POST" style="" name="adduser">
				<input type="hidden" name="action" value="add">
				<a href="#" onClick="document.adduser.submit()">&raquo Add User</a><br>
			</form>
		<div style="text-align: right; clear: both; width: 1px;"></div>

		<br><br>



	{* ----------------- [group] MATRIX --------------------- *}
	<div>
		<div style="float:left; width: 65%">
		
			<i>({t 1=$nrperm}%1 permission groups{/t})</i>
		
			<table style="font-size: 12px; float:left;" cellpadding="3" cellspacing="0" width="95%">

					<tr style="background-color:#AAAAAA;">
						<td><b>ID</b></td>
						<td><b>Groupname</b></td>
						<td><b>Permissions</b></td>
						<td><b>Description</b></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					
				{foreach name=gr key=nr item=item from=$permgroups}
					<tr style="background-color:{cycle values="#eeeeee,#d0d0d0"}">
						<td  valign="top">{$item.id}</td>
						<td  valign="top">{$item.name}</td>
						<td  valign="top">{$item.perm}</td>
						<td  valign="top">{$item.desc}</td>
						<td valign="top">
								<form action="" method="POST" style="padding:0px;margin:0px;">
										<input type="hidden" name="action" value="editgroup">
										<input type="hidden" name="groupid" value="{$item.id}">
										<button onclick="window.location.href='user_main.php'">
											<img alt="edit" src="/pommo/aardvark-development/themes/shared/images/icons/edit.png"/>
										</button>
								</form>
						</td>
						<td valign="top">
								<form action="" method="POST" style="padding:0px;margin:0px;">
										<input type="hidden" name="action" value="deletegroup">
										<input type="hidden" name="groupid" value="{$item.id}">
										<button onclick="window.location.href='user_main.php'">
											<img alt="delete" src="/pommo/aardvark-development/themes/shared/images/icons/delete.png"/>
										</button>
								</form>
						</td>
						{*<td  valign="top"><a href="user_main.php?action=editgroup&groupid={$item.id}">edit</a></td>
						<td  valign="top"><a href="user_main.php?action=delgroup&groupid={$item.id}">delete</a></td>*}
					</tr>
				{/foreach}
				
			</table>

		</div>
	
	
	
	
		{* ------ [permission groups] USE CASES ------- *}
		
		<div style="float: right; width: 35%; text-align: left;">
			{if $showGroupAddForm}
				<div class="actioncontainer"  style="margin: left; border: 1px solid silver; background-color:#eeeeee; padding:8px;">
					<h4>{t}Add Group{/t}</h4>
					<form action="" method="POST">
						{t}Groupname:{/t} <input type="text" name="groupname" value="{$groupname}"><br>
						{t}Permissions:{/t} <input type="text" name="groupperm" value="{$groupperm}"><br>
						{t}Description:{/t} <input type="text" name="groupdesc" value="{$groupdesc}"><br>
						<input type="submit" name="AddGroup" value="Add Group">  <input type="reset" name="reset" value="Reset">
					</form>
				</div>
				<div  style="text-align:right;">
					<a href="user_main.php">Cancel Process</a>
				</div>
			{elseif $showGroupDelForm}
				<div class="actioncontainer"  style="margin: left; border: 1px solid silver; background-color:#eeeeee; padding:8px;">
					<h4>{t}Delete Group{/t}</h4>
					{t}Do you really want to delete this group? {/t}
					<form action="" method="POST">
						<input type="hidden" name="groupid" value="{$groupinfo.id}">
						{t}Groupname:{/t} {$groupinfo.name}<br>
						{t}Permissions:{/t} {$groupinfo.perm}<br>
						{t}Description:{/t} {$groupinfo.desc}<br>
						<input type="submit" name="DeleteGroup" value="Delete Permission Group">
					</form>
				</div>
				<div  style="text-align:right;">
					<a href="user_main.php">Cancel Process</a>
				</div>
			{elseif $showGroupEditForm}
				<div class="actioncontainer"  style="margin: left; border: 1px solid silver; background-color:#eeeeee; padding:8px;">
					<h4>{t}Edit Group{/t}</h4>
					<form action="" method="POST">
						<input type="hidden" name="groupid" value="{$groupinfo.id}">
						{t}Groupname:{/t} <input type="text" name="groupname" value="{$groupinfo.name}"><br>
						{t}Permissions:{/t} <input type="text" name="groupperm" value="{$groupinfo.perm}"><br>
						{t}Description:{/t} <input type="text" name="groupdesc" value="{$groupinfo.desc}"><br>
						<input type="submit" name="EditGroup" value="Edit Group">  <input type="reset" name="reset" value="Reset">
					</form>
				</div>
				<div  style="text-align:right;">
					<a href="user_main.php">Cancel Process</a>
				</div>
			{/if}
		</div>
		
		
		
		{* --- TODO ADD PERMISSION GROUP BUTTON -> ICON??? --- *}
		<div style="clear:both;"></div>
		<div style="text-align: left; ">
			<form action="" method="POST" style="" name="addgroup">
				<input type="hidden" name="action" value="addgroup">
				<a href="#" onClick="document.addgroup.submit()">&raquo {t}Add new permission group{/t}</a><br>
			</form>
		</div>
		<br><br><br>
	
			
{include file="admin/inc.footer.tpl"}