<?php
/**
 * Copyright (C) 2005, 2006, 2007, 2008  Brice Burgess <bhb@iceburg.net>
 * 
 * This file is part of poMMo (http://www.pommo.org)
 * 
 * poMMo is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published 
 * by the Free Software Foundation; either version 2, or any later version.
 * 
 * poMMo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
 * the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with program; see the file docs/LICENSE. If not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 */
// include smarty template class
require_once($GLOBALS['pommo']->_baseDir . 'inc/lib/smarty/Smarty.class.php');

// wrapper class around smarty
class PommoTheme extends Smarty {

	var $_pommoTheme;
	var $_gettext_func;
	var $_gettext_plural_func;

	function __construct() {
		global $pommo;
		parent::__construct();

		// set theme -- TODO; extend this to the theme selector
		$this->_pommoTheme = 'biscuit';

		$theme_path = $pommo->_baseDir . 'themes/' . $this->_pommoTheme;

		$this->setTemplateDir(array(
			$theme_path,
			$pommo->_baseDir . 'themes/default',
		));

	   $this->setConfigDir($theme_path . '/inc/config' );
       $this->setCompileDir($pommo->_workDir . '/pommo/smarty_c');
	   $this->setCacheDir($pommo->_workDir . '/pommo/smarty_cache');

	   $this->setPluginsDir(array(
		   $pommo->_baseDir . 'inc/lib/smarty/plugins',
		   $pommo->_baseDir . 'inc/lib/smarty-plugins/validate/plugins',
		   $pommo->_baseDir . 'inc/lib/smarty-plugins/gettext',
		   $pommo->_baseDir . 'inc/lib/smarty-plugins/pommo'
	   ));
	   

		// set base/core variables available to all template
		$this->assign('url', array (
			'theme' => array (
				'shared' => $pommo->_baseUrl . 'themes/shared/',
				'this' => $pommo->_baseUrl . 'themes/' . $this->_pommoTheme . '/'
			),
			'base' => $pommo->_baseUrl,
			'http' => $pommo->_http
		));
		$this->assign('config', @array (
			'app' => array (
				'path' => $pommo->_baseDir,
				'weblink' => '<a href="http://pommo.sourceforge.net/">' . Pommo::_T('poMMo Website') . '</a>',
				'dateformat' => PommoHelper::timeGetFormat()
				),
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
			
	}

	// display function falls back to "default" theme if theme file not found
	// also assigns any poMMo errors or messages
	function display($resource_name = null, $cache_id = null, $compile_id = null, $display = false) {
		global $pommo;

		if(!$this->templateExists($resource_name)) {
			Pommo :: kill(sprintf(Pommo::_T('Template file (%s) not found in default or current theme'), $resource_name), true, true);
		}

		if ($pommo->_logger->isMsg())
			$this->assign('messages', $pommo->_logger->getMsg());
		if ($pommo->_logger->isErr())
			$this->assign('errors', $pommo->_logger->getErr());
			
		return parent :: display($resource_name, $cache_id = null, $compile_id = null, $display = false);
	}
	
	function prepareForForm() {
		global $pommo;
		require_once($pommo->_baseDir . 'inc/lib/smarty-plugins/validate/libs/SmartyValidate.class.php');
		$this->assign('vErr',array());
	}

	// Loads field data into template, as well as _POST (or a saved subscribeForm). 
	function prepareForSubscribeForm() {
		global $pommo;
		$dbo =& $pommo->_dbo;
		require_once($pommo->_baseDir . 'inc/helpers/fields.php');

		// Get array of fields. Key is ID, value is an array of the demo's info
		$fields = PommoField::get(array('active' => TRUE,'byName' => FALSE));
		if (!empty ($fields))
			$this->assign('fields', $fields);
			
		foreach ($fields as $field) {
			if ($field['type'] == 'date')
			$this->assign('datePicker', TRUE);
		}
			
		// process.php appends serialized values to _GET['input']
		// TODO --> look into this behavior... necessary?
		if (isset ($_GET['input'])) 
			$this->assign(unserialize($_GET['input']));
		elseif (isset($_GET['Email'])) 
			$this->assign(array('Email' => $_GET['Email']));
		
		$this->assign($_POST);
	}
	
	// returns an array of invalid fields, empty if none.
	// array key == invalid field, value == message
	// e.g. array(field => 'email', 'message' => 'Must be an Email Address');
	function getInvalidFields($form = SMARTY_VALIDATE_DEFAULT_FORM) {
		$o = array();
		
		foreach($_SESSION['SmartyValidate'][$form]['validators'] as $validator) {
			if(!$validator['valid'])
				array_push($o,array('field' => $validator['field'], 'message' => $validator['message']));
		}
		
		return $o;
	}
}

?>
