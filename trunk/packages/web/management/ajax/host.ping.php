<?php
/*
 *  FOG is a computer imaging solution.
 *  Copyright (C) 2007  Chuck Syperski & Jian Zhang
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   any later version.
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

// Require FOG Base - the relative path to config.php changes in AJAX files as these files are included and accessed directly
require_once((defined('BASEPATH') ? BASEPATH . '/commons/config.php' : '../../commons/config.php'));
require_once(BASEPATH . '/commons/init.php');

// This was added in case we need to loop up the hostname by the host id
// TODO: We should probably ensure that hostname is passed instead of the ID
// so we don't incure the overhead
require_once(BASEPATH . '/commons/init.database.php');

try
{
	$ping = $_GET['ping'];
	// Error checking
	if (!$_SESSION['AllowAJAXTasks'])
	{
		throw new Exception(_('FOG session invalid'));
	}
	if (empty($ping) || $ping == 'undefined')
	{
		throw new Exception(_('Undefined host to ping'));
	}
	if (!HostManager::isHostnameSafe($ping))
	{
		throw new Exception(_('Invalid hostname'));
	}
	
	if ( is_numeric($ping) )
	{	
		// ping is a host id
		$Host = new Host($ping);
		$ping = $Host->get('name');
	}

	// Resolve hostname
	$ip = gethostbyname($ping);
	
	// Did the hostname resolve correctly?
	if ($ip == $ping)
	{
		throw new Exception(_('Unable to resolve hostname'));
	}
	
	// Ping IP Address
	$result = $FOGCore->getClass('Ping', $ip)->execute();
	
	// Show error message if not successful
	if ($result !== true)
	{
		// Error from Ping class
		throw new Exception($result);
	}
	
	// Success
	print '1';
}
catch (Exception $e)
{
	// Failure
	print $e->getMessage();
}
