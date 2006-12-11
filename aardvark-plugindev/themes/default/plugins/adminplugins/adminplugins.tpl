{include file="admin/inc.header.tpl"}

<div id="mainbar">

		<h1>{t}Plugin Menu{/t}</h1>
		
		<div>
			<a class="pommoClose" href="../../" style="float: left; line-height:18px; " >
			<img src="{$url.theme.shared}/images/icons/left.png" width="21" height="21" align="absmiddle" border="0">&nbsp;
			{t}Return to Main Menu{/t}
			</a>
		</div>
		<div style="text-align: right; clear: both;width: 1px;"></div>		
		
		
		<p>
			<a href="{$url.base}plugins/adminplugins/useradmin/useradmin.php">
			<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
			{t}Multiuser & Authentication, Lists & Responsible Persons{/t}</a> - 
			{t}Plugins USING THINGS like Add a User (text?) Beschreibung Beschreibung Beschreibung Beschreibung Beschreibung Beschreibung {/t}
		</p>
		<p>
			<a href="{$url.base}plugins/adminplugins/pluginconfig/config_main.php">
			<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
			{t}GENERAL PLUGIN SETUP{/t}</a> - 
			{t}Plugin 'connections' setup. (text?) The standard values for LDAP, DB, Auth, Bounce Server, if you want to use special features like mailing queue or not -- go here {/t}
		</p>

</div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}