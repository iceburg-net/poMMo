<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2006 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/
// include smarty template class
Pommo :: requireOnce($GLOBALS['pommo']->_baseDir . 'inc/lib/smarty/Smarty.class.php');

// wrapper class around smarty
class PommoTemplate extends Smarty {

	var $_pommoTheme;

	function PommoTemplate() {
		global $pommo;

		// set theme -- TODO; extend this to the theme selector
		$this->_pommoTheme = 'default';

		// set smarty directories
		$this->_themeDir = $pommo->_baseDir . 'themes/';
		$this->template_dir = $this->_themeDir . $this->_pommoTheme;
		$this->config_dir = $this->template_dir . '/inc/config';
		$this->cache_dir = $pommo->_workDir . '/pommo/smarty';
		$this->compile_dir = $pommo->_workDir . '/pommo/smarty';
		$this->plugins_dir = array (
				'plugins', // the default under SMARTY_DIR
				$pommo->_baseDir . 'inc/lib/smarty-plugins/gettext');

		// set base/core variables available to all template
		$this->assign('url', array (
			'theme' => array (
				'shared' => $pommo->_baseUrl . 'themes/shared/',
				'this' => $pommo->_baseUrl . 'themes/' . $this->_pommoTheme . '/'
			),
			'base' => $pommo->_baseUrl,
			'http' => $pommo->_http
		));
		$this->assign('config', array (
			'app' => array (
				'path' => $pommo->_baseDir,
				'weblink' => '<a href="http://pommo.sourceforge.net/">' . Pommo::_T('poMMo Website') . '</a>',
				'version' => $pommo->_config['version']),
			'site_name' => $pommo->_config['site_name'],
			'site_url' => $pommo->_config['site_url'], 
			'list_name' => $pommo->_config['list_name'],
			'admin_email' => $pommo->_config['admin_email'],
			'demo_mode' => $pommo->_config['demo_mode']));

		// set gettext overload functions (see block.t.php...)
		$this->_gettext_func = array('Pommo','_T'); // calls Pommo::_T($str)
		$this->_gettext_plural_func = array('Pommo','_TP');

		// assign page title
		$this->assign('title', '. ..poMMo.. .');

		// assign section (used for sidebar template)
		$this->assign('section', $pommo->_section);
		
		// destroy pagination data if not in use
		if(isset($_SESSION['SmartyPaginate']) && $_SESSION['SmartyPaginate']['default']['url'] != $_SERVER['PHP_SELF'])
			unset($_SESSION['SmartyPaginate']);
	}

	// display function falls back to "default" theme if theme file not found
	// also assigns any poMMo errors or messages
	function display($resource_name, $cache_id = null, $compile_id = null, $display = false) {
		global $pommo;

		// attempt to load the theme's requested template
		if (!is_file($this->template_dir . '/' . $resource_name))
			// template file not existant in theme, fallback to "default" theme
			if (!is_file($this->_themeDir . 'default/' . $resource_name))
				// requested template file does not exist in "default" theme, die.
				Pommo :: kill(sprintf(Pommo::_T('Template file (%s) not found in default or current theme'), $resource_name));
			else {
				$resource_name = $this->_themeDir . 'default/' . $resource_name;
				$this->template_dir = $this->_themeDir . 'default';
			}
		if ($pommo->_logger->isMsg())
			$this->assign('messages', $pommo->_logger->getMsg());
		if ($pommo->_logger->isErr())
			$this->assign('errors', $pommo->_logger->getErr());

		return parent :: display($resource_name, $cache_id = null, $compile_id = null, $display = false);
	}

	function addPager($limit, $tally) {
		global $pommo;
		
		if(!is_numeric($limit) || !is_numeric($tally))
			Pommo::kill('addPager() was passed illegal vars');

		$this->plugins_dir[] = $pommo->_baseDir . 'inc/lib/smarty-plugins/paginate';
		Pommo :: requireOnce($pommo->_baseDir . 'inc/lib/class.smartypaginate.php');
	
		$this->assign('pagerPrev',Pommo::_T('prev'));
		$this->assign('pagerNext',Pommo::_T('next'));
		
		SmartyPaginate::connect();
		
		if(isset($_REQUEST['resetPager']))
			SmartyPaginate::reset();
			
		SmartyPaginate::setLimit($limit);
		SmartyPaginate::setTotal($tally);
	}
	
	function prepareForForm() {
		global $pommo;

		$this->plugins_dir[] = $pommo->_baseDir . 'inc/lib/smarty-plugins/validate';
		Pommo :: requireOnce($pommo->_baseDir . 'inc/lib/class.smartyvalidate.php');
	}

	// Loads field data into template, as well as _POST (or a saved subscribeForm). 
	function prepareForSubscribeForm() {
		global $pommo;
		$dbo =& $pommo->_dbo;
		Pommo :: requireOnce($pommo->_baseDir . 'inc/helpers/fields.php');

		// Get array of fields. Key is ID, value is an array of the demo's info
		$fields = PommoField::get(array('active' => TRUE));
		if (!empty ($fields))
			$this->assign('fields', $fields);
			
		foreach ($fields as $field) {
			if ($field['type'] == 'date')
			$this->assign('datePicker', TRUE);
		}
			
		// process.php appends serialized values to _GET['input']
		if (isset ($_GET['input'])) 
			$this->assign(unserialize($_GET['input']));
		elseif (isset($_GET['Email'])) 
			$this->assign(array('Email' => $_GET['Email']));
		
		$this->assign($_POST);
	}
}
?>
