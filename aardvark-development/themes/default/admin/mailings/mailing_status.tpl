{capture name=head}{* used to inject content into the HTML <head> *}
<link type="text/css" rel="stylesheet" href="{$url.theme.this}inc/mailing.status.css"/>
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
{/capture}
{include file="inc/tpl/admin.header.tpl"}

<p>
<img src="{$url.theme.shared}images/icons/alert.png" class="navimg right" alt="thunderbolt icon" />
{t escape=no 1="<a href='`$url.base`admin/setup/setup_throttle.php'>" 2="</a>"}Mailings are processed in the background so feel free to turn off your computer and browse other sites. %1Throttle settings%2 can also be adjusted -- although you must pause and revive the mailing before changes take effect.{/t}
</p>

<div>
{t 1=$mailing.tally}Sending message to %1 subscribers.{/t}
</div>

<br/>

{* Updates via AJAX: Processing Mailing, Mailing Finished, Mailing Frozen *}
<div class="warn"> 
{t}Mailing Status{/t} &raquo; <span id="status"></span>
</div>

<hr />


{* Updates via AJAX: Pause/Resume (started), Resume/Cancel (stopped), DeThaw/Cancel (frozen) *}
<div id="commands">
	<div class="error init">{t}Initializing...{/t}</div>
</div>

{* Hidden until mailing is finished *}
<div id="finished" class="hide error">
{t}Mailing Finished{/t} -- <a href="admin_mailings.php">{t}Return to{/t} {t}Mailings Page{/t}</a>
</div>

{* Displayed when a command is clicked *}
<div id="wait" class="hide error">
{t}Command Recieved. Please wait...{/t}
</div>

<hr />

<div class="pbBarText" id="pbBarText">
	{t escape="no" 1='<span id="sent">0</span>'}%1 mails sent{/t}
	<img class="anim go" src="{$url.theme.shared}images/loader.gif" alt="Processing" />
	<img class="anim hide stop" src="{$url.theme.shared}images/icons/stopped-small.png" alt="Stopped" />
</div>

<div class="pbTrack">
	<div class="pbBarContainer">
		<div class="pbBar" id="pbBar"></div>
	</div>
</div>

<div class="pbText" id="pbText"></div>


<fieldset>
	<legend>{t}Last 50 notices{/t}</legend>
	
<div class="inpage_menu">
	<li>
	<a href="ajax/status_download.php?type=sent">{t}View{/t} {t}Sent Emails{/t}</a>
	</li>
	
	<li>
	<a href="ajax/status_download.php?type=unsent">{t}View{/t} {t}Unsent Emails{/t}</a>
	</li>
	
	<li>
	<a href="ajax/status_download.php?type=error">{t}View{/t} {t}Failed Emails{/t}</a>
	</li>
</div>

<br/>
	
<div id="notices"></div>

</fieldset>

{* the folowing populate #commands via Javascript, and are here for reference/translation *}
<div class="hide" id="started">
	<div class="first">
		<a class="cmd" href="#stop">
		<img src="{$url.theme.shared}images/icons/pause-small.png" alt="Pause"/>
		{t}Pause Mailing{/t}
		</a>
	</div>
	<div class="second">
		<a class="cmd" href="#restart">
		{t}Resume Mailing{/t}
		<img src="{$url.theme.shared}images/icons/restart-small.png" alt="Restart"/>
		</a>
	</div>
</div>

<div class="hide" id="stopped">
	<div class="first">
		<a class="cmd" href="#restart">
		<img src="{$url.theme.shared}images/icons/restart-small.png" alt="Pause"/>
		{t}Resume Mailing{/t}
		</a>
	</div>
	<div class="second">
		<a class="cmd" href="#cancel">
		{t}Cancel Mailing{/t}
		<img src="{$url.theme.shared}images/icons/stopped-small.png" alt="Restart"/>
		</a>
	</div>
</div>

<div class="hide" id="frozen">
	<div class="first">
		<a class="cmd" href="#restart">
		<img src="{$url.theme.shared}images/icons/restart-small.png" alt="Pause"/>
		{t}Dethaw Mailing{/t}
		</a>
	</div>
	<div class="second">
		<a class="cmd" href="#cancel">
		{t}Cancel Mailing{/t}
		<img src="{$url.theme.shared}images/icons/stopped-small.png" alt="Restart"/>
		</a>
	</div>
</div>


{literal}
<script type="text/javascript">
pommo = {
	init: function() {
		this.disabled = true;
		this.attempt = 1;
		this.waiting = false;
		this.poll();
	},
	poll: function() {
		this.polling = true;
		$.post("ajax/status_poll.php?id={/literal}{$mailing.id}{literal}&attempt="+pommo.attempt, {}, function(out) {
			pommo.disabled = false; // enable commands after AJAX success
			eval("var json = " + out);
				if (typeof(json.status) == 'undefined')
			alert('ajax error!');
			
			if(this.waiting && !json.waiting)
				return;
			
			$('#status').html(json.statusText);
			
			// status >> 1: Processing  2: Stopped  3: Frozen  4: Finished
			if(json.status == 1)
				$('#pbBarText').find('img.go').css('display', 'inline').end().find('img.stop').css('display', 'none');
			else 
				$('#pbBarText').find('img.go').css('display', 'none').end().find('img.stop').css('display', 'inline');
			
			if(!json.command || $('#commands div.init').size() > 0)  {
				switch(json.status) {
					case 1: 
						$('#commands').html($('#started').html());
						break;
					case 2: $('#commands').html($('#stopped').html()); break;
					case 3: $('#commands').html($('#frozen').html()); break;
					case 4: $('#commands').html('').append($('#finished').clone().css('display','block')); break;
				}
			}
			
			$('#commands a.cmd').click(function() { return pommo.click(this); });
			
			$('#sent').html(json.sent);
			$('#pbText').html(json.percent+'%');
			$('#pbBar').width(json.percent+'%');
			
			if (typeof(json.notices) == 'object')
				for (i in json.notices)
					if(json.notices[i] != '')
						$('#notices').prepend('<li>'+json.notices[i]+'</li>');
			
			// TODO --> make a nice XPATH selector out of this...
			if ($('#notices li').size() > 50) {
				$('#notices li').each(function(i){ if (i > 40) $(this).remove(); });
			}
			
			pommo.attempt = (json.incAttempt) ? pommo.attempt + 1 : 1;
			
			// repoll
			if(json.status < 3)
				setTimeout('pommo.poll()',5500);
			else
				pommo.polling = false;
		});
		
	},
	sendCmd: function(cmd) {
		this.disabled = true;
		this.attempt = 1;
		$('#commands').html('').append($('#wait').clone().css('display','block'));
		
		$.post('ajax/status_cmd.php?cmd='+cmd,{}, function(out) {
			eval("var json = " + out);
				if (typeof(json.success) == 'undefined')
			alert('ajax error!');
			
			if (!pommo.polling)
				pommo.poll();
		});
	},
	click: function(e) {
		if (this.disabled)
			return false;
		this.sendCmd($(e).href().replace(/.*\#/,''));
		return false;
	}
};

$().ready(function(){ pommo.init(); });
</script>
{/literal}

{include file="inc/tpl/admin.footer.tpl"}