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
			<i style="align:center;">(Einstellungparameter: {$nrplugins})</i>
			
			
			{foreach key=key item=plugitem from=$plugins}
			<div class="plugins">
			
				{if $mom!=$plugitem.category}
					{assign var="mom" value=$plugitem.category}
					<div style="background-color:{cycle values="#eeeeee,#d0d0d0"};">
						<b>{$mom}</b>&nbsp;&nbsp; <a href="plugin_main.php?switchcid={$plugitem.cid}&active=0" style="font-size:10px;">
						&raquo;deactivate</a>
				{/if}
				
				
					{if $plugitem.pid == NULL}
							<div style="text-align:center;">{t}No plugins detected in this category{/t}</div>
					
					{else}
				
				
							<div style="border: 1px solid blue; background-color:inherit;">
								{$plugitem.pid}<br><b>{$plugitem.uniquename}</b><br>
								{$plugitem.name}<br>
								{$plugitem.desc}<br>
								{$plugitem.version}<br>
								{if $plugitem.pactive==1}
									<a href="plugin_main.php?action=switch&active=0&switchid={$plugitem.pid}">
										<img width="20" height="20" border="0" src="{$url.theme.shared}/corinna/active.png"></a>
								{else}
									<a href="plugin_main.php?action=switch&active=1&switchid={$plugitem.pid}">
										<img width="20" height="20" border="0" src="{$url.theme.shared}/corinna/inactive.png"></a>
								{/if}
								
								<form action="" method="POST" style="padding:0px;margin:0px;">
									<input type="hidden" name="setupid" value="{$plugitem.pid}">
									<input type="submit" name="viewsetup" value="view Plugin Setup">
								</form>
								
										{if ($viewsetup AND $plugitem.pid==$setupid)}
									
											{if $plugsetup == NULL}
												<div style="text-align:center">{t}No setup for this plugin{/t}</div>
											{else}
											
												<div>einstellungen plugin a</div>
											
											{/if}
								
										{/if}
										
							</div>
				
					{/if}
					
				{if $mom!=$plugitem.category}
				</div>
				{/if}
			
				
			</div>
			{/foreach}
		
		
		{/if} {*if nrplugins > 1*}
		

		
<button class="edit tsToggleEdit">
<img src="/pommo/aardvark-development/themes/shared/images/icons/yes.png"/>
</button>
		
<button onclick="window.location.href='groups_edit.php?group_id=1'; return false;">
<img alt="edit icon" src="/pommo/aardvark-development/themes/shared/images/icons/edit.png"/>
</button>

<button onclick="window.location.href='/pommo/aardvark-development/admin/subscribers/subscribers_groups.php?group_id=1&delete=TRUE'; return false;">
<img alt="delete icon" src="/pommo/aardvark-development/themes/shared/images/icons/delete.png"/>
</button>			

							


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