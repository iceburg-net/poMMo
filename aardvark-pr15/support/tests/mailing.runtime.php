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
 
// set maximum runtime of this script in seconds (Default: 80). 
$maxRunTime = 90;
if (ini_get('safe_mode'))
	$maxRunTime = ini_get('max_execution_time') - 10;
else
	set_time_limit(0);

define('_poMMo_support', TRUE);
require ('../../bootstrap.php');
$pommo->init();

echo 'Initial Run Time: '.ini_get('max_execution_time').' seconds <br>';
echo '<br/> SLEEPING FOR 90 SECONDS -- FAILED IF "SUCCESS" NEVER OUTPUTTED';
echo '<hr>';
ob_flush(); flush();
$i = 0;
while ($i < 90) {
	$i += 10;
	sleep(10);
	echo "$i <br />"; 
	ob_flush(); flush();
}

die('<hr>SUCCESS');