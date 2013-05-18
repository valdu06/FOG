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
$taskMan = $core->getTaskManager();
$data = array();

// Main
// Prep query
$crit = addslashes(preg_replace(array('#[[:space:]]#', '#\*#'), array('%', '%'), urldecode($crit)));

// Find groups
$sql = "select * from groups where groupName like '%$crit%' ORDER BY groupName asc";
$res = mysql_query( $sql, $conn ) or die( mysql_error() );
while( $ar = mysql_fetch_array( $res ) )
{
	$members = getImageMembersByGroupID($conn, $ar['groupID']);
	$data[] = array(
			'type'		=> 'group',
			'id'		=> $ar['groupID'],
			'name'		=> $ar['groupName'],
			'description'	=> $ar['groupDesc'],
			'count'		=> ($members != null ? count($members) : 0)
			);
}

// Find hosts
try
{
	$arHosts = $hostMan->search($crit, HostManager::SORT_HOST_ASC);
	$cnt = count($arHosts);
	if ($cnt > 0)
	{
		for ($i = 0; $i < $cnt; $i++)
		{
			$host=$arHosts[$i];
			if ($host != null)
			{
				$mac = $host->getMAC();
				$data[] = array(	
						'type'		=> 'host',
						'id'		=> $host->getID(),
						'name'		=> $host->getHostname(),
						'ip'		=> $host->getIPAddress(),
						'mac'		=> $mac->getMACWithColon(),
						'running'	=> ($taskMan->getCountOfActiveTasksForHost( $host ) > 0 ? 1 : 0)
						
						);
			}
		}
	}
}
catch( Exception $e )
{
	$data['error'] = $e->getMessage();
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
		// TODO: Finish this
		// HOST
		// "<tr class=\"header\"><td></td><td><b>&nbsp;<a onclick=\"getContentTask( document.getElementById('tSearch').value + '&sort=$nexthost' );\" href=\"#\" class=\"plainfont\">"._("Host Name")." $hostimg</a></b></td><td><b>&nbsp;<a onclick=\"getContentTask( document.getElementById('tSearch').value + '&sort=$nextip' );\" href=\"#\" class=\"plainfont\">"._("IP Address")." $ipimg</a></b></td><td><b>&nbsp;<a onclick=\"getContentTask( document.getElementById('tSearch').value + '&sort=$nextmac' );\" href=\"#\" class=\"plainfont\">"._("MAC")." $macimg</a></b></td><td><b>&nbsp;"._("Deploy")."</b></td><td><b>&nbsp;"._("Upload")."</b></td><td><b>&nbsp;"._("Advanced")."</b></td></tr>"
		foreach ($data AS $item)
		{
			print '<tr id="host-' . $item['id'] . '">
			<td><div class="ping"></td>
			<td>' . $item['name'] . '</td>
			<td>' . $item['mac'] . '</td>
			<td>' . ($item['ip'] ? $item['ip'] : '&nbsp;') . '</td>
			<td><a href="?node=host&sub=edit&id=' . $item['id'] . '"><span class="icon icon-edit" title="Edit: ' . $item['name'] . '"></span></a></td>
			</tr>';
		}
	
		msgBox(sprintf('%s tasks found', count($data)));
	}
	else
	{
		// TODO: Move to language variables
		print '<tr><td colspan="7" class="no-active-tasks">' . ($data['error'] ? (is_array($data['error']) ? '<p>' . implode('</p><p>', $data['error']) . '</p>' : $data['error']) : 'No items found') . '</td></tr>';
	}
}