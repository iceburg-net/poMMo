{include file="admin/inc.header.tpl"}
</div>
<!-- begin content -->

<div id="mainbar" style="width:700px; text-align:left;">


	<h1>{t}General Plugin Setup (other name here){/t}</h1>

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


		<div>
			<a class="pommoClose" href="../adminplugins.php" style="float: left; line-height:18px; " >
			<img src="{$url.theme.shared}/images/icons/left.png" width="21" height="21" align="absmiddle" border="0">&nbsp;
			{t}Return to Plugin Menu{/t}
			</a>
		</div>
		<div style="text-align: right; clear: both;width: 1px;"></div>
		<br>
		
		
		<!--CONTENT-->
		

		
		{if $nrplugins<1}
			
			<p align="center">
			<b><i>No Plugin activated</i></b>
			</p>
			
		{else}
		
		<h2>{t}Active Plugins{/t}</h2>
			<i>(Einstellungparameter: {$nrplugins})</i>
		
		<table cellpadding="3" cellspacing="1" style="font-size:11px;" width="100%">
			<tr style="text-align: center; background-color:#aaaaaa">
				<td width="50px"><b>Category</b></td>
				<td><b>ID</b></td>
				<td><b>Uniquename</b></td>
				<td><b>Name</b></td>
				<td><b>Description</b></td>
				<td><b>active</b></td>
				<td><b>Version</b></td>
				<td>&nbsp;</td>
			</tr>
			{foreach key=key item=plugitem from=$plugins}

				{if $mom!=$plugitem.category}
						{assign var="mom" value=$plugitem.category}
						<tr>
							<td colspan="8" align="left" style="padding-top:10px; border-top:1px solid blue; border-left:1px solid blue;  border-right:1px solid blue; color: black;">
								<b>{$mom}</b>&nbsp;&nbsp; <a href="plugin_main.php?switchcid={$plugitem.cid}&active=0" style="font-size:10px;">&raquo;deactivate</a> 
							</td>
						</tr>
						
				{/if}


				{if $plugitem.pid == NULL}
				
						<tr>
							<td colspan="8" align="center" style="border-left:1px solid blue; border-right: 1px solid blue;">{t}No plugins detected in this category{/t}</td>
						</tr>
				
				{else}
				
				
							<tr valign="top" style="background-color:{cycle values="#eeeeee,#d0d0d0"};">
								<td style="background-color:#ffffff; border-left:1px solid blue;">&nbsp;</td>
								<td>{$plugitem.pid}</td>
								<td>{$plugitem.uniquename}</td>
								<td>{$plugitem.name}</td>
								<td>{$plugitem.desc}</td>
								<td>	{if $plugitem.pactive==1}
											<a href="plugin_main.php?action=switch&active=0&switchid={$plugitem.pid}"><img width="20" height="20" border="0" src="{$url.theme.shared}/corinna/active.png"></a>
										{else}
											<a href="plugin_main.php?action=switch&active=1&switchid={$plugitem.pid}"><img width="20" height="20" border="0" src="{$url.theme.shared}/corinna/inactive.png"></a>
										{/if}</td>
								<td align="center">{$plugitem.version}</td>
								<td style=" border-right:1px solid blue;"><form action="" method="POST" style="padding:0px;margin:0px;">
										<input type="hidden" name="setupid" value="{$plugitem.pid}">
										<input type="submit" name="viewsetup" value="view Plugin Setup">
									</form>
								</td>
							</tr>
							
							{if ($viewsetup AND $plugitem.pid==$setupid)}
							
								{if $plugsetup == NULL}
									<tr><td colspan="8" style="border-left: 1px solid blue; border-right: 1px solid blue;">
										<table cellpadding="3" cellspacing="0" width="80%" align="right">
											<tr><td align="center" style="margin-bottom: 10px; border: 1px solid silver; background-color:#eeeeee; 
											margin-bottom:10px;">No setup for this plugin</td></tr>
										</table>
									</td></tr>
								{else}
								<tr>
									<td style="border-left:1px solid blue;">&nbsp;
									</td>
									<td colspan="7" style="padding:0px; margin:0px; border-right:1px solid blue;">
									
										<form action="" method="POST" style="padding:0px; margin:0px;">
										<input type="hidden" name="changeid" value="{$plugitem.pid}">
										<!--margin-left:50px;-->
													<table width="80%" style="font-size: 11px; padding:0px;border: 1px solid silver; 
														background-color:#eeeeee; margin-bottom:10px;" cellpadding="3" cellspacing="0" align="right">
														<tr>
															<td><b>Current Setup</b>
															</td>
														</tr>
														<tr>
															<td>This plugin is currently: &lt;{if $plugitem.pactive==0}<b>inactive</b>{elseif $plugitem.pactive==1}<b>active</b>{/if}&gt; 
															{*If you want to change this: 
																<input id="active1" type="radio" name="active" value="1" {if $plugitem.pactive==1}checked{/if}>
																	<label for="active1">active</label>
																<input id="active2" type="radio" name="active" value="0" {if $plugitem.pactive==0}checked{/if}>
																	<label for="active2">inactive</label>*}
															</td>
														</tr>
														<tr>
															<td>
															{foreach name=data key=key item=item from=$plugsetup}
																{if $item.data_type == 'TXT' OR $item.data_type == 'NUM' OR $item.data_type == 'BOOL'}
																	<input type="hidden" name="old[]" value="{$item.data_value}">
																	<label for="" style="min-width: 150px; float: left; width: 6em;">{$item.data_name}</label>
																	<input style="width: 20em; display: inline;" 
																		name="plugindata[{$item.data_id}]" value="{$item.data_value}">({$item.data_type})<br>
																{*{elseif $item.data_type == 'ENUM'}
																		{if  $dropdown}
																			<label for="" style="min-width: 150px; float: left; width: 6em;">{$item.data_name}</label>
																				<select size="Höhe" name="plugindata[{$item.data_id}]">
																				{foreach name=opt key=ke item=it from=$dropdown}
																				<option>{$it.plugin_uniquename}</option>
																				{/foreach}
																			</select>
																		{/if}*}
																{/if}
															{/foreach}
															</td>
														</tr>
														<tr>
															<td><input type="submit" name="changesetup" value="Change"></td>
														</tr>
														
													</table>
													
										</form>
										<br>
									</td>
									</tr>
									{/if}
								
								
							{/if}
					{/if}		

			{/foreach}

				<tr>
						<td height="1" colspan="8" style="margin:0px; padding:0px; height:1px; border-bottom:1px solid blue; background-color: blue;"></td>
				</tr>		
		
		</table>
		
		{/if}

		<br>
		
		<h2>{t}Inactive Categories{/t}</h2>
		Click the cativate button to enable the use of a plugin category ans its various subplugins:<br>
		<table cellpadding="3" cellspacing="0" border="0" width="100%">
			{foreach key= key item=item from=$inactive}
			<tr style="background-color: darkblue; border: 1px solid white; color: white; font-size:12px;">
				<td>{$item.cid}</td>
				<td><b>{$item.name}</b></td>
				<td>{$item.desc}</td>
				<td><a href="plugin_main.php?switchcid={$item.cid}&active=1" style="font-size:10px;">&raquo; activate tools / use tools (button?)</a></td>
			</tr>
			{/foreach}
		</table>



	</div>
	<br><br>

</div>
{include file="admin/inc.footer.tpl"}




		{*
		
		<h2>{t}Categories{/t}</h2>
		<table cellpadding="0" cellspacing="0" border="0">
			{foreach key= key item=item from=$categories}
			<tr style="background-color: darkblue; border: 1px solid white; color: white;">
				<td>{$item.cid}</td>
				<td>{$item.name}</td>
				<td>{$item.cactive}</td>
			</tr>
			{/foreach}
		</table>

		<h2>{t}Active Categories{/t}</h2>
		<table>
			{foreach key= key item=item from=$active}
			<tr style="background-color: darkblue; border: 1px solid white; color: white;">
				<td>{$item.cid}</td>
				<td>{$item.name}</td>
				<td>{$item.cactive}</td>
			</tr>
			{/foreach}
		</table>
		
		<h2>{t}Inactive Categories{/t}</h2>
		<table>
			{foreach key= key item=item from=$inactive}
			<tr style="background-color: darkblue; border: 1px solid white; color: white;">
				<td>{$item.cid}</td>
				<td>{$item.name}</td>
				<td>{$item.cactive}</td>
			</tr>
			{/foreach}
		</table>
		*}

		{*
		JOIN: <table>
		{foreach key=key item=item from=$plugins}
			<tr>
				<td>{$item.pid}</td>
				<td>{$item.uniquename}</td>
				<td>{$item.name}</td>	
				<td>{$item.desc}</td>	
				<td>{$item.pactive}</td>	
				<td>{$item.version}</td>	
				<td>{$item.category}</td>	
				<td>{$item.cactive}</td>	
			</tr>
		{/foreach}
		</table>
		*}