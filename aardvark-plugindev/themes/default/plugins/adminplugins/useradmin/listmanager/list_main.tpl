{include file="inc/tpl/admin.header.tpl"}

<h2>{t}Mailing List Management Management{/t}</h2>

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

		<i>({t 1=$nrlists}%1 lists{/t})</i>

		{assign }
		{if ($nrlists <= 0) } 
			<i>No Mailing List found.</i>
		{else}
		


			{foreach key=key item=item from=$list}
	
			<div style="border: 1px solid blue">
				<div>{$item.uname} [{$item.uid}]</div>
				<div><b>Administrates</b>
				
				
				
				</div>
			<div>
			{/foreach}



		<table cellpadding="3" cellspacing="0" width="100%" style="font-size: 12px;">
			{*<tr style="text-align:center;">
				<td><b><i>{t}Listname{/t}</i> - List Description</b></td>
				<td><b>{t}Senderinfo{/t}</b></td>
				<td><b>{t}Created{/t}</b></td>
				<td><b>{t}sent mails{/t}</b><br></td>
				<td></td><td></td><td></td>
			</tr>*}
			{foreach key=key item=item from=$list}
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
									<img alt="activate plugin" src="/pommo/aardvark-development/themes/shared/images/icons/nok.png" />
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
			{/foreach}
		</table>



		{/if}	{* more than 0 list data records *}

	</div> <!-- plugincontent -->

{include file="inc/tpl/admin.footer.tpl"}