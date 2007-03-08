<?php
/**
 * Copyright (C) 2005, 2006, 2007  Brice Burgess <bhb@iceburg.net>
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
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

if (PommoMailing::isCurrent())
	Pommo::kill(sprintf(Pommo::_T('A Mailing is currently processing. Visit the %sStatus%s page to check its progress.'),'<a href="mailing_status.php">','</a>'));

if ($pommo->_config['demo_mode'] == 'on')
	$logger->addMsg(Pommo::_T('Demonstration Mode is on -- no Emails will actually be sent. This is good for testing settings.'));


// assign language (for wysiwyg)
$smarty->assign('lang',($pommo->_slanguage) ? $pommo->_slanguage : $pommo->_language);	
$smarty->assign('fields',PommoField::get());

$smarty->display('admin/mailings/mailings_start.tpl');
Pommo::kill();
?>