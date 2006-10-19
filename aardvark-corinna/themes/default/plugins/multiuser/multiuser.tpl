{include file="admin/inc.header.tpl"}


</div>
<div id="mainbar">

	{*{if $showplugin}*}
	<div style="width: 500px; text-align: left;">
	<h1>{t}User menu{/t}</h1>
	
		<h2>{t}Mailing options{/t}</h2>
		
			<p>
				<a href="{$url.base}/plugins/adminplugins/adminuser/usermanagement/user_main.php">
				<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
				{t}Send Mailing / Compose Mailing{/t}</a> - 
				{t}Compose and send mailings{/t}
			</p><br>
			<p>
				<a href="{$url.base}/plugins/adminplugins/adminuser/usermanagement/user_main.php">
				<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
				{t}Mailing Queue{/t}</a> - 
				{t}List of mails to send (regarding user logged in){/t}
			</p><br>
			<p>
				<a href="{$url.base}/plugins/adminplugins/adminuser/usermanagement/user_main.php">
				<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
				{t}Mailing Lists Administration{/t}</a> - 
				{t}Your Mailing Lists with responsible persons + (Listname/Groups/responsible persons){/t}
			</p><br>
			<p>
				<a href="{$url.base}/plugins/adminplugins/adminuser/usermanagement/user_main.php">
				<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
				{t}BOUNCE management{/t}</a> - 
				{t}See rejected list of mails and actions on the subscriber wo bounced the mail{/t}
			</p><br>		
			<p>
				<a href="{$url.base}/plugins/adminplugins/adminuser/usermanagement/user_main.php">
				<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
				{t}Mailing History{/t}</a> - 
				{t}Mailings from the past{/t}
			</p><br>		
		<br>
		<h2>{t}Subscribers: Responsible Persons can add/del Subscribers and edit the groups{/t}</h2>

			<p>
				<a href="{$url.base}/plugins/adminplugins/adminuser/usermanagement/user_main.php">
				<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
				{t}View Subscribers list{/t}</a> - 
				{t}All Abbonated users{/t}
			</p><br>
			<p>
				<a href="{$url.base}/plugins/adminplugins/adminuser/usermanagement/user_main.php">
				<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
				{t}Group administration{/t}</a> - 
				{t}Group subscribers based on their parameters{/t}
			</p><br>
			<p>
				<a href="{$url.base}/plugins/adminplugins/adminuser/usermanagement/user_main.php">
				<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
				{t}Mailing Lists Administration{/t}</a> - 
				{t}Mailing Lists with their responsible persons + list of subscribers per mailinglist (Listname/Groups/responsible persons){/t}
			</p><br>
			<p>
				<a href="{$url.base}/plugins/adminplugins/adminuser/usermanagement/user_main.php">
				<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
				{t}Import subscribers{/t}</a> - 
				{t}Import subscribers from a file{/t}
			</p><br>
		</div>
	
		<br><br>
	{*{/if}*}
	</div>



{include file="admin/inc.footer.tpl"}