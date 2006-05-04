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

define('_IS_VALID', TRUE);

require('../../bootstrap.php');
require_once(bm_baseDir.'/inc/lib.import.php');
$poMMo = & fireup('secure');
$dbo = & $poMMo->openDB();

// Maximum File Size (in MB) 
$max_file_size = 2;

// Filename (in $_FILES array)
$fname = "csvfile";

$errors = FALSE;
// if file is uploaded, validate & re-direct.
if (!empty($_FILES[$fname]['tmp_name'])) {
	
	$csvArray =& csvPrepareFile($poMMo, $dbo, $_FILES[$fname]['tmp_name']);
	
	if (is_array($csvArray)) {
		$sessionArray['csvArray'] =& $csvArray;
		$poMMo->dataSet($sessionArray);
		header('Location: '.bm_http.bm_baseUrl.'/admin/subscribers/subscribers_import2.php');
	}
	$errors = TRUE;
}

/** poMMo templating system **/
// header settings -->

$_nologo = FALSE;
$_menu = array ();
$_menu[] = '<a href="'.bm_baseUrl.'/index.php?logout=TRUE">Logout</a>';
$_menu[] = '<a href="admin_subscribers.php">Subscribers Page</a>';
$_menu[] = '<a href="'.$poMMo->_config['site_url'].'">'.$poMMo->_config['site_name'].'</a>';

// right bar settings -->
$_nomenu = FALSE; // turn off main "admin menu" in right bar
$_nodemo = FALSE; // turn off display of poMMo demonstration mode status

$_extmenu = array ();
$_extmenu['name'] = "Subscriber Management";
$_extmenu['links'] = array ();
$_extmenu['links'][] = "<a href=\"subscribers_manage.php\">Manage</a>";
$_extmenu['links'][] = "<a href=\"subscribers_import.php\">Import</a>";
$_extmenu['links'][] = "<a href=\"subscribers_groups.php\">Groups</a>";

include (bm_baseDir.'/setup/top.php');
/** End templating system **/

?>

<h1>Import Subscribers</h1>

<img src="<?php echo bm_baseUrl; ?>/img/icons/cells.png" class="articleimg">

<p>
poMMo supports importing many subscribers at once from <b>CSV</b> files.
Your CSV file should have one subscriber(email) per line with demographic information seperated by commas(,).<br><br>
</p>
<p>
Popular programs such as Microsoft Excel and <a href="http://www.openoffice.org">Open Office</a> support saving files in Comma-Seperated-Value format. 
</p>

<br>

<?php

	echo "
	  <form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">
		<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"". $max_file_size * 1024 * 1024 ."\" />
		<div align=\"center\">
	    Your CSV file: <input name=\"".$fname."\" type=\"file\" />
	    &nbsp;&nbsp; <input type=\"submit\" value=\"Upload\" />
		</div>
	  </form>
	  ";
	  
	  	if ($errors) {
		echo '<br><b>Error</b> proccessing your file.';
		$poMMo->printMessages('</li>','<li>');
	}
		

include(bm_baseDir.'/setup/footer.php');
?>