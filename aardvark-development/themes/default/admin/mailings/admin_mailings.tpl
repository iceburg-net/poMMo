{include file="admin/inc.header.tpl"}

<div id="mainbar">

<h2>{t}Mailings Page{/t}</h2>

<p><a href="{$url.base}admin/mailings/mailings_send.php">
<img src="{$url.theme.shared}images/icons/typewritter.png" alt="typewritter icon" class="navimage" />{t}Send{/t}</a> - {t}Create and send a mailing.{/t}</p>

<p><a href="{$url.base}admin/mailings/mailings_history.php"><img src="{$url.theme.shared}images/icons/history.png" alt="calendar icon" class="navimage" />{t}History{/t}</a> - {t}View mailings that have already been sent.{/t}</p>

</div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}