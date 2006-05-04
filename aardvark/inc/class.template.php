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

require(bm_baseDir.'/inc/smarty/Smarty.class.php');
		
// wrapper class around smarty
class bTemplate extends Smarty {
	
	// custom display function to fall back to "default" theme if template file not found
	// also assigns any poMMo errors or messages
	function display($resource_name, $cache_id = null, $compile_id = null, $display = false) {
		
		// attempt to load the theme's requested template
		if (!is_file($this->template_dir.'/'.$resource_name))
			// template file not existant in theme, fallback to "default" theme
			if (!is_file($this->_themeDir.'default/'.$resource_name))
				// requested template file does not exist in "default" theme, die.
				die('<img src="'.bm_baseUrl.'/img/icons/alert.png" align="middle">'.$resource_name.': '._T('Template file not found in default theme.'));
			else
				$resource_name = $this->_themeDir.'default/'.$resource_name;
		
		global $poMMo;
		if ($poMMo->logger->isMsg()) 
			$this->assign('messages',$poMMo->logger->getMsg());
		if ($poMMo->logger->isErr())
			$this->assign('errors',$poMMo->logger->getErr());
		
		return parent::display($resource_name, $cache_id = null, $compile_id = null, $display = false);;
	}
	
	function prepareForForm() {
		$this->plugins_dir[] = bm_baseDir.'/inc/smarty-plugins/validate';
		require(bm_baseDir.'/inc/class.smartyvalidate.php');
		
		// assign isForm to TRUE, used by header.tpl to include form CSS/Javascript in HTML HEAD
		$this->assign('isForm',TRUE);
		
		/*
		// strip out those bastard slashes
		if (get_magic_quotes_gpc()) {
			if (!empty($_POST))
				$_POST = bmStripper($_POST);
			if (!empty($_GET))
				$_GET = bmStripper($_GET);
		}
		*/
	}
	
	// Loads demographic data into template, as well as _POST (or a saved subscribeForm). 
	function prepareForSubscribeForm() {
		require_once (bm_baseDir . '/inc/db_demographics.php');
		global $dbo;
		global $poMMo;
		
		// Get array of demographics. Key is ID, value is an array of the demo's info
		$demographics = dbGetDemographics($dbo);
		if (!empty($demographics))
			$this->assign('demographics', $demographics);
		
		// assign email & demographic values if they exist in POST
		require_once(bm_baseDir.'/inc/lib.txt.php');
		
		// process.php stores form values in Session (so they don't get lost). Attempt to reload them
		$input = $poMMo->get();
		if (isset($input['saveSubscribeForm'])) {
			$_POST = $input['saveSubscribeForm'];
			$poMMo->dataClear(); // TODO -> should poMMo data be cleared once "gotten" --> like the logger..?
		}
		$this->assign($_POST);

	}
}
?>