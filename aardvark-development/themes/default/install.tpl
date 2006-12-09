{include file="admin/inc.header.tpl" sidebar='off'}

<h2>{t}Installation{/t}</h2>

<h3>{t}Online install{/t}</h3>

<ul class="inpage_menu">
<li>{$config.app.weblink}</li>
<li><a href="{$url.base}admin/admin.php">{t}Admin Page{/t}</a></li>
</ul>

<p><img src="{$url.theme.shared}images/icons/alert.png" alt="alert icon" class="navimage" /> {t}Welcome to the online installation process. We have connected to the database and set your language successfully. Fill in the values below, and you'll be on your way!{/t}</p>

{include file="admin/inc.messages.tpl"}

{if !$installed}
<form method="post" action="">
{if $debug}
<input type="hidden" name="debugInstall" value="true" />

<div style="float: right; text-align: right;">
<label for="disableDebug">{t}To disable debugging{/t}</label>
<input type="submit" id="disableDebug" name="disableDebug" value="{t}Click Here{/t}" />
</div>

{else}

<div>
<label for="debugInstall">{t}To enable debugging{/t}<label>
<input type="submit" id="debugInstall" name="debugInstall" value="{t}Click Here{/t}" />
</div>

{/if}

<fieldset>
<legend>{t}Configuration Options{/t}</legend>

<div>
<div class="error">{validate id="list_name" message=$formError.list_name}</div>
<label for="list_name"><span class="required">{t}Name of Mailing List:{/t}</span></label>
<input type="text" size="32" maxlength="60" name="list_name" value="{$list_name|escape}" id="list_name" />
<div class="notes">{t}(ie. Brice's Mailing List){/t}</div>
</div>

<div>
<div class="error">{validate id="site_name" message=$formError.site_name}</div>
<label for="site_name"><span class="required">{t}Name of Website:{/t}</span></label>
<input type="text" size="32" maxlength="60" name="site_name" value="{$site_name|escape}" id="site_name" />
<div class="notes">{t}(ie. The poMMo Website){/t}</div>
</div>

<div>
<div class="error">{validate id="site_url" message=$formError.site_url}</div>
<label for="site_url"><span class="required">{t}Website URL:{/t}</span></label>
<input type="text" size="32" maxlength="60" name="site_url" value="{$site_url|escape}" id="site_url" />
<div class="notes">{t}(ie. http://www.pommo-rocks.com/){/t}</div>
</div>

<div>
<div class="error">{validate id="admin_password" message=$formError.admin_password}</div>
<label for="admin_password"><span class="required">{t}Administrator Password:{/t}</span></label>
<input type="text" size="32" maxlength="60" name="admin_password" value="{$admin_password|escape}" id="admin_password" />
<div class="notes">{t}(you will use this to login){/t}</div>
</div>

<div>
<div class="error">{validate id="admin_password2" message=$formError.admin_password2}</div>
<label for="admin_password2"><span class="required">{t}Verify Password:{/t}</span></label>
<input type="text" size="32" maxlength="60" name="admin_password2" value="{$admin_password2|escape}" id="admin_password2" />
 <div class="notes">{t}(enter password again){/t}</div>
</div>

<div>
<div class="error">{validate id="admin_email" message=$formError.admin_email}</div>
<label for="admin_email"><span class="required">{t}Administrator Email:{/t}</span></label>
<input type="text" size="32" maxlength="60" name="admin_email" value="{$admin_email|escape}" id="admin_email" />
<div class="notes">{t}(enter your valid email address){/t}</div>
</div>

</fieldset>

<div class="buttons">
<input type="submit" id="installerooni" name="installerooni" value="{t}Install{/t}" />
</div>

</form>
{/if}

<p><a href="{$url.base}index.php"><img src="{$url.theme.shared}images/icons/back.png" alt="back icon" class="navimage" />{t}Continue to login page{/t}</a></p>

<p><em>{t escape="no" url='<a href="http://pommo.sourceforge.net/">poMMo</a>'}Page fueled by %1 mailing management software.{/t}</em></p>

{include file="admin/inc.footer.tpl"}