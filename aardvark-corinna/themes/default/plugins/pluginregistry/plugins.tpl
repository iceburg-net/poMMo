{include file="admin/inc.header.tpl"}

<h1>{$returnStr}</h1>

<a class="pommoClose" href="../../admin/admin.php" style="float: left;">
	<img src="{$url.theme.shared}/images/icons/left.png" align="absmiddle" border="0">&nbsp;
	 {t}Return to Admins Page{/t}
</a><div style="clear: both; "></div>
<br>


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
	<div class="container" style="margin: left; width: 760px; ">
	
	{if $showedit}
	
		//check is not empty $data
		<div class="container" style="margin: left; border: 1px solid silver; background-color:#eeeeee; padding:8px;">
			
			<h2 style="margin: 10px; ">Setup Plugin {$plugin.plugin_uniquename}</h2>
			<form name="aForm" id="aForm" method="POST" action="">
			
				<div class="standardconf" style="margin: 10px; padding: 10px; border: 1px dashed silver;">
					<i style="min-width: 120px; float: left; width: 6em;">ID:</i> {$plugin.plugin_id}<br>
					<i style="min-width: 120px; float: left; width: 6em;">Uniquename:</i> {$plugin.plugin_uniquename}<br>
					<i style="min-width: 120px; float: left; width: 6em;">Name:</i> {$plugin.plugin_name}<br>
					<i style="min-width: 120px; float: left; width: 6em;">Desc:</i> {$plugin.plugin_desc}<br>
				</div>
				
				<div class="varconf" style="margin: 10px; padding: 5px 5px 5px 8px; border: 1px dashed silver;">
					{foreach name=data key=key item=item from=$data}
						{if $item.data_type == 'TXT'}
							<input type="hidden" name="old[]" value="{$item.data_value}">
							<label for="" style="min-width: 150px; float: left; width: 6em;">{$item.data_name}</label>
							<input style="width: 20em; display: inline;" name="plugindata[{$item.data_id}]" value="{$item.data_value}">({$item.data_type})<br>
						{elseif $item.data_type == 'ENUM'}
								{if  $dropdown}
									<label for="" style="min-width: 150px; float: left; width: 6em;">{$item.data_name}</label>
										<select size="Höhe" name="plugindata[{$item.data_id}]">
										{foreach name=opt key=ke item=it from=$dropdown}
										<option>{$it.plugin_uniquename}</option>
										{/foreach}
									</select>
								{/if}
						{/if}
					{/foreach}
				</div>
				
				<div style=" margin: 10px; ">
					<input type="submit" name="closeedit" value="Update and Close">
					<input type="reset" value="Reset">
				</div>

				
			</form>
		</div>
		<br><br>
		
	{/if}
	
	
	
	<div class="table" style="margin:0px;">
		{foreach name=aussen key=nr item=plugin from=$plugins}
		
					<div class="row" style="float:top; background-color:#eeeeee; border:1px dashed silver; margin:0px; padding: 8px; height: 30px;"> 
						
					{if $plugin.plugin_subrelation == 0}
						<img src="{$plugin.plugin_img}" style="float: left;vertical-align:bottom; height: 30px; width: 30px; margin-right: 15px;">
					{*{elseif $plugin.plugin_category }*}
					{/if}
						
						<div class="cell" style="float:left; height: 30px; text-align:left; line-height:30px;">
					{if $plugin.plugin_super == 0}
							{if $highlight == $plugin.plugin_id }<span style="backgroung-color: blue;"><i>&raquo;{/if}
									<b style="	font-size:12pt;">{$plugin.plugin_name} <i> ({$plugin.plugin_uniquename}) - {$plugin.plugin_id}/{$plugin.plugin_super}</i></b>
							{if $highlight == $plugin.plugin_id }</i><span>{/if}
							
					{else}
							{if $highlight == $plugin.plugin_id }<span style="backgroung-color: blue;"><i>&raquo;{/if}
									<b style="margin-left: 50px;">{$plugin.plugin_name} <i> ({$plugin.plugin_uniquename}) - {$plugin.plugin_id}/{$plugin.plugin_super}</i></b>
							{if $highlight == $plugin.plugin_id }</i><span>{/if}
					{/if}
						</div>
							
						
						<div class="cell" style="float: right; height: 30px; margin-left:10px; text-align: center; margin-top:3px;">
								<form style="padding: 0px; margin: 0px;">
									<input type="hidden" name="pluginid" value="{$plugin.plugin_id}">
									<input type="submit" name="edit" value="Edit this Plugin">
								</form>
								&nbsp;
						</div>
		
						<div class="cell" style="float: right; height: 30px; text-align: center;  margin-top:3px;">
								<form style="padding: 0px; margin: 0px;">
									<input type="hidden" name="pluginid" value="{$plugin.plugin_id}">
									{if $plugin.plugin_active==1}
										<b><span style="color: green">(Plugin active.)</span></b>
										<input type="hidden" name="setto" value="0">
										<input type="submit" name="switch" value="Deactivate Plugin">
										{*<a href="setup_plugin.php?pluginid={$plugin.plugin_id}&onlyactivate=true&setto=0">&raquo; deactivate</a>*}
									{elseif $plugin.plugin_active==0}
										<b><span style="color: red">(Plugin <b>not</b> active.)</span></b>
										<input type="hidden" name="setto" value="1">
										<input type="submit" name="switch" value="Activate Plugin">
										{*<a href="setup_plugin.php?pluginid={$plugin.plugin_id}&onlyactivate=true&setto=1">&#187; activate</a>*}
									{/if}
								</form>
						</div>
							
					</div>
					
	
			<div style="text-align: right; clear: both;width: 1px;"></div>
		{/foreach}
	</div>
	</div>
	<br><br>

{include file="admin/inc.footer.tpl"}
