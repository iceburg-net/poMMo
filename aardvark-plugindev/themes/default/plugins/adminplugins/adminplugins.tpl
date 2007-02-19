{include file="inc/admin.header.tpl"}

<h2>{t}Plugin Menu{/t}</h2>

<div id="boxMenu">

	<div>
		<a href="{$url.base}plugins/adminplugins/useradmin/useradmin.php">
		<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
		{t}Multiuser & Authentication, Lists & Responsible Persons{/t}</a> - 
		{t}Plugins USING THINGS like Add a User (text?) Beschreibung Beschreibung Beschreibung Beschreibung Beschreibung Beschreibung {/t}
	</div>
	
	
	<div>
		<a href="{$url.base}plugins/adminplugins/pluginconfig/config_main.php">
		<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
		{t}Bounce Mailbox{/t}</a> - 
		{t}Manage and see the bounce mailbox. {/t}
	</div>
	
	
	<div>
		<a href="{$url.base}plugins/adminplugins/pluginconfig/config_main.php">
		<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
		{t}GENERAL PLUGIN SETUP{/t}</a> - 
		{t}Plugin 'connections' setup. (text?) The standard values for LDAP, DB, Auth, Bounce Server, if you want to use special features like mailing queue or not -- go here {/t}
	</div>

</div>

{include file="inc/admin.footer.tpl"}