{include file="inc/tpl/admin.header.tpl"}

<h2>{t}Admin Menu{/t}</h2>

<div id="boxMenu">

<div><a href="{$url.base}admin/mailings/admin_mailings.php"><img src="{$url.theme.shared}images/icons/mailing.png" alt="envelope icon" class="navimage" /> {t}Mailings{/t}</a> - {t}Send mailings to the entire list or to a subset of subscribers. Mailing status and history can also be viewed from here.{/t}</div>

<div><a href="{$url.base}admin/subscribers/admin_subscribers.php"><img src="{$url.theme.shared}images/icons/subscribers.png" alt="people icon" class="navimage" /> {t}Subscribers{/t}</a> - {t}Here you can list, add, delete, import, export, and update your subscribers. You can also create groups (subsets) of your subsribers from here.{/t}</div>

<div><a href="{$url.base}admin/setup/admin_setup.php"><img src="{$url.theme.shared}images/icons/settings.png" alt="hammer and screw icon" class="navimage" /> {t}Setup{/t}</a> - {t}This area allows you to configure {$app_name} and its default behavior. Set mailing list parameters, choose the information you'd like to collect from subscribers, and generate subscription forms from here.{/t}</div>


	{*corinna Display this only if plugins are activated in $pommo->_useplugins *}
	{if $showplugin}
		<div>
			<a href="{$url.base}plugins/adminplugins/adminplugins.php">
			<img src="" class="navimage" width="64" height="64" />
			{t}Setup Plugins{/t}</a> - 
			{t}Set up all the Plugins: Authentication methods, User Administration, and more...{/t}
		</div>
	{/if}
	{*corinna End additional plugin functionality *}


</div>

{include file="inc/tpl/admin.footer.tpl"}