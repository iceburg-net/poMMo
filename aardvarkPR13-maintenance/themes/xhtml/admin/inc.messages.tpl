{if $messages}
<div id="warnmsg" class="warn">

<ul>
{foreach from=$messages item=msg}
<li><strong>{$msg}</strong></li>
{/foreach}
</ul>

</div>
{/if}

{if $errors}
<div id="errormsg" class="error">

<ul>
{foreach from=$errors item=msg}
<li>{$msg}</li>
{/foreach}
</ul>

</div>
{/if}