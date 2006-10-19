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



{* -------------------------- USE CASES ----------------------------------- *}
	
	{if $action == 'adduser'}

		<br>
		<div class="container" style="margin: left; width: 760px;">

			<form method="POST" action="">
				<table>
					<tr>
						<td>Username:</td>
						<td><input type="text" name="username"></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><input type="password" name="userpass"></td>
					</tr>
					<tr>
						<td>Retype password:</td>
						<td><input type="password" name="userpasscheck"></td>
					</tr>
					<tr>
						<td>Group:</td>
						<td><input type="select" name="usergroup"></td>
					</tr>
					<tr>
						<td>Rights:</td>
						<td>
							<input type="checkbox" name="userperm">send &nbsp; <br>
							<input type="checkbox" name="userperm">edit articles &nbsp;<br>
							<input type="checkbox" name="userperm">another right<br>
						</td>
					</tr>
					<tr>
						<td colspan="2"><input type="submit" name="AddUser" value="Add User">  <input type="reset" name="reset" value="Reset"></td>
					</tr>
				</table>
			</form>

		</div>

	{/if}
	
	{if $action == 'adduser'}
	
		<form action="" method="POST">
			Do you really want to delete this user: {$userid}
			<input type="submit" name="DeleteUser" value="Delete">
		</form>
	
	{/if}

{* --------------------------- show USER Matrix ---------------------------- *}


	<div class="container" style="margin: left; width: 760px;">
	
	
		<div style="padding: 12px;">
			<a class="pommoClose" href="../../admin/admin.php" style="float: left;">
			<img src="{$url.theme.shared}/images/icons/left.png" align="absmiddle" border="0">&nbsp;
			{t}Return to Admins Page{/t}
			</a>
		</div>
		<div style="text-align: right; clear: both;width: 1px;"></div>
		<br>
	
		<div class="table" style="margin:0px; padding: 12px;">

			<div class="row" style="float:top;background-color:{cycle values="#eeeeee,#d0d0d0"}">
				<div class="cell" style="float: left; text-align: center; padding: 5px 10px 5px 10px; min-width: 15px;"><b>ID</b></div>
				<div class="cell" style="float: left; text-align: left; padding: 5px 10px 5px 10px; min-width: 100px;"><b>Name</b></div>
				<div class="cell" style="float: left; text-align: left; padding: 5px 10px 5px 10px; min-width: 100px;"><b>Pass: to md5</b></div>
				<div class="cell" style="float: left; text-align: left; padding: 5px 10px 5px 10px; min-width: 100px;"><b>Group</b></div>

				<div style="text-align: right; clear: both;width: 1px;"></div>
			</div>
		
		{foreach name=aussen key=nr item=user from=$user}
			<div class="row" style="float:top;background-color:{cycle values="#eeeeee,#d0d0d0"}">
				<div class="cell" style="float: left; text-align: center; padding: 5px 10px 5px 10px; min-width: 15px;">{$user.user_id}</div>
				<div class="cell" style="float: left; text-align: left; padding: 5px 10px 5px 10px; min-width: 100px;">{$user.user_name}</div>
				<div class="cell" style="float: left; text-align: left; padding: 5px 10px 5px 10px; min-width: 100px;">{$user.user_pass}</div>
				<div class="cell" style="float: left; text-align: left; padding: 5px 10px 5px 10px; min-width: 100px;">{$user.user_group}</div>
				<div class="cell" style="float: left; text-align: center; padding: 5px 10px 5px 10px; min-width: 60px;"><a href="user_main.php?action=edit&userid={$user.user_id}">edit</a></div>		
				<div class="cell" style="float: left; text-align: center; padding: 5px 10px 5px 10px; min-width: 60px;"><a href="user_main.php?action=delete&userid={$user.user_id}">delete</a></div>
				<div class="cell" style="float: left; text-align: center; padding: 5px 10px 5px 10px; min-width: 60px;"><a href="user_main.php?action=setrights&userid={$user.user_id}">bef&ouml;rdern</a></div>
				<div class="cell" style="float: left; text-align: center; padding: 5px 10px 5px 10px; min-width: 60px;"><a href="">eine option</a></div>		
				<div style="text-align: right; clear: both;width: 1px;"></div>
			</div>
		{/foreach}
			<div style="float:left;"><br><a href="user_main.php?action=adduser">&raquo; Add New User</a></div>
			<div style="text-align: right; clear: both;width: 1px;"></div>
		
		</div>

	<br><br>

	</div>



{include file="admin/inc.footer.tpl"}