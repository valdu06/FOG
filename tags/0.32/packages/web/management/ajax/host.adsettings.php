<?php
/*
 *  FOG is a computer imaging solution.
 *  Copyright (C) 2007  Chuck Syperski & Jian Zhang
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 */
// Disable caching
@header("Cache-Control: no-cache");

// Require FOG Base
require_once((defined('BASEPATH') ? BASEPATH . '/commons/config.php' : '../../commons/config.php'));
require_once(BASEPATH . '/commons/init.php');

// Allow AJAX check
if (!$_SESSION['AllowAJAXTasks']) die('FOG Session Invalid');

// Variables
$data = array( 'domain' => $core->getGlobalSetting('FOG_AD_DEFAULT_DOMAINNAME'),
	       'ou' => $core->getGlobalSetting('FOG_AD_DEFAULT_OU'),
	       'user' => $core->getGlobalSetting('FOG_AD_DEFAULT_USER'), 
               'pass' => $core->getGlobalSetting('FOG_AD_DEFAULT_PASSWORD'));


if (true||strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
	// AJAX request - JSON output
	print json_encode($data);
}
	

