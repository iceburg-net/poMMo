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
 
 /*
bMail templating system.  

//Set values to FALSE/empty/or leave out if a item is undesred.
 * 

$_nosidebar = TRUE; // set this to TRUE to leave out right sidebar

// header settings -->
$_header = "Welcome to bMail";
$_subheader = "Empowering a mailing list near you.";
$_intro = "This is some introductory text";

$_head = "<script ......>" (anything which should go in HTML <head> section')
  
$_nologo = FALSE;
  
$_menu = array();
$_menu[] = "<a href=\"foo.php\">foo</a>";
$_menu[] = "<a href=\"bar.php\">bar</a>";
		
// right bar settings -->
$_nomenu = TRUE;
$_nodemo = FALSE;

$_extmenu['name'] = "Extended Menu";
$_extmenu['links'] = array();
$_extmenu['links'][] = "<a href=\"foo.php\">foo</a>";
$_extmenu['links'][] = "<a href=\"bar.php\">bar</a>";
 * 
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">


<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>. ..bMail.. .</title>
<?php if (empty($_css))
	echo '<link href="'.bm_baseUrl.'/inc/css/bmail.css" rel="stylesheet" type="text/css" />';
else 
	echo "<link href=\"".$_css."\" rel=\"stylesheet\" type=\"text/css\" />";
	
if (!empty($_head))
	echo $_head;
?>

<!--[if lt IE 7.]>
<script defer type="text/javascript" src="<?php echo bm_baseUrl;?>/inc/js/pngfix.js"></script>
<![endif]-->

</head>

<body>
<a name="top" id="top"></a>
<center>

<div id="menu">
<?php // print out a top menu
// setup a menu array before calling top.php -- ie. $_menu[] = "<a href=\"blah.php\">blah</a>"; //
foreach ( array_keys($_menu) as $key ) {
	$element =& $_menu[$key];
 	echo $element . " ";
}
?>
&nbsp;
</div>
<!-- end menu -->
		
<?php // print out header/subheader
if (!empty($_header) || !empty($_subheader)) {
	echo "<div id=\"header\">\n";
	if (!empty($_header))
		echo "<h1>".$_header."</h1>\n";
	if (!empty($_subheader))
		echo "<h2>".$_subheader."</h2>\n";
	echo "</div>\n<!-- end header -->\n";
}
?>

<div id="content">
	
<?php if (empty($_nosidebar)) {
include('right.php');

if (!empty($_intro))	//print out into text
	echo "<p class=\"introduction\">\n".$_intro."\n</p>\n";
?>		
	
<div id="mainbar">
<?php }

if (bm_debug == 'on' && isset($dbo) && is_object($dbo))
	$dbo->debug(TRUE);
?>