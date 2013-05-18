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
if ( $_SESSION["allow_ajax_host"] )
{
	// Prep query
	$crit = addslashes(preg_replace(array('#[[:space:]]#', '#\*#'), array('%', '%'), urldecode($crit)));
		
	// Query
	$sql = "SELECT * FROM printers WHERE pModel LIKE '%$crit%' OR pAlias LIKE '%$crit%' OR pIP LIKE '%$crit%' OR pPort LIKE '%$crit%' ORDER BY pAlias";
	$res = mysql_query( $sql, $conn ) or die( mysql_error() );
	while( $ar = mysql_fetch_array( $res ) )
	{
		$data[] = array('id' => $ar['pID'], 'model' => $ar['pModel'], 'alias' => $ar['pAlias'], 'port' => $ar['pPort'], 'inf' => $ar['pDefFile'], 'ip' => $ar['pIP']);
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
				print '<tr class="' . (++$i % 2 ? 'alt1' : 'alt2') . '">
				<td>' . $item['model'] . '</td>
				<td>' . $item['alias'] . '</td>
				<td>' . $item['port'] . '</td>
				<td>' . $item['inf'] . '</td>
				<td>' . $item['ip'] . '</td>
				<td class="c"><a href="?node=print&sub=edit&id=' . $item['id'] . '"><span class="icon icon-edit" title="Edit: ' . $item['alias'] . '"></span></a></td>
				</tr>';
			}
		
			msgBox(sprintf('%s printers found', count($data)));
		}
		else
		{
			// TODO: Move to language variables
			print '<tr><td colspan="7" class="no-active-tasks">' . ($data['error'] ? (is_array($data['error']) ? '<p>' . implode('</p><p>', $data['error']) . '</p>' : $data['error']) : 'No items found') . '</td></tr>';
		}
	}
}