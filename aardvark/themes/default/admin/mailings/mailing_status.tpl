{include file="admin/inc.header.tpl"}
<link href="{$url.theme.this}/admin/status.css" type="text/css" rel="STYLESHEET">
<script src="{$url.theme.shared}/scriptaculous/prototype.js" type="text/javascript"></script>

<div>
<img src="{$url.theme.shared}/images/icons/alert.png" align="middle" style="float: left;">
{t}The mailing process takes place in the background, so feel free to close your browser, visit other sites, or work within poMMo. Throttle settings can be adjusted, although you must pause and revive a mailings before the changes take effect.{/t}
</div>

<div style="width: 514px; margin: auto;">
	
	<div style="text-align: center;">
		<br>
		<strong>{t 1=$subscriberCount}Sending message to %1 subscribers.{/t}</strong>
	</div>
	
	<div id="ajaxUpdate"></div>
	
	<div style="text-align: center;">
	<em>({t}Processing Mailing{/t})</em><br><hr>
		<span style="float: left;">
			<a href="mailing_status2.php?command=kill">
				<img src="{$url.theme.shared}/images/icons/pause-small.png" border="0" align="absmiddle">
				{t}Pause Mailing{/t}
			</a> 
		</span>
		<span style="float: right;">
		<a href="/bmail/demo/mailing_status2.php?command=restart">
			{t}Cancel Mailing{/t}
			<img src="{$url.theme.shared}/images/icons/stopped-small.png" border="0" align="absmiddle">
		</a>
		</span>
		<p style="clear: both;"></p>
		<hr>
	</div>
		
	<div class="pbBarText" id="pbBarText">0 sent</div>
	<div class="pbTrack">
		<div class="pbBarContainer">
			<div class="pbBar" id="pbBar"></div>
		</div>
	</div>
	<div class="pbText" id="pbText">0%</div>

</div>

{literal}
<script type="text/javascript">
// <![CDATA[

/* JSON objects - pb (progress bar) ,  ce (command executer) */

var pb = {
  init: function() {
    this.percent = 0;
    this.updater = new Ajax.PeriodicalUpdater(
      'ajaxUpdate',
      'ajax_status.php',
      { 
        frequency: 2, 
        onSuccess: this.update.bind(this)
      }
    );
  },
  update: function(resp, json) {
    if (json.percent >= 100) { this.updater.stop(); this.updater = null; }
    $('pbBarText').innerHTML = json.sent + " sent";
    $('pbText').innerHTML = json.percent + "%";
    $('pbBar').setStyle({width: json.percent + '%' });
  }
}

/* INIT FUNCTIONS */
pb.init();


/*
function pushCommand(cmd) {
	if (!document.pbCommand) {
		document.pbCommand =
		new Ajax.PeriodicalUpdater("ajaxCommand", "ajax_statusCommand.php", {
 		asynchronous:true,
 		frequency : 3,
 		parameters: "command=" + cmd
		});
	}
}
*/

// ]]>
</script>
{/literal}

	
{include file="admin/inc.footer.tpl"}