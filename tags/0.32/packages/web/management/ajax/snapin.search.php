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

// Require FOG Base
require_once((defined('BASEPATH') ? BASEPATH . '/commons/config.php' : '../../commons/config.php'));
require_once(BASEPATH . '/commons/init.php');

// Allow AJAX check
if (!$_SESSION['AllowAJAXTasks']) die('FOG Session Invalid');

// No search query - exit
if (!$crit) die('No Query');

// Variables
$data = array();

// Main
if ( $_SESSION["allow_ajax_snapin"] )
{
	// Prep query
	$crit = addslashes(preg_replace(array('#[[:space:]]#', '#\*#'), array('%', '%'), urldecode($crit)));
	
	// Find Snapins
	$sql = "select * from snapins where sName like '%$crit%' or sDesc like '%$crit%' or sFilePath like '%$crit%' ORDER BY sName";
	$res = mysql_query($sql, $conn) or die(mysql_error());
	while( $ar = mysql_fetch_array( $res ) )
	{
		$data[] = array('id' => $ar["sID"], 'name' => $ar["sName"], 'description' => $ar["sDesc"], 'path' => $ar["sFilePath"]);
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
				print '<tr>
				<td><a href="?node=snap&sub=edit&snapinid=' . $item['id'] . '" title="Edit">' . $item['name'] . '</a></td>
				<td>' . substr($item['description'], 0, 45) . '</td>
				<td align="center"><a href="?node=snap&sub=edit&snapinid=' . $item['id'] . '"><span class="icon icon-edit" title="Edit: ' . $item['name'] . '"></span></a></td>
				</tr>';
			}
		
			msgBox(sprintf('%s snap-ins found', count($data)));
		}
		else
		{
			// TODO: Move to language variables
			print '<tr><td colspan="7" class="no-active-tasks">' . ($data['error'] ? (is_array($data['error']) ? '<p>' . implode('</p><p>', $data['error']) . '</p>' : $data['error']) : 'No items found') . '</td></tr>';
		}
	}
}
