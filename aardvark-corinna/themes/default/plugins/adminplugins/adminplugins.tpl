{include file="admin/inc.header.tpl"}
</div><!--TODO: currently no sidebar-->

<div id="mainbar" style="width:700px; text-align:left;">

	{*{if $showplugin}*}
		<h1>{t}Plugin Menu{/t}</h1>
		<p>
			<a href="{$url.base}/plugins/adminplugins/adminuser/adminuser.php">
			<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
			{t}Multiuser & Authentication Setup{/t}</a> - 
			{t}Beschreibung Beschreibung Beschreibung Beschreibung Beschreibung Beschreibung {/t}
		</p><br>
		<p>
			<a href="{$url.base}/plugins/adminplugins/adminbounce/adminbounce.php">
			<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
			{t}Bounce Mail Settings{/t}</a> - 
			{t}In Arbeit - I am working on it {/t}
		</p><br>
		

		<br><br>
	{*{/if}*}

</div>



{include file="admin/inc.footer.tpl"}