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

// Exit if no query
if (!$crit) die('No Query');

// Variables
$data = array();

// Main
// Prep query
$crit = addslashes(preg_replace(array('#[[:space:]]#', '#\*#'), array('%', '%'), urldecode($crit)));

$sql = "SELECT * FROM groups WHERE groupName LIKE '%$crit%' OR groupDesc LIKE '%$crit%' ORDER BY groupName";
$res = mysql_query($sql, $conn) or die(mysql_error());
if (mysql_num_rows($res) > 0)
{
	while ($ar = mysql_fetch_array($res))
	{
		$members = getImageMembersByGroupID($conn, $ar['groupID']);
		$data[] = array('id' => $ar['groupID'], 'name' => $ar['groupName'], 'count' => ($members != null ? count($members) : 0), 'description' => $ar['groupDesc']);
	}
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
			print '<tr id="host-' . $item['id'] . '" class="' . (++$i % 2 ? 'alt1' : 'alt2') . '">
			<td><a href="?node=group&sub=edit&groupid=' . $item['id'] . '" title="Edit">' . $item['name'] . '</a></td>
			<td>' . $item['description'] . '</td>
			<td>' . $item['count'] . '</td>
			<td class="c"><a href="?node=group&sub=edit&groupid=' . $item['id'] . '"><span class="icon icon-edit" title="Edit: ' . $item['name'] . '"></span></a></td>
			</tr>';
		}
	
		msgBox(sprintf('%s groups found', count($data)));
	}
	else
	{
		// TODO: Move to language variables
		print '<tr><td colspan="7" class="no-active-tasks">' . ($data['error'] ? (is_array($data['error']) ? '<p>' . implode('</p><p>', $data['error']) . '</p>' : $data['error']) : 'No items found') . '</td></tr>';
	}
}