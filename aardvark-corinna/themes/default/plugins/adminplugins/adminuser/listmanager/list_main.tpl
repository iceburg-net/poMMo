{include file="admin/inc.header.tpl"}
</div>
<!-- begin content -->

<h1>{t}Responsible Persons Management{/t}</h1>

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
		

		
		<i>({t 1=$nrusers}%1 users{/t})</i>

		<table cellpadding="3" cellspacing="0" width="100%" style="font-size: 12px;">
			<tr style="text-align:center;">
				<td><b>{t}Username{/t}</b>
				</td>
				<td><b>{t}Permission Group{/t}</b>
				</td>
				<td><b>{t}# Lists{/t}</b>
				</td>
				<td><b>{t}List Info{/t}</b><br>
				</td>
				<td>&nbsp;</td>
			</tr>
			{foreach key=key item=item from=$userlist}
			<tr style="text-align:left; padding-right: 10px; background-color:{cycle values="#eeeeee,#d0d0d0"}">
				<td valign="top">
					{$item.user_name}
				</td>
				<td valign="top">{$item.user_group}&nbsp;
				</td>
				<td valign="top" style="text-align:center;">{if $item.numlist!=0}{$item.numlist}{/if}&nbsp;
				</td>
				<td>
					{if $item.lists!=""}
						<table cellspacing="0" cellpadding="0" width="100%"  style="font-size: 12px;">
							{foreach key=listkey item=listitem from=$item.lists}
							<tr>
								<td width="150px">{$listitem.list_name}&nbsp;
								</td>
								<td style="text-align:center;"><a href="list_main.php?action=edit&id={$listitem.user_id}&listid={$listitem.list_id}">edit</a>
								</td>
								<td style="text-align:center;"><a href="list_main.php?action=delete&id={$listitem.user_id}&listid={$listitem.list_id}">delete</a>
								</td>
							</tr>
							{/foreach}
						</table>
					{else}
						-
					{/if}
				</td>
				<td style="vertical-align:top; text-align:center;"><a href="list_main.php?action=add&id={$item.user_id}">&raquo;add List</a>
				</td>
			</tr>

							{if $showDelete AND $item.user_id==$id}
									<tr>
									<td colspan="5" align="right">
										<form style="margin:0px;padding:0px;" action="" method="POST">
										<input type="hidden" name="userid" value="{$item.user_id}">
											<table style="width:400px; font-size:11px;margin: left; border: 1px solid silver; background-color:#eeeeee;" style="margin:0px;padding:0px;">
												<tr>
													<td>Do you really want to delete this:</td>
													<td>id hier</td>
												</tr>
												<tr>
													<td><input type="submit" name="deleteList" value="{t}Delete List{/t}">
													</td>
												</tr>
											</table>
										</form>
									</td>
									</tr>
							{elseif $showEdit AND $item.user_id==$id}
									<tr>
									<td colspan="5" align="right">
										<form style="margin:0px;padding:0px;" action="" method="POST">
										<input type="hidden" name="userid" value="{$item.user_id}">
										
											<table style="width:400px;font-size:11px;margin: left; border: 1px solid silver; background-color:#eeeeee;" style="margin:0px;padding:0px;">
												<tr>
													<td>List Name:</td>
													<td><input type="text" name="listname" value=""></td>
												</tr>
												<tr>
													<td>List Description</td>
													<td> <input type="text" name="listdesc" value=""></td>
												</tr>
												<tr>
													<td>&nbsp;</td>
													<td>Person: schon eindeutig</td>
												</tr>
												<tr><td>&nbsp;
													<td>
														<select>
															<option name="mailgroup" value="" selected>hier die mailgruppen</option>
														</select>
													</td>
												</tr>
												<tr>
													<td><input type="submit" name="editList" value="{t}Edit List{/t}">
													</td>
												</tr>
											</table>
										</form>
									</td>
									</tr>
							{elseif $showAdd AND $item.user_id==$id}
									<tr>
									<td colspan="5" align="right">
										<form style="margin:0px;padding:0px;" action="" method="POST">
											<input type="hidden" name="userid" value="{$item.user_id}">
											<table style="width:400px;font-size:11px;margin: left; border: 1px solid silver; background-color:#eeeeee;" style="margin:0px;padding:0px;">
												<tr>
													<td>List Name:</td>
													<td><input type="text" name="listname" value=""></td>
												</tr>
												<tr>
													<td>List Description</td>
													<td> <input type="text" name="listdesc" value=""></td>
												</tr>
												<tr>
													<td>&nbsp;</td>
													<td>Person: schon eindeutig</td>
												</tr>
												<tr><td>&nbsp;
													<td>
														<select>
															<option name="mailgroup" value="" selected>hier die mailgruppen</option>
														</select>
													</td>
												</tr>
												<tr>
													<td><input type="submit" name="addList" value="{t}Add List{/t}">
													</td>
												</tr>
											</table>
										</form>
									</td>
									</tr>
							{/if}

			{/foreach}
		</table>
		<br><br>
		
		


	</div>

{include file="admin/inc.footer.tpl"}