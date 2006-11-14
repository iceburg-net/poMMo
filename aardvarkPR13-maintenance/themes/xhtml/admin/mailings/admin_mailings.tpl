{include file="admin/inc.header.tpl"}

<div id="mainbar">

<h2>{t}Mailings Page{/t}</h2>

{if $mailing}
<p><a href="{$url.base}admin/mailings/mailing_status.php"><img src="{$url.theme.shared}images/icons/status.png" alt="world with arrows icon" class="navimage">{t}Status{/t}</a> - {t}A mailing is currently taking place. You can not create a mailing until this one completes. Visit this page to check on the status of this mailing.{/t}</p>

{else}
<p><a href="{$url.base}admin/mailings/mailings_send.php">
<img src="{$url.theme.shared}images/icons/typewritter.png" alt="typewritter icon" class="navimage" />{t}Send{/t}</a> - {t}Create and send a mailing.{/t}</p>
{/if}

<p><a href="{$url.base}admin/mailings/mailings_history.php"><img src="{$url.theme.shared}images/icons/history.png" alt="calendar icon" class="navimage" />{t}History{/t}</a> - {t}View mailings that have already been sent.{/t}</p>

</div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}