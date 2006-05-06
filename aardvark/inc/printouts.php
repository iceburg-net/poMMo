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
?>