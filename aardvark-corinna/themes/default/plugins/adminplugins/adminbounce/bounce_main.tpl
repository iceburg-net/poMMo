{include file="admin/inc.header.tpl"}
</div>
<!-- begin content -->

<h1>{t}Bounce Mail Management{/t}</h1>

	<div class="container" style="margin: left; width: 760px;">

		{* Display a eventual error message *}
		{if $messages}
			<div class="msgdisplay">
				{foreach from=$messages item=msg}
					<div>* {$msg}</div>
				{/foreach}
			</div>
		{/if}
		{if $errors}
			<br>
			<div class="errdisplay">
				{foreach from=$errors item=msg}
					<div>* {$msg}</div>
				{/foreach}
			</div>
		{/if}
		
		Bounce Server<br>
		Bounce Mailbox<br>
		Bounce Mailbox User<br>
		Bounce Password <br>


	</div>

{include file="admin/inc.footer.tpl"}