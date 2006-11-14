{include file="admin/inc.header.tpl"}

<div id="mainbar">

<h2>{t}Setup Page{/t}</h2>

<p><a href="{$url.base}admin/setup/setup_configure.php"><img src="{$url.theme.shared}images/icons/settings.png" alt="settings icon" class="navimage" />{t}Configure{/t}</a> - {t}Set your mailing list name, its default behavior, and the administrator's information.{/t}</p>

<p><a href="{$url.base}admin/setup/setup_fields.php"><img src="{$url.theme.shared}images/icons/fields.png" alt="subscriber icon" class="navimage" />{t}Subscriber Fields{/t}</a> - {t}Choose the information you'd like to collect from your subscribers.{/t}</p>

<p><a href="{$url.base}admin/setup/setup_form.php"><img src="{$url.theme.shared}images/icons/form.png" alt="form icon" class="navimage" />{t}Subscription Form{/t}</a> - {t}Preview and Generate the subscription form for your website.{/t}</p>

</div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}