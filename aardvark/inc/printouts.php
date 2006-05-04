<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/
 
 /** 
 * Don't allow direct access to this file. Must be called from
elsewhere
*/
defined('_IS_VALID') or die('Move along...');
 
function printConfirm($okUrl, $backUrl = NULL, $note = NULL, $okStr = 'I confirm.', $backStr = 'Please Return.' ) {
	echo '
	<table border="0" cellspacing="0" cellpadding="0">
	<tr><td colspan="2">'.$note.'</td></td>
	<tr><td nowrap>

	<img src="'.bm_baseUrl.'/img/icons/alert.png" align="middle">Confirm your action.
	
	</td><td>

	<p>	
		<a href="'.$okUrl.'">
			<img src="'.bm_baseUrl.'/img/icons/ok.png" class="navimage">
			Yes</a> '.$okStr.'
	</p>

	<p>
		<a href="'.$backUrl.'">
			<img src="'.bm_baseUrl.'/img/icons/undo.png" class="navimage" align="middle">
			No</a> '.$backStr.'
	</p>

	</td></tr></table>
	';
}

function javascriptRefresh($seconds = 2) {
	echo '
<script type="text/javascript">
<!--
//min/second refresh
var limit="0:'.$seconds.'"
var parselimit=limit.split(":")
parselimit=parselimit[0]*60+parselimit[1]*1
function jsrefresh(){
	if (!document.images)
	return
	if (parselimit==1)
		window.location.reload()
	else{ 
		setTimeout("jsrefresh()",1000)
	}
}
window.onload=jsrefresh
jsrefresh
//-->
</script>';
}

