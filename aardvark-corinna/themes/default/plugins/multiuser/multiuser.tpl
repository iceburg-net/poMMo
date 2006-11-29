{include file="admin/inc.header.tpl"}


</div>
<!--<div id="submenu">&raquo;submenu?
</div>-->
<div id="mainbar">


	{*{if $showplugin}*}
	
	
	<div style="width: 600px; text-align: left;"> {*WAS 500px*}
	<h1>{t}User menu{/t}</h1>

		<div style="margin: 7px; padding:3px;">
			{*TODO %1 in t tag *}{t}Welcome, {$user}. (logintime?). Your options: {/t}<br><br>
		</div>


	{if $perm!=""}
	
		{if $options}
		<h3>{t}Mailing options{/t}</h3>
		{/if}
		
			{if $compose}
				<div style="float:top; height:40px; margin: 7px; padding:3px;">
					<a href="{$url.base}/admin/mailings/mailings_send.php">
					<img src="" class="navimage" width="20" height="20" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
					{t}Compose Mailing{/t}</a> - 
					{t}Compose and send mailings{/t}
				</div>
			{/if}

			{if $send}
				<div style="float:top; height:40px; margin: 7px; padding:3px;">
					<a href="{$url.base}/plugins/old/mailingqueue/queue_main.php">
					<img src="" class="navimage" width="20" height="20" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
					{t}Send Mailing from Mailing Queue{/t}</a> - 
					{t}List of mails to send (regarding user logged in){/t}
				</div>
			{/if}
			
			{if $history}
				<div style="float:top; height:40px; margin: 7px; padding:3px;">
					<a href="{$url.base}/admin/mailings/mailings_history.php">
					<img src="" class="navimage" width="20" height="20" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
					{t}Mailing History{/t}</a> - 
					{t}Mailings from the past{/t}
				</div>	
			{/if}

			{if $bounce}
				<div style="float:top; height:40px; margin: 7px; padding:3px;">
					<a href="{$url.base}/plugins/multiuser/bounce/bounce_main.php">
					<img src="" class="navimage" width="20" height="20" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
					{t}BOUNCE POP Postfach{/t}</a> - 
					{t}Bpounce folder{/t}
				</div>
			{/if}
			
			
		{if $admin}
		<h3>{t}Administration Tasks{/t}</h3>
		{/if}
			
			{if $maillists}
				<div style="float:top; height:40px; margin: 7px; padding:3px;">
					<a href="{$url.base}/plugins/adminplugins/adminuser/listmanager/list_main.php">
					<img src="" class="navimage" width="20" height="20" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
					{t}Mailing Lists Administration{/t}</a> - 
					{t}Your Mailing Lists with responsible persons + (Listname/Groups/responsible persons){/t}
				</div>
			{/if}
			
			{if $bounce}
				<div style="float:top; height:40px; margin: 7px; padding:3px;">
					<a href="{$url.base}/plugins/adminplugins/adminbounce/bounce_main.php">
					<img src="" class="navimage" width="20" height="20" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
					{t}BOUNCE management{/t}</a> - 
					{t}See rejected list of mails and actions on the subscriber wo bounced the mail{/t}
				</div>
			{/if}

			{if $useradmin}
				<div style="float:top; height:40px; margin: 7px; padding:3px;">
					<a href="{$url.base}/plugins/adminplugins/adminuser/usermanager/user_main.php">
					<img src="" class="navimage" width="20" height="20" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
					{t}User Administration{/t}</a> - 
					{t}See Users and change{/t}
				</div>
			{/if}

			{if $subscribers}
				<div style="float:top; height:40px; margin: 7px; padding:3px;">
					<a href="{$url.base}/admin/subscribers/subscribers_manage.php">
					<img src="" class="navimage" width="20" height="20" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
					{t}View Subscribers list{/t}</a> - 
					{t}All Abbonated users{/t}
				</div>
			{/if}
			
			{if $groups}
				<div style="float:top; height:40px; margin: 7px; padding:3px;">
					<a href="{$url.base}/admin/subscribers/groups_edit.php">
					<img src="" class="navimage" width="20" height="20" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
					{t}Group administration{/t}</a> - 
					{t}Group subscribers based on their parameters{/t}
				</div>
			{/if}
			
			{if $maillists}
				<div style="float:top; height:40px; margin: 7px; padding:3px;">
					<a href="{$url.base}/plugins/adminplugins/adminuser/">
					<img src="" class="navimage" width="20" height="20" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
					{t}Mailing Lists Administration{/t}</a> - 
					{t}Mailing Lists with their responsible persons + list of subscribers per mailinglist (Listname/Groups/responsible persons){/t}
				</div>
			{/if}
			
			{*
				<div style="float:top; height:40px; margin: 7px; padding:3px;">
					<a href="{$url.base}/plugins/adminplugins/adminuser/usermanagement/user_main.php">
					<img src="" class="navimage" width="20" height="20" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
					{t}Import subscribers{/t}</a> - 
					{t}Import subscribers from a file{/t}
				</div>
			*}
			
			
			{if $blah}
				COW LEVEL DETECTED<br>
			{/if}
			
		</div>
	
		<br><br>
	{/if}
	</div>



{include file="admin/inc.footer.tpl"}