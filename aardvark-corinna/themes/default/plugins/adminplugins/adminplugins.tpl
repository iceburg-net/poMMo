{include file="admin/inc.header.tpl"}
</div><!--TODO: currently no sidebar-->

<div id="mainbar" style="width:700px; text-align:left;">

	{*{if $showplugin}*}
		<h1>{t}Plugin Menu ??(other name here){/t}</h1>
		
		<div>
			<a class="pommoClose" href="../../" style="float: left; line-height:18px; " >
			<img src="{$url.theme.shared}/images/icons/left.png" width="21" height="21" align="absmiddle" border="0">&nbsp;
			{t}Return to Main Menu{/t}
			</a>
		</div>
		<div style="text-align: right; clear: both;width: 1px;"></div>		
		
		
		<p>
			<a href="{$url.base}/plugins/adminplugins/useradmin/adminuser.php">
			<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
			{t}Multiuser & Authentication, Lists & Responsible Persons{/t}</a> - 
			{t}Plugins USING THINGS like Add a User (text?) Beschreibung Beschreibung Beschreibung Beschreibung Beschreibung Beschreibung {/t}
		</p><br>
		<p>
			<a href="{$url.base}/plugins/adminplugins/pluginsetup/plugin_main.php">
			<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
			{t}GENERAL PLUGIN SETUP{/t}</a> - 
			{t}Plugin 'connections' setup. (text?) The standard values for LDAP, DB, Auth, Bounce Server, if you want to use special features like mailing queue or not -- go here {/t}
		</p><br>


		<br><br>
	{*{/if}*}
	
	
	
	{*<!--<br><br>
			<p style="width:30%; font-size:7px;"><b>WEG!!!</b>
			<a href="{$url.base}/plugins/adminplugins/adminbounce/bounce_main.php">
			<img src="" class="navimage" width="10" height="10" /><!-- src="{$url.theme.shared}/images/icons/subscribersa.png"-->
			{t}Bounce Mail Settings{/t}</a> - 
			{t}In Arbeit - I am working on it {/t}
		</p><br>-->*}

</div>



{include file="admin/inc.footer.tpl"}