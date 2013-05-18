<?php
/*
 *  FOG  is a computer imaging solution.
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
// Prep query
$crit = addslashes(preg_replace(array('#[[:space:]]#', '#\*#'), array('%', '%'), urldecode($crit)));

// Find Images
$sql = "SELECT *, DATE_FORMAT(imageDateTime, '%c-%e-%Y %l:%i %p') AS d FROM images WHERE imageName LIKE '%$crit%' ORDER BY imageName";
$res = mysql_query($sql, $conn) or die(mysql_error());
while ($ar = mysql_fetch_array($res))
{
	// Storage Group Name
	$sql = "SELECT ngName FROM nfsGroups WHERE ngID = '" . $ar["imageNFSGroupID"] . "' LIMIT 1";
	if ($resn = mysql_fetch_array(mysql_query($sql, $conn)))
	{
		$group = $resn["ngName"];
	}
	
	// Only show the first line of the Description - trim removed in favour of overflow / removing superfluous information
	if (preg_match('#\n#', $ar['imageDesc']))
	{
		$ar['imageDesc'] = explode("\n", preg_replace('#\r#', '', $ar['imageDesc']));
		$ar['imageDesc'] = $ar['imageDesc'][0];
	}
	// Replace with nbsp if empty
	$ar['imageDesc'] = ($ar['imageDesc'] ? $ar['imageDesc'] : '&nbsp;');
	
	$data[] = array('id' => $ar['imageID'], 'name' => $ar['imageName'], 'description' => $ar['imageDesc'], 'storagegroup' => $group);
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
			<td><a href="' . "?node=$node&sub=edit&imageid=$item[id]" . '" title="Edit">' . $item['name'] . '</a></td>
			<td>' . substr($item['description'], 0, 35) . '</td>
			<td align="center">' . $item['storagegroup'] . '</td>
			<td align="center"><a href="' . "?node=$node&sub=edit&imageid=$item[id]" . '"><span class="icon icon-edit" title="Edit: ' . $item['name'] . '"></span></a></td>
			</tr>';
		}
	
		msgBox(sprintf('%s images found', count($data)));
	}
	else
	{
		// TODO: Move to language variables
		print '<tr><td colspan="7" class="no-active-tasks">' . ($data['error'] ? (is_array($data['error']) ? '<p>' . implode('</p><p>', $data['error']) . '</p>' : $data['error']) : 'No items found') . '</td></tr>';
	}
}