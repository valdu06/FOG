<?php
/*
 *  FOG - Free, Open-Source Ghost is a computer imaging solution.
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

// Require FOG Base
require_once((defined('BASEPATH') ? BASEPATH . '/commons/config.php' : '../../commons/config.php'));
require_once(BASEPATH . '/commons/init.php');

// Allow AJAX check
if (!$_SESSION['AllowAJAXTasks']) die('FOG Session Invalid');

// No search query - exit
if (!$crit) die('No Query');

// Variables
$hostMan = $core->getHostManager();
$data = array();

// Blackout - replace spaces with wildcards
$crit = addslashes(preg_replace(array('#[[:space:]]#', '#\*#'), array('%', '%'), urldecode($crit)));

try
{
	$arHosts = $hostMan->search($crit, HostManager::SORT_HOST_ASC);

	$cnt = 0;
	if ( $arHosts != null )
	{
		$cnt = count( $arHosts );
		
		for( $i = 0; $i < $cnt; $i++ )
		{
			$host = $arHosts[$i];
			
			$mac = $host->getMAC();
			$strMac = "";
			if ( $mac != null ) $strMac = $mac->getMACWithColon();
			
			// Minimum fields
			$x = array(	
					'id' 		=> 	$host->getID(),
					'hostname'	=>	$host->getHostname(),
					'mac'		=>	$strMac
					);
			
			// Optional fields - dont send fields that have no data - this decreases ajax overhead
			if ($host->getIPAddress()) $x['ip'] = $host->getIPAddress();
			
			$data[] = $x;
		}
	}
}
catch( Exception $e )
{
	$data['error'] = 'Could not search hosts, Error: ' . $e->getMessage();
}

// Output
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
	// AJAX request - JSON output
	print json_encode($data);
}
else
{
	// Regular request / include - HTML output
	if (count($data))
	{
		foreach ($data AS $item)
		{
			print '<tr id="host-' . $item['hostname'] . '" class="' . (++$i % 2 ? 'alt1' : 'alt2') . '">
			<td><input type="checkbox" name="HID' . $item['id'] . '" checked="checked" /></td>
			<td><span class="icon ping"></span></td>
			<td><a href="?node=host&sub=edit&id=' . $item['id'] . '" title="Edit">' . $item['hostname'] . '</a></td>
			<td>' . $item['mac'] . '</td>
			<td>' . ($item['ip'] ? $item['ip'] : '&nbsp;') . '</td>
			<td><a href="?node=host&sub=edit&id=' . $item['id'] . '"><span class="icon icon-edit" title="Edit: ' . $item['hostname'] . '"></span></a></td>
			</tr>';
		}
		
		msgBox(sprintf('%s hosts found', count($data)));
	}
	else
	{
		// TODO: Move to language variables
		print '<tr><td colspan="7" class="no-active-tasks">' . ($data['error'] ? (is_array($data['error']) ? '<p>' . implode('</p><p>', $data['error']) . '</p>' : $data['error']) : 'No items found') . '</td></tr>';
	}
}