function printThrottleForm(& $config) {
	echo '

<script src="'.bm_baseUrl.'/inc/js/scriptaculous/prototype.js" type="text/javascript"></script>
<script src="'.bm_baseUrl.'/inc/js/scriptaculous/slider.js" type="text/javascript"></script>

<style>
#track1 {background:url('.bm_baseUrl.'/img/slider_track.png) no-repeat; height:26px; width:218px;}
#handle1 {width:18px;height:28px;background:url('.bm_baseUrl.'/img/slider_handle.png) no-repeat bottom center;cursor:move;}
#track2 {background:url('.bm_baseUrl.'/img/slider_track.png) no-repeat; height:26px; width:218px;}
#handle2 {width:18px;height:28px;background:url('.bm_baseUrl.'/img/slider_handle.png) no-repeat bottom center;cursor:move;}
#track3 {background:url('.bm_baseUrl.'/img/slider_track.png) no-repeat; height:26px; width:218px;}
#handle3 {width:18px;height:28px;background:url('.bm_baseUrl.'/img/slider_handle.png) no-repeat bottom center;cursor:move;}
#track4 {background:url('.bm_baseUrl.'/img/slider_track.png) no-repeat; height:26px; width:218px;}
#handle4 {width:18px;height:28px;background:url('.bm_baseUrl.'/img/slider_handle.png) no-repeat bottom center;cursor:move;}
#track5 {background:url('.bm_baseUrl.'/img/slider_track2.png) no-repeat; height:26px; width:218px;}
#handle5 {width:18px;height:28px;background:url('.bm_baseUrl.'/img/slider_handle.png) no-repeat bottom center;cursor:move;}
</style>

<form id="bm_throttle" name="bm_throttle" action="'.$_SERVER['PHP_SELF'].'" method="POST">

<fieldset>
    <legend>Throttle Controller</legend>

<div class="field">
<table align="center" border="0">
<tr><td></td><td>Hour</td><td>Minute</td><td>Second</td></tr>
<tr>
  <td align="right">Mails</td>
  <td><input id="mph" type="text" readonly size="7"></td>
  <td><input id="mpm" type="text" readonly size="7"></td>
  <td><input id="mps" name="mps" type="text" readonly size="7"></td>
</tr>
<tr>
  <td align="right">Megabytes</td>
  <td><input id="mbph" type="text" readonly size="7"></td>
  <td><input id="mbpm" type="text" readonly size="7"></td>
  <td><input id="mbps" type="text" readonly size="7"></td>
</tr>
<tr>
  <td align="right">Kilobytes</td>
  <td><input id="kbph" type="text" readonly size="7"></td>
  <td><input id="kbpm" type="text" readonly size="7"></td>
  <td><input id="kbps" name="kbps" type="text" readonly size="7"></td>
</tr>
</table>
</div>

<div class="field">
  Mail Rate
  <div id="track1"><div id="handle1"></div></div>
</div>

<script type="text/javascript" language="javascript">
// <![CDATA[
	var s_mps = new Control.Slider(\'handle1\',\'track1\',{
	range: $R(0,5),
	onSlide:function(v){$(\'mps\').value=+v,$(\'mpm\').value=+v*60,$(\'mph\').value=+v*60*60},
	onChange:function(v){$(\'mps\').value=+v,$(\'mpm\').value=+v*60,$(\'mph\').value=+v*60*60}});
// ]]>
</script>

<div class="field">
  Bandwith
  <div id="track2"><div id="handle2"></div></div>

<div class="notes">
  If a controller is set to off (0), mails will be sent without consulting the throttler, resulting in very fast sending of mails but also high server load.
</div>

</div>

<script type="text/javascript" language="javascript">
// <![CDATA[
	var s_bps = new Control.Slider(\'handle2\',\'track2\',{
	range: $R(0,250),
	onSlide:function(v){$(\'kbps\').value=+v,$(\'kbpm\').value=+v*60,$(\'kbph\').value=+v*60*60,$(\'mbps\').value=+v/1024,$(\'mbpm\').value=+v*60/1024,$(\'mbph\').value=+v*60*60/1024},
	onChange:function(v){$(\'kbps\').value=+v,$(\'kbpm\').value=+v*60,$(\'kbph\').value=+v*60*60,$(\'mbps\').value=+v/1024,$(\'mbpm\').value=+v*60/1024,$(\'mbph\').value=+v*60*60/1024}});
// ]]>
</script>



</fieldset>

<div align="center"><input class="button" id="throttle-submit" name="throttle-submit" type="submit" value="Save Values" /><br>&nbsp;</div>

<fieldset>
    <legend>Domain Controller</legend>

<div class="field">
  Period length (In Seconds): <table align="right" border="0"><tr><td><input id="dp" name="dp" type="text" readonly size="3"></td><td style="width: 77px;"></td></tr></table>
  <div id="track5"><div id="handle5"></div></div>
</div>

<script type="text/javascript" language="javascript">
// <![CDATA[
	var s_dp = new Control.Slider(\'handle5\',\'track5\',{
	range: $R(5,20),
	values: [5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20],
	onSlide:function(v){$(\'dp\').value=+v},
	onChange:function(v){$(\'dp\').value=+v}});
// ]]>
</script>

<div class="field">
  Max Mails sent per Period: <table align="right" border="0"><tr><td><input id="dmpp" name="dmpp" type="text" readonly size="3"></td><td style="width: 77px;"></td></tr></table>
  <div id="track3"><div id="handle3"></div></div>
</div>

<script type="text/javascript" language="javascript">
// <![CDATA[
	var s_dmpp = new Control.Slider(\'handle3\',\'track3\',{
	range: $R(-0.01,5),
	values: [-0.01,0,1,2,3,4,5],
	onSlide:function(v){$(\'dmpp\').value=+v},
	onChange:function(v){$(\'dmpp\').value=+v}});
// ]]>
</script>

<div class="field">
  Max Kilobytes sent per Period: <table align="right" border="0"><tr><td><input id="dbpp" name="dbpp" type="text" readonly size="3"></td><td style="width: 77px;"></td></tr></table>
  <div id="track4"><div id="handle4"></div></div>
</div>

<script type="text/javascript" language="javascript">
// <![CDATA[
	var s_dbpp = new Control.Slider(\'handle4\',\'track4\',{
	range: $R(0,200),
	onSlide:function(v){$(\'dbpp\').value=+v},
	onChange:function(v){$(\'dbpp\').value=+v}});
// ]]>
</script>
</fieldset>

<div align="center">
	<input class="button" id="throttle-submit" name="throttle-submit" type="submit" value="Save Values" />
	<br><br><br>---<br>&nbsp;
	<input class="button" id="throttle-restore" name="throttle-restore" type="submit" value="Restore Defaults" />
	<br>&nbsp;
</div>

</form>

<script type="text/javascript" language="javascript">
s_mps.setValue('.$config['throttle_MPS'].');
s_bps.setValue('.$config['throttle_BPS'].');
s_dp.setValue('.$config['throttle_DP'].');
s_dbpp.setValue('.$config['throttle_DBPP'].');
s_dmpp.setValue('.$config['throttle_DMPP'].');
</script>
	';
	
} 
?>