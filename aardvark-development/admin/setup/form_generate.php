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
/**********************************
	INITIALIZATION METHODS
*********************************/
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;
 
// URL which processes the form input + adds (or warns) subscriber to pending table.
$signup_url = "http://" . $_SERVER['HTTP_HOST'] . $pommo->_baseUrl . "user/process.php";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Sample form</title>
</head>

<?php
$form_name = "signup";
?>
<body>

<p><em>VIEW THE SOURCE TO COPY, PASTE, EDIT, AND SAVE THE FORM TO AN APPROPRIATE LOCATION ON YOUR WEBSITE</em></p>

<hr>

<h1><?php echo $pommo->_config['list_name']; ?> Subscriber Form</h1>

<!-- 	Set "ACTION" to the URL of poMMo's process.php
		process.php located in the "user" directory of your poMMo installation.
		** poMMo attempted to detect this location, and it may not need to be changed. ** -->
		
<form method="post" action="<?php echo $signup_url; ?>"name="<?php echo $form_name; ?>">

<p><em>Fields in <strong>bold</strong> are required.</em></p>

<div>
<!--	Email field must be named "bm_email" -->
<label for="email"><strong>Your Email:</strong></label>
<input type="text" name="bm_email" id="email" maxlength="60">
</div>

<?php

$fields = & PommoField::get(array('active' => TRUE));
foreach (array_keys($fields) as $field_id) {
	$field = & $fields[$field_id];
	
	if ($field['required'] == 'on')
		echo "\n<div>\n<!-- BEGIN INPUT FOR REQUIRED FIELD ".$field['name']." -->\n<label for=\"field".$field_id."\"><strong>".$field['prompt'].":</strong></label>\n";
	else
		echo "\n<div>\n<!-- BEGIN INPUT FOR FIELD ".$field['name']." -->\n<label for=\"field".$field_id."\">".$field['prompt'].":</label>\n";
	
	switch ($field['type']) {
		case "checkbox": // checkbox	
			if (empty($field['normally']))
				echo "\n<input type=\"checkbox\" name=\"d[".$field_id."]\" id=\"field".$field_id."\">";
			else
				echo "\n<input type=\"checkbox\" name=\"d[".$field_id."]\" id=\"field".$field_id."\" checked>";
			break;
			
		case "multiple": // select
		
			echo "\n<select name=\"d[".$field_id."]\" id=\"field".$field_id."\">\n";
			
			echo "<option value=\"\"> Please Choose...</option>\n";
			
			foreach ($field['array'] as $option) {
				
				if (!empty($field['normally']) && $option == $field['normally'])
					echo "<option value=\"".htmlspecialchars($option)."\" selected> ".$option."</option>\n";
				else
					echo "<option value=\"".htmlspecialchars($option)."\"> ".$option."</option>\n";
			}			
			echo "</select>\n";
					
			break;
			
		case "text": // select
		case "number": // select
		case "date": // select
		
			if (empty($field['normally']))
				echo "<input type=\"text\" name=\"d[".$field_id."]\" id=\"field".$field_id."\" maxlength=\"60\">\n";
			else
				echo "<input type=\"text\" name=\"d[".$field_id."]\" maxlength=\"60\" value=\"".htmlspecialchars($field['normally'])."\">\n";
			break; 

		default:
			break;
	}

	echo "\n</div>\n";
}

?>

<!--  *** DO NOT CHANGE name="pommo_signup" ! ***
	  If you'd like to change the button text change the "value=" text. -->

<input type="hidden" name="pommo_signup" value="true">
<input type="submit" name="submit" value="Signup">
<input type="reset" name="reset">

</form>

</body>
</html>