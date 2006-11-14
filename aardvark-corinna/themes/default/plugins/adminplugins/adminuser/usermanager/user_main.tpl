{include file="admin/inc.header.tpl"}
</div>
<!-- begin content -->

<h1>{t}poMMo User Manager{/t}</h1>

	<div class="container" style="margin: left; width: 760px;">

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
		
		

	{* -------------------------- USE CASES ----------------------------------- *}
	
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
						<td>Group:</td>
						<td>{*<input type="select" name="usergroup" value="{$usergroup}">*}
							<select name="usergroup">
								{foreach key=nr item=groupitem from=$usergroups}
									<option name="group_name" value="{$groupitem.group_id}">{$groupitem.group_name}</option>
								{/foreach}
							</select>
						</td>
					</tr>
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
				<input type="hidden" name="userid" value="{$userinfo.user_id}">
				<table style="font-size: 12px;" cellpadding="3" cellspacing="0">
					<tr>
						<td>Username:</td>
						<td><input type="text" name="username" value="{$userinfo.user_name}"></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><input type="password" name="userpass" value="{$userinfo.user_pass}"></td>
					</tr>
					<tr>
						<td>Group:</td>
						<td><select name="usergroup">
							{foreach key=nr item=groupitem from=$usergroups}
									<option name="group_name" value="{$groupitem.group_id}" {if $userinfo.user_group==$groupitem.group_name}selected{/if} >{$groupitem.group_name}</option>
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
				<input type="hidden" name="userid" value="{$userinfo.user_id}">
				Userid: {$userinfo.user_id}<br>
				Username: {$userinfo.user_name}<br>
				Group: {$userinfo.user_group}<br>
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
	{* ---------------------- Show User Matrix ------------------------ *}

	
		<a class="pommoClose" href="../adminuser.php" style="float: left;">
			<img src="{$url.theme.shared}/images/icons/left.png" align="absmiddle" border="0">&nbsp;
			{t}Return to User Management Menu{/t}
		</a>
		<div style="text-align: right; clear: both;width: 1px;"></div>
		<br>
	
		<i>({t 1=$nrusers}%1 users{/t})</i>
		
		<table style=" float: left; font-size: 12px;" cellpadding="3" cellspacing="0" width="100%">

			<tr class="row" style="background-color:#aaaaaa">
				<td class="cell" style="text-align: center;"><b>ID</b></td>
				<td class="cell" style="text-align: left;"><b>Name</b></td>
				<td class="cell" style="text-align: left;"><b>Pass: to md5</b></td>
				<td class="cell" style="text-align: left;"><b>Group</b></td>
				<td> </td>
				<td> </td>
				<!--<td style="text-align: right; clear: both;width: 1px;"></td>-->
			</tr>
		
		{foreach name=aussen key=nr item=user from=$user}
			<tr class="row" style="float:top;background-color:{cycle values="#eeeeee,#d0d0d0"}">
				<!--<form action="" method="POST">-->
				<td class="cell" style="text-align: center;">{$user.user_id}</td>
				<td class="cell" style="text-align: left;">{$user.user_name}</td>
				<td class="cell" style="text-align: left;">{$user.user_pass}</td>
				<td class="cell" style="text-align: left;">{$user.user_group}</td>
				<td class="cell" style="text-align: center;"><a href="user_main.php?action=edit&userid={$user.user_id}">edit</a></td>		
				<td class="cell" style=" text-align: center;"><a href="user_main.php?action=delete&userid={$user.user_id}">delete</a></td>
				<!--<div class="cell" style="float: left; text-align: center; padding: 5px 10px 5px 10px; min-width: 60px;"><a href="user_main.php?action=setrights&userid={*{$user.user_id}*}">bef&ouml;rdern</a></div>-->
				<!--<div style="text-align: right; clear: both;width: 1px;"></div>-->
				<!--</form>-->
			</tr>
		{/foreach}
		
		</table>
		
		<div style="text-align: right; clear: both;width: 1px;"></div>
			<div style="float:left;"><br>
				<a href="user_main.php?action=add">Add User</a>
			</div>
		<div style="text-align: right; clear: both;width: 1px;"></div>

	<br><br>


	
	
	
	
	
	{* ----------------- GROUP THINGS --------------------- *}
	<div>
		<div style="float:left; width: 65%">
			<table style="font-size: 12px; float:left;" cellpadding="3" cellspacing="0" width="95%">
				<tr style="background-color:#aaaaaa;">
					<td><b>ID</b></td>
					<td><b>Groupname</b></td>
					<td><b>Permissions</b></td>
					<td><b>Description</b></td>
					<td> </td>
					<td> </td>
				</tr>
				{foreach name=gr key=nr item=item from=$permgroups}
				<tr style="background-color:{cycle values="#eeeeee,#d0d0d0"}">
					<td>{$item.group_id}</td>
					<td>{$item.group_name}</td>
					<td>{$item.group_perm}</td>
					<td>{$item.group_desc}</td>
					<td><a href="user_main.php?action=editgroup&groupid={$item.group_id}">edit</a></td>
					<td><a href="user_main.php?action=delgroup&groupid={$item.group_id}">delete</a></td>
				</tr>
				{/foreach}
			</table>

		</div>
	
		{* ------ Group Use Cases ------- *}
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
			{elseif $showGroupDelForm}
				<div class="actioncontainer"  style="margin: left; border: 1px solid silver; background-color:#eeeeee; padding:8px;">
					<h4>{t}Delete Group{/t}</h4>
					{t}Do you really want to delete this group? {/t}
					<form action="" method="POST">
						<input type="hidden" name="groupid" value="{$groupinfo.group_id}">
						{t}Groupname:{/t} {$groupinfo.group_name}<br>
						{t}Permissions:{/t} {$groupinfo.group_perm}<br>
						{t}Description:{/t} {$groupinfo.group_desc}<br>
						<input type="submit" name="DeleteGroup" value="Delete Group">
					</form>
				</div>
			{elseif $showGroupEditForm}
				<div class="actioncontainer"  style="margin: left; border: 1px solid silver; background-color:#eeeeee; padding:8px;">
					<h4>{t}Edit Group{/t}</h4>
					<form action="" method="POST">
						<input type="hidden" name="groupid" value="{$groupinfo.group_id}">
						{t}Groupname:{/t} <input type="text" name="groupname" value="{$groupinfo.group_name}"><br>
						{t}Permissions:{/t} <input type="text" name="groupperm" value="{$groupinfo.group_perm}"><br>
						{t}Description:{/t} <input type="text" name="groupdesc" value="{$groupinfo.group_desc}"><br>
						<input type="submit" name="EditGroup" value="Edit Group">  <input type="reset" name="reset" value="Reset">
					</form>
				</div>
			{/if}
		</div>


			<div style="clear:both;"></div>
			<div style="text-align: left; ">
				<br><a href="user_main.php?action=addgroup" style="text-align: left; ">{t}Add new group{/t}</a><br><br><br>
			</div>
			
			<div  style="text-align:right;">
				<a href="user_main.php">Cancel Process</a>
			</div>

	</div>
	

	</div>

{include file="admin/inc.footer.tpl"}