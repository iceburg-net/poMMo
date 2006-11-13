{include file="admin/inc.header.tpl"}
</div>
<!-- begin content -->

<h1>{t}poMMo Authentication Method Manager{/t}</h1>

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


		<div style="text-align: center;">
			Current authentication method is &lt;<b style="color: green;">{$currentmethod}</b>&gt;<br>
			You can choose between following authentication methods:<br><br>
		</div>


	
		<div>
			<a class="pommoClose" href="../adminuser.php" style="float: left;">
			<img src="{$url.theme.shared}/images/icons/left.png" align="absmiddle" border="0">&nbsp;
			{t}Return to User Management Menu{/t}
			</a>
		</div>
		<div style="text-align: right; clear: both;width: 1px;"></div>
		<br>
		

	
	<table style="font-size: 11px;" cellpadding="3" cellspacing="0">
		{foreach nr=nr item=item from=$authmethods}
			<tr valign="top" style="background-color:{cycle values="#eeeeee,#d0d0d0"}">
				<td>
					{if $item.uniquename==$currentmethod}<span style="color: green"><b>{/if}
						{$item.uniquename}
					{if $item.uniquename==$currentmethod}</b></span>{/if}
				</td>
				<td>{if $item.uniquename==$currentmethod}<span style="color: green"><b>{/if}
						{$item.name}
					{if $item.uniquename==$currentmethod}</b></span>{/if}
				</td>
				<td>
					{$item.desc}
				</td>
				<td>
					<form action="" method="POST" style="padding: 0px; margin:0px;">
						<input type="hidden" name="setupid" value="{$item.id}">
						<input type="submit" name="viewsetup" value="View Setup">
					</form>
				</td>
				{if ($viewsetup AND $item.id==$setupid)}
				<tr>
					<td colspan="5" style="padding:0px; margin:0px;">
					
						<form action="" method="POST" style="padding:0px; margin:0px;">
						<input type="hidden" name="changeid" value="{$item.id}">
						
									<table width="100%" style="margin-left:50px; font-size: 11px; padding:0px;border: 1px solid silver; 
										background-color:#eeeeee;" cellpadding="3" cellspacing="0">
										<tr>
											<td><b>Current Setup</b>
											</td>
										</tr>
										<tr>
											<td>This plugin is currently: &lt;{if $item.active==0}<b>inactive</b>{elseif $item.active==1}<b>active</b>{/if}&gt; 
											If you want to change this: 
												<input id="active1" type="radio" name="active" value="1" {if $item.active==1}checked{/if}>
													<label for="active1">active</label>
												<input id="active2" type="radio" name="active" value="0" {if $item.active==0}checked{/if}>
													<label for="active2">inactive</label>
											</td>
										</tr>
										<tr>
											<td>
											{foreach name=data key=key item=item from=$authsetup}
												{if $item.data_type == 'TXT'}
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
			</tr>
		{/foreach}
		
	</table>
	

	<br><br>

{include file="admin/inc.footer.tpl"}