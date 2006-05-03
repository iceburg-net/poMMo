<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://bmail.sourceforge.net/
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

?>
<p class="clearer"></p>
</div>
<!-- end mainbar -->

</div>
<!-- end content -->

<div id="footer">
&nbsp;<br />
 Page fueled by <a href="http://bmail.sourceforge.net/">bMail</a> mailing management software.
 </div>
<!-- end footer -->

</center>

<?
if (bm_debug == 'on') {
	if (isset($dbo) && is_object($dbo))	{
	$dbo->debug(FALSE);
	$dbMessage = mysql_error($dbo->_link);
	}
	

	print_r($_SESSION);
	print_r($_REQUEST);
}
?>
</body>
</html>