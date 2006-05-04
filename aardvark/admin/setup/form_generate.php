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
 
 // Generates a simple HTML form based from active subscriber criteria
 
 define('_IS_VALID', TRUE);
 
 require('../../bootstrap.php');
 require_once(bm_baseDir.'/inc/db_demographics.php');
 $poMMo =& fireup("secure");
 $dbo = & $poMMo->openDB();

 
// URL which processes the form input + adds (or warns) subscriber to pending table.
$signup_url = "http://" . $_SERVER['HTTP_HOST'] . bm_baseUrl . "/user/process.php";
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
</head>

<?php
$form_name = "signup";
?>
			

<body>

<br>
<em>VIEW THE SOURCE TO COPY, PASTE, EDIT, AND SAVE THE FORM TO AN APPROPRIATE LOCATION ON YOUR WEBSITE</em>
<br><br><br>



<hr>
<div align="center"><b><?php echo $poMMo->_config['list_name']; ?> Subscriber Form</b></div>
<hr>

<!-- 	Set "ACTION" to the URL of poMMo's process.php
		process.php located in the "user" directory of your poMMo installation.
		** poMMo attempted to detect this location, and it may not need to be changed. ** -->
		
<form action="<?php echo $signup_url; ?>" method="POST" name="<?php echo $form_name; ?>">

<i>Fields in <font color="red">RED</font> are required.</i><br>

<br>

<!--	Email field must be named "bm_email" -->
<font color="red"><LABEL for="bm_email">Your Email: </LABEL></font>
	<input type="text" name="bm_email" maxlength="60">
<br>
<?php

$demographics = & dbGetDemographics($dbo, 'active');
foreach (array_keys($demographics) as $demographic_id) {
	$demographic = & $demographics[$demographic_id];
	
	if ($demographic['required'] == 'on')
		echo "\n\n <!-- BEGIN INPUT FOR REQUIRED FIELD ".$demographic['name']." --> \n<p>\n <font color=\"red\"><LABEL for=\"d[".$demographic_id."]\">".db2str($demographic['prompt']).": </LABEL></font>\n\t";
	else
		echo "\n\n <!-- BEGIN INPUT FOR FIELD ".$demographic['name']." --> \n<p>\n<LABEL for=\"d[".$demographic_id."]\">".db2str($demographic['prompt']).": </LABEL>\n\t";
	
	switch ($demographic['type']) {
		case "checkbox": // checkbox	
			if (empty($demographic['normally']))
				echo "\t<input type=\"checkbox\" name=\"d[".$demographic_id."]\">";
			else
				echo "\t<input type=\"checkbox\" name=\"d[".$demographic_id."]\" checked>";
			break;
			
		case "multiple": // select
		
			echo "\t<select name=\"d[".$demographic_id."]\">\n";
			
			echo "\t  <option value=\"\"> Please Choose...\n";
			
			foreach ($demographic['options'] as $option) {
				
				if (!empty($demographic['normally']) && $option == $demographic['normally'])
					echo "\t  <option value=\"".db2str($option)."\" selected> ".db2str($option)."\n";
				else
					echo "\t  <option value=\"".db2str($option)."\"> ".db2str($option)."\n";
			}			
			echo "</select>";
					
			break;
			
		case "text": // select
		
			if (empty($demographic['normally']))
				echo "<input type=\"text\" name=\"d[".$demographic_id."]\" maxlength=\"60\">";
			else
				echo "<input type=\"text\" name=\"d[".$demographic_id."]\" maxlength=\"60\" value=\"".db2str($demographic['normally'])."\">";
			break; 
			
		case "date": // select
		
			break; 
			
		case "number": // select
		
			break; 
	
		default:
			break;
	}
	
	echo "\n</p>\n";
}

?>

<br>

<!--  *** DO NOT CHANGE name="pommo_signup" ! ***
	  If you'd like to change the button text change the "value=" text. -->
<INPUT type="submit" name="pommo_signup" value="Signup"> <INPUT type="reset">

</FORM>


<br>
<br>
<hr>