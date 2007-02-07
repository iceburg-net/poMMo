{capture name=head}
{* used to inject content into the HTML <head> *}
<style type="text/css">
#track1 {ldelim}background:url({$url.theme.shared}images/slider_track.png) no-repeat; height:26px; width:218px;{rdelim}
#handle1 {ldelim}width:18px;height:28px;background:url({$url.theme.shared}images/slider_handle.png) no-repeat bottom center;cursor:move;{rdelim}
#track2 {ldelim}background:url({$url.theme.shared}images/slider_track.png) no-repeat; height:26px; width:218px;{rdelim}
#handle2 {ldelim}width:18px;height:28px;background:url({$url.theme.shared}images/slider_handle.png) no-repeat bottom center;cursor:move;{rdelim}
#track3 {ldelim}background:url({$url.theme.shared}images/slider_track.png) no-repeat; height:26px; width:218px;{rdelim}
#handle3 {ldelim}width:18px;height:28px;background:url({$url.theme.shared}images/slider_handle.png) no-repeat bottom center;cursor:move;{rdelim}
#track4 {ldelim}background:url({$url.theme.shared}images/slider_track.png) no-repeat; height:26px; width:218px;{rdelim}
#handle4 {ldelim}width:18px;height:28px;background:url({$url.theme.shared}images/slider_handle.png) no-repeat bottom center;cursor:move;{rdelim}
#track5 {ldelim}background:url({$url.theme.shared}images/slider_track2.png) no-repeat; height:26px; width:218px;{rdelim}
#handle5 {ldelim}width:18px;height:28px;background:url({$url.theme.shared}images/slider_handle.png) no-repeat bottom center;cursor:move;{rdelim}
</style>
<script src="{$url.theme.shared}js/scriptaculous/prototype.js" type="text/javascript"></script>
<script src="{$url.theme.shared}js/scriptaculous/slider.js" type="text/javascript"></script>
{/capture}

{include file="inc/admin.header.tpl"}

<h2>{t}Configure{/t} {t}Throttling{/t}</h2>

<ul class="inpage_menu">
<li><a href="{$url.base}admin/setup/setup_configure.php">{t 1=$returnStr}Return to %1{/t}</a></li>
</ul>

<p><img src="{$url.theme.shared}images/icons/settings.png" alt="settings icon" class="navimage right" /> {t}You can throttle mails so you don't overload your server or slam a common domain (such as hotmail/yahoo.com). Mail volume and bandwith can be controlled. Additionally, you can limit the mails and kilobytes sent to a single domain during a specified time frame.{/t}</p>

{include file="inc/messages.tpl"}

<form method="post" action="">

<fieldset>
<legend>{t}Throttle Controller{/t}</legend>

<table summary="{t}Throttling information{/t}">
<thead>
  <tr>
	<th></th>
	<th>{t}Hour{/t}</th>
	<th>{t}Minute{/t}</th>
	<th>{t}Second{/t}</th>
  </tr>
</thead>
<tbody>
  <tr>
	<th>{t}Mails{/t}</th>
	<td><input type="text" id="mph" readonly size="7" /></td>
	<td><input type="text" id="mpm" readonly size="7" /></td>
	<td><input type="text" id="mps" name="mps" readonly size="7" /></td>
  </tr>
  <tr>
	<th>{t}Megabytes{/t}</th>
	<td><input type="text" id="mbph" readonly size="7" /></td>
	<td><input type="text" id="mbpm" readonly size="7" /></td>
	<td><input type="text" id="mbps" readonly size="7" /></td>
  </tr>
  <tr>
	<th>{t}Kilobytes{/t}</th>
	<td><input type="text" id="kbph" readonly size="7" /></td>
	<td><input type="text" id="kbpm" readonly size="7" /></td>
	<td><input type="text" id="kbps" name="kbps" readonly size="7" /></td>
  </tr>
</tbody>
</table>

<div>
<h3>{t}Mail Rate{/t}:</h3>

<div id="track1"><div id="handle1"></div></div>
</div>

<div>
<h3>{t}Bandwith{/t}:</h3>
<div id="track2"><div id="handle2"></div></div>

<div class="notes">

<p>{t escape='no' 1='<tt>' 2='</tt>'}If a controller is set to off (%10%2), mails will be sent without consulting the throttler, resulting in very fast sending of mails but also high server load.{/t}</p>

</div>

</div>

</fieldset>

<div class="buttons">

<input type="submit" name="throttle-submit" value="{t}Save Values{/t}" />

</div>

<fieldset>
<legend>{t}Domain Controller{/t}</legend>

<div>

<h3>{t}Period length (In Seconds){/t}:</h3>

<div>
<input type="text" id="dp" name="dp" readonly size="3" />
</div>

<div id="track5"><div id="handle5"></div></div>

</div>

<div>

<h3>{t}Max Mails sent per Period{/t}:</h3>

<div>
<input type="text" id="dmpp" name="dmpp" readonly size="3" />
</div>

</div>

<div>

<div id="track3"><div id="handle3"></div></div>

<h3>{t}Max Kilobytes sent per Period{/t}:</h3>

<div>
<input type="text" id="dbpp" name="dbpp" readonly size="3" />
</div>

<div id="track4"><div id="handle4"></div></div>

</div>

</fieldset>

<div class="buttons">

<input type="submit" name="throttle-submit" value="{t}Save Values{/t}" />
<input type="submit" name="throttle-restore" value="{t}Restore Defaults{/t}" />

</div>

</form>

{literal}
<script type="text/javascript" language="javascript">
// <![CDATA[
	var s_mps = new Control.Slider('handle1','track1',{
	range: $R(0,5),
	onSlide:function(v){$('mps').value=+v,$('mpm').value=+v*60,$('mph').value=+v*60*60},
	onChange:function(v){$('mps').value=+v,$('mpm').value=+v*60,$('mph').value=+v*60*60}});

	var s_bps = new Control.Slider('handle2','track2',{
	range: $R(0,250),
	onSlide:function(v){$('kbps').value=+v,$('kbpm').value=+v*60,$('kbph').value=+v*60*60,$('mbps').value=+v/1024,$('mbpm').value=+v*60/1024,$('mbph').value=+v*60*60/1024},
	onChange:function(v){$('kbps').value=+v,$('kbpm').value=+v*60,$('kbph').value=+v*60*60,$('mbps').value=+v/1024,$('mbpm').value=+v*60/1024,$('mbph').value=+v*60*60/1024}});

	var s_dp = new Control.Slider('handle5','track5',{
	range: $R(5,20),
	values: [5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20],
	onSlide:function(v){$('dp').value=+v},
	onChange:function(v){$('dp').value=+v}});

	var s_dmpp = new Control.Slider('handle3','track3',{
	range: $R(-0.01,5),
	values: [-0.01,0,1,2,3,4,5],
	onSlide:function(v){$('dmpp').value=+v},
	onChange:function(v){$('dmpp').value=+v}});

	var s_dbpp = new Control.Slider('handle4','track4',{
	range: $R(0,200),
	onSlide:function(v){$('dbpp').value=+v},
	onChange:function(v){$('dbpp').value=+v}});

// ]]>
</script>
{/literal}

<script type="text/javascript" language="javascript">
s_mps.setValue({$throttle_MPS});
s_bps.setValue({$throttle_BPS});
s_dp.setValue({$throttle_DP});
s_dbpp.setValue({$throttle_DBPP});
s_dmpp.setValue({$throttle_DMPP});
</script>

{include file="inc/admin.footer.tpl"}