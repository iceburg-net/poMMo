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
 
/**********************************
	INITIALIZATION METHODS
*********************************/
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/import.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();


//Pommo::kill('Importing and Exporting is temporarily disabled until PR15');

// Maximum File Size (in MB) 
$max_file_size = 2;
$smarty->assign('maxSize',$max_file_size * 1024 * 1024);

// Filename (in $_FILES array)
$fname = "csvfile";

if(isset($_POST['submit'])) {

	// POST exists -- set pointer to content
	$fp = false;
	
	if (!empty($_FILES[$fname]['tmp_name']))
		$fp =& fopen($_FILES[$fname]['tmp_name'], "r");
	elseif (!empty($_POST['box'])) {
		$str =& $_POST['box']; 
		
		// wrap $c as a file stream -- requires PHP 4.3.2
		//  for early versions investigate using tmpfile() -- efficient?
		stream_wrapper_register("pommoCSV", "PommoCSVStream")
			or die('Failed to register pommoCSV');
		$fp = fopen("pommoCSV://str", "r+"); 
	}
	
	if(is_resource($fp)) {
		
		if($_POST['type'] == 'txt') { // list of emails 
			$a = array(); 
			while (($data = fgetcsv($fp,2048,',','"')) !== FALSE) {
				foreach($data as $email)
					if(PommoHelper::isEmail($email))
						array_push($a,$email);
			}
			
			// remove dupes
			$a = array_unique($a);
			$emails = array_diff($a, PommoHelper::emailExists($a));
			
			$pommo->set(array(
				'emails' => $emails,
				'dupes' => (count($a) - count($emails))));
			Pommo::redirect('import_txt.php');
		}
		elseif($_POST['type'] == 'csv') { // csv of subscriber data, store first 10 for preview
			$a = array(); $i = 1;
			while (($data = fgetcsv($fp,2048,',','"')) !== FALSE) {
				array_push($a,$data);
					
				if($i > 9) // only get first 10 lines -- move file
					break;
				$i++;
			}
			
			// save file for access after assignments
			move_uploaded_file($pommo->_workDir.'import.csv', $_FILES[$fname]['tmp_name']);
			$pommo->set(array('preview' => $a));
			Pommo::redirect('import_csv.php');
		}
		else {
			$logger->addErr('Unknown Import Type');
		}
	}
}

$smarty->display('admin/subscribers/subscribers_import.tpl');
Pommo::kill();
?>