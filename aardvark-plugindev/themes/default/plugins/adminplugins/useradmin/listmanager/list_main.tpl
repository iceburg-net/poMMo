{include file="admin/inc.header.tpl"}

<div id="mainbar">

<h1>{t}Responsible Persons Management{/t}</h1>

	{include file="admin/inc.messages.tpl"}

		<div>
			<a class="pommoClose" href="../useradmin.php" style="float: left; line-height:18px;">
			<img src="{$url.theme.shared}/images/icons/left.png" width="21" height="21" align="absmiddle" border="0">&nbsp;
			{t}Return to User Management Menu{/t}
			</a>
		</div>
		<div style="text-align: right; clear: both;width: 1px;"></div>
		<br>

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
					{$item.name}
				</td>
				<td valign="top">{$item.perm}&nbsp;
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
								<td style="text-align:center;"><a href="list_main.php?action=edit&userid={$listitem.user_id}&listid={$listitem.list_id}">edit</a>
								</td>
								<td style="text-align:center;"><a href="list_main.php?action=delete&userid={$listitem.user_id}&listid={$listitem.list_id}">delete</a>
								</td>
							</tr>
							{/foreach}
						</table>
					{else}
						-
					{/if}
				</td>
				<td style="vertical-align:top; text-align:center;"><a href="list_main.php?action=add&userid={$item.uid}">&raquo;add List</a>
				</td>
			</tr>

							{if $showDelete AND $item.uid==$showformid}
									<tr>
									<td colspan="5" align="right">
										<form style="margin:0px;padding:0px;" action="" method="POST">
											<table style="width:400px; font-size:11px;margin: left; border: 1px solid silver; 
												background-color:#eeeeee;" style="margin:0px;padding:0px;">
												<tr>
													<td align="center" colspan="2"><h3>{t}Delete mailing list{/t}</h3></td>
												</tr>
												<tr>
													<td colspan="2">Do you really want to delete this:</td>
												</tr>
												<tr>
													<td>Listname: <b>{$listdata.list_name}</b><br>
														Userid: {$listdata.user_id} / Listid: {$listdata.list_id}
													</td>
												</tr> 
												<tr>
													<td><input type="submit" name="deleteList" value="{t}Delete List{/t}">
													</td>
												</tr>
											</table>
										</form>
									</td>
									</tr>
							{elseif $showEdit AND $item.uid==$showformid}
									<tr>
									<td colspan="5" align="right">
										<form style="margin:0px;padding:0px;" action="" method="POST">
										{*<input type="hidden" name="userid" value="{$item.user_id}">*}
										
											<table style="width:400px;font-size:11px;margin: left; border: 1px solid silver; 
											background-color:#eeeeee;" style="margin:0px;padding:0px;">
												<tr>
													<td align="center" colspan="2"><h3>{t}Edit mailing list{/t}</h3></td>
												</tr>
												<tr>
													<td>List Name:</td>
													<td><input type="text" name="listname" value="{$listdata.list_name}"></td>
												</tr>
												<tr>
													<td>List Description</td>
													<td> <input type="text" name="listdesc" value="{$listdata.list_desc}"></td>
												</tr>
												<tr>
													<td>&nbsp;</td>
													<td>Person: {$listdata.user_id}</td>
												</tr>
												<tr><td>&nbsp;
													<td>
														<select>
															{foreach key=groupkey item=groupitem from=$mailgroups}
															<option name="mailgroup" value="{$groupitem}" {if $groupitem==$mailgroup}selected{/if}>{$groupitem}</option>
															{/foreach}
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
							{elseif $showAdd AND $item.uid==$showformid}
									<tr>
									<td colspan="5" align="right">
										<form style="margin:0px;padding:0px;" action="" method="POST">
											<table style="width:400px;font-size:11px;margin: left; border: 1px solid silver; background-color:#eeeeee;" style="margin:0px;padding:0px;">
												<tr>
													<td align="center" colspan="2"><h3>{t}Add a new mailing list{/t}</h3></td>
												</tr>
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
															{foreach key=groupkey item=groupitem from=$mailgroups}
															<option name="mailgroup" value="{$groupitem}" {if $groupitem==$mailgroup}selected{/if}>{$groupitem}</option>
															{/foreach}
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
	


	</div>

{include file="admin/inc.footer.tpl"}