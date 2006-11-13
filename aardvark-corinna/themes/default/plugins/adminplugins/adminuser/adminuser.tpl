{include file="admin/inc.header.tpl"}
</div><!--TODO: currently no sidebar-->

<div id="mainbar" style="width:700px; text-align:left;">

	{*{if $showplugin}*}
		<h1>{t}User management menu{/t}</h1>
		
		<div style="padding: 12px;">
			<a class="pommoClose" href="../adminplugins.php" style="float: left;">
			<img src="{$url.theme.shared}/images/icons/left.png" align="absmiddle" border="0">&nbsp;
			{t}Return to MultiUser & Bounce Management Menu{/t}
			</a>
		</div>
		<div style="text-align: right; clear: both;width: 1px;"></div>

		
		
		<p>
			<a href="{$url.base}/plugins/adminplugins/adminuser/usermanager/user_main.php">
			<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
			{t}Manage Users for Pommo and their permissions{/t}</a> - 
			{t}Use more users in pommo{/t}
		</p><br>
		<p>
			<a href="{$url.base}/plugins/adminplugins/adminuser/authmanager/auth_main.php">
			<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
			{t}Manage Authentication Method{/t}</a> - 
			{t}Witch one will you choose??????{/t}
		</p><br>
		<p>
			<a href="{$url.base}/plugins/adminplugins/adminuser/listmanager/list_main.php">
			<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
			{t}Mailing List Management{/t}</a> - 
			{t}Different users can have different lists to manage.{/t}
		</p><br>

		<br><br>
	{*{/if}*}

</div>



{include file="admin/inc.footer.tpl"}