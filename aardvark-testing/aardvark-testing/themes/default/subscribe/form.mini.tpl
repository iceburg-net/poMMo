{* Include form CSS styling *}
<link href="{$url.theme.this}/subscribe/subscribe_form.css" type="text/css" rel="STYLESHEET">

<div id="subscribeForm">
<form  action="{$url.base}/user/subscribe.php" method="POST">
		<span style="margin-right: 17px">
			{t}Your Email:{/t}
		</span>
		<input type="text" class="text" size="20" maxlength="60" name="bm_email" id="bm_email"> 
		<input style="margin-left: 17px;" type="submit" value="{t}Subscribe{/t}" />
</form>
</div>