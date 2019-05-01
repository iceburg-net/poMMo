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

/**********************************
	INITIALIZATION METHODS
 *********************************/
require ('../../../bootstrap.php');
require_once($pommo->_baseDir.'inc/helpers/templates.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once($pommo->_baseDir.'inc/classes/template.php');
$template = new PommoTheme();
$template->prepareForForm();


if (!SmartyValidate :: is_registered_form() || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___

	SmartyValidate :: connect($template, true);

	SmartyValidate :: register_validator('name', 'name', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('description', 'description', 'dummyValid', false, false, 'trim');

	$vMsg = array ();
	$vMsg['name'] = Pommo::_T('Cannot be empty.');
	$template->assign('vMsg', $vMsg);
	
} else {
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($template);

	if (SmartyValidate :: is_valid($_POST)) {
		// __ FORM IS VALID
		
		$t = PommoMailingTemplate::make(array(
			'name' => $_POST['name'],
			'description' => $_POST['description'],
			'body' => $pommo->_session['state']['mailing']['body'],
			'altbody' => $pommo->_session['state']['mailing']['altbody']
		));
		$id = PommoMailingTemplate::add($t);
		
		if ($id) {
			$logger->addMsg(sprintf(Pommo::_T('Template %s saved.'),'<strong>'.$_POST['name'].'</strong>'));
			$template->assign('success',true);
		}
		else
			$logger->addMsg(Pommo::_T('Error with addition.'));
		
		
	} else {
		// __ FORM NOT VALID
		$logger->addMsg(Pommo::_T('Please review and correct errors with your submission.'));
	}
}

$template->display('admin/mailings/mailing/ajax.addtemplate.tpl');
Pommo::kill();
?>