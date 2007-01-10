<div id="sidebar">

<ul id="nav">
<li><a href="{$url.base}admin/mailings/admin_mailings.php">{t}Mailings{/t}</a>
	{if $section == "mailings"}
	<ul>
	<li><a href="mailings_send.php">{t}Send{/t}</a></li>
	<li><a href="mailings_history.php">{t}History{/t}</a></li>
	</ul>
	{/if}
</li>
<li><a href="{$url.base}admin/subscribers/admin_subscribers.php">{t}Subscribers{/t}</a>
	{if $section == "subscribers"}
	<ul>
	<li><a href="subscribers_manage.php">{t}Manage{/t}</a></li>
	<li><a href="subscribers_import.php">{t}Import{/t}</a></li>
	<li><a href="subscribers_groups.php">{t}Groups{/t}</a></li>
	</ul>
	{/if}
</li>
<li><a href="{$url.base}admin/setup/admin_setup.php">{t}Setup{/t}</a>
	{if $section == "setup"}
	<ul>
	<li class="advanced"><a href="setup_configure.php">{t}Configure{/t}</a></li>
	<li><a href="setup_fields.php">{t}Fields{/t}</a></li>
	<li><a href="setup_form.php">{t}Setup Form{/t}</a></li>
	</ul>
	{/if}
</li>


{*corinna TODO: remove!!! DIRTY!!!! *}
{*{if $showplugin}*}
<li><a href="{$url.base}plugins/adminplugins/adminplugins.php">{t}Plugins{/t}</a>
	{if $section == "plugins/adminplugins"}
		<ul>
			<li><a href="useradmin/useradmin.php">{t}Administration Tasks{/t}</a></li>
			<li><a href="pluginconfig/config_main.php">{t}General Plugin Setup{/t}</a></li>
		</ul>
	{/if}
	{if $section == "plugins/adminplugins/pluginconfig"}
		<ul>
			<li><a href="../useradmin/useradmin.php">{t}Administration Tasks{/t}</a></li>
			<li><a href="config_main.php">{t}General Plugin Setup{/t}</a></li>
		</ul>
	{/if}
	{if $section == "plugins/adminplugins/useradmin"}
	<ul>
		<li><a href="useradmin.php">{t}Administration Tasks{/t}</a>
			<ul>
				<li style="margin-left: 12px; background-color: #FFF;"><a href="listmanager/list_main.php">{t}Mailing Lists{/t}</a></li>
				<li style="margin-left: 12px; background-color: #FFF;"><a href="usermanager/user_main.php">{t}User Administration{/t}</a></li>
				<li style="margin-left: 12px; background-color: #FFF;"><a href="respmanager/resp_main.php">{t}Responsible Persons{/t}</a></li>
			</ul>		
		</li>
		<li><a href="../pluginconfig/config_main.php">{t}General Plugin Setup{/t}</a></li>
	</ul>
	{/if}
	{if $section == "plugins/adminplugins/useradmin/listmanager"}
	<ul>
		<li><a href="../useradmin.php">{t}Administration Tasks{/t}</a>
			<ul>
				<li style="margin-left: 12px; background-color: #FFF;"><a href="list_main.php">{t}Mailing Lists{/t}</a></li>
				<li style="margin-left: 12px; background-color: #FFF;"><a href="../usermanager/user_main.php">{t}User Administration{/t}</a></li>
				<li style="margin-left: 12px; background-color: #FFF;"><a href="../respmanager/resp_main.php">{t}Responsible Persons{/t}</a></li>
			</ul>		
		</li>
		<li><a href="../../pluginconfig/config_main.php">{t}General Plugin Setup{/t}</a></li>
	</ul>
	{/if}	
	{if $section == "plugins/adminplugins/useradmin/usermanager"}
	<ul>
		<li><a href="../useradmin.php">{t}Administration Tasks{/t}</a>
			<ul>
				<li style="margin-left: 12px; background-color: #FFF;"><a href="../listmanager/list_main.php">{t}Mailing Lists{/t}</a></li>
				<li style="margin-left: 12px; background-color: #FFF;"><a href="user_main.php">{t}User Administration{/t}</a></li>
				<li style="margin-left: 12px; background-color: #FFF;"><a href="../respmanager/resp_main.php">{t}Responsible Persons{/t}</a></li>
			</ul>		
		</li>
		<li><a href="../../pluginconfig/config_main.php">{t}General Plugin Setup{/t}</a></li>
	</ul>
	{/if}	
	{if $section == "plugins/adminplugins/useradmin/respmanager"}
	<ul>
		<li><a href="../useradmin.php">{t}Administration Tasks{/t}</a>
			<ul>
				<li style="margin-left: 12px; background-color: #FFF;"><a href="../listmanager/list_main.php">{t}Mailing Lists{/t}</a></li>
				<li style="margin-left: 12px; background-color: #FFF;"><a href="../usermanager/user_main.php">{t}User Administration{/t}</a></li>
				<li style="margin-left: 12px; background-color: #FFF;"><a href="resp_main.php">{t}Responsible Persons{/t}</a></li>
			</ul>		
		</li>
		<li><a href="../../pluginconfig/config_main.php">{t}General Plugin Setup{/t}</a></li>
	</ul>
	{/if}	
</li>
{*{/if}*}
{*corinna*}

</ul>

<div class="extra">

{if $config.demo_mode == "on"}
<p><img src="{$url.theme.shared}images/icons/demo.png" alt="Key icon" class="sideimage" />{t}Demonstration mode is ON.{/t}</p>

{else}

<p><img src="{$url.theme.shared}images/icons/nodemo.png" alt="World icon" class="sideimage" />{t}Demonstration mode is OFF.{/t}</p>
{/if}

</div>

</div>