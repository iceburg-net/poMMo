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
?>

<div id="sidebar">

<?php // print out logo
if (empty($_nologo)) { 
?>
<img src="<?php echo bm_baseUrl; ?>/img/bmail.png" alt="bMail Logo" class="logo" />
<?php }

if (!empty($_extmenu)) { // print out extra menu
		echo "<h1>".$_extmenu['name']."</h1>\n";
		echo "<div class=\"submenu\">";
		foreach ( array_keys($_extmenu['links']) as $key ) {
  					$element =& $_extmenu['links'][$key];
 					echo $element . " ";
				}
		echo "</div>\n<!-- end submenu -->";
	}

	if (empty($_nomenu)) { // print out menu
?>

<h1>Sections</h1>
<div class="submenu">
<a href="<?php echo bm_baseUrl; ?>/admin/setup/admin_setup.php">Setup</a>	
<a href="<?php echo bm_baseUrl; ?>/admin/subscribers/admin_subscribers.php">Subscribers</a>				
<a href="<?php echo bm_baseUrl; ?>/admin/mailings/admin_mailings.php">Mailings</a>				 
</div>
<!-- end submenu -->

<?php 	}
	
if (empty($_notext)) { // print out right-bar text
?>	

<p>
Design modified from <a href="http://www.jameskoster.co.uk/">James Koster's</a> "plain" template. Icons predominately borrowed from the  <a href="http://www.uludag.org.tr/">Pardus</a> project.					
</p>				

<?php }
	if (empty($_nodemo)) {
		if ($bMail->_config['demo_mode'] == "on")
			echo "<p><img src=\"".bm_baseUrl."/img/icons/demo.png\" class=\"sideimage\">Demonstration mode is ON.</p>";
		else
			echo "<p><img src=\"".bm_baseUrl."/img/icons/nodemo.png\" class=\"sideimage\">Demonstration mode is OFF.</p>";
	}
?>
								
</div>
<!-- end sidebar -->