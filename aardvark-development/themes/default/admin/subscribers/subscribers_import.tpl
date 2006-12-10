{include file="admin/inc.header.tpl"}

<h2>{t}Import Subscribers{/t}</h2>

<p><img src="{$url.theme.shared}images/icons/cells.png" class="articleimg" alt="table cells icon" /> {t escape=no 1='<strong>' 2='</strong>' 3='<tt>' 4='</tt>'}You can import subscribers from %1CSV%2 files. Your CSV file should have one subscriber(email) per line with field information seperated by commas(%3,%4).{/t}</p>

<p>{t escape=no 1='<a href="http://www.openoffice.org/">' 2='</a>'}Popular programs such as Microsoft Excel and %1Open Office%2 support saving files in Comma-Seperated-Value format.{/t}</p>

<form method="post" enctype="multipart/form-data" action="">

{include file="admin/inc.messages.tpl"}

<fieldset>
<legend>Import</legend>

<input type="hidden" name="MAX_FILE_SIZE" value="{$maxSize}" />

<div>
<label for="csvfile">{t}CSV file:{/t}</label>
<input type="file" accept="text/csv" name="csvfile" id="csvfile" class="file" />
</div>

</fieldset>

<div class="buttons">

<input type="submit" value="{t}Upload{/t}" />

</div>

</form>

{include file="admin/inc.footer.tpl"}