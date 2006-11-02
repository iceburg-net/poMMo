{assign var='mailingCount' value=$mailings|@count}

<h1>{$actionStr}</h1>
{if $messages}
	<div class="msgdisplay">
		{foreach from=$messages item=msg}
			<div>* {$msg}</div>
		{/foreach}
	</div>
{/if}
{if $errors}
	<div class="errdisplay">
		{foreach from=$errors item=msg}
			<div>* {$msg}</div>
		{/foreach}
	</div>
{/if}

<p><span style="text-align: center;">
{if $action == 'view'}
	{t 1=$mailingCount}Displaying %1 mailings.{/t}
{/if}
</span></p>

		
{foreach from=$mailings key=key item=mailing}
	<div style="background-color: #E6ECDA; width: 80%; text-align:left;">
					<p><strong>{t}From:{/t} </strong>{$mailing.fromname} &lt;{$mailing.fromemail}&gt;</p>
					{if $mailing.fromemail != $mailing.frombounce}<p><strong>{t}Bounces:{/t} </strong>&lt;{$mailing.frombounce}&gt;</p>{/if}
					<p><strong>{t}To:{/t} </strong>{$mailing.mailgroup}, <em>{$mailing.subscriberCount}</em> {t}recipients.{/t}</p>
					<p><strong>{t}Subject:{/t} {$mailing.subject}</strong></p>
	</div>
	
	
	<div style="background-color: #F6F8F1;  width: 80%; text-align:left;">
					{if $mailing.ishtml == 'on'}
						<p>
							<strong>{t}HTML Body:{/t} </strong>
						</p>
						<p>
								 {$mailing.body}
						</p>
						{if $mailing.altbody}
							<p>
							<strong>{t}Alt Body:{/t} </strong>
							</p>
							<p>
							<pre>{$mailing.altbody}</pre>
							</p>
						{/if}
					{else}
						<p>
						<strong>{t}Body:{/t} </strong>
						</p>
						<p>
						<pre>{$mailing.body}</pre>
						</p>
					{/if}
	</div>

{/foreach}


