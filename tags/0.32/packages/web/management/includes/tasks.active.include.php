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
require_once('../commons/config.php');
require_once(BASEPATH . '/commons/init.php');

if ( IS_INCLUDED !== true ) die( _("Unable to load system configuration information.") );

if ( $_GET[rmtask] != null && is_numeric($_GET[rmtask]) )
{
	if ( ! ftpDelete( getSetting( $conn, "FOG_TFTP_PXE_CONFIG_DIR" ) . "01-" . str_replace ( ":", "-", strtolower($_GET[mac]) ) ) )
	{
		msgBox( _("Unable to delete PXE file") );
	}
	
	$sql = "delete from tasks where taskID = '" . mysql_real_escape_string( $_GET[rmtask] ) . "' limit 1";
	if ( mysql_query( $sql, $conn ) )
	{
		msgBox( _("Task removed, but if the task was in progress or the computer already booted to the Linux Image, you will need to reboot it!") );
		lg( _("Task deleted")." :: $_GET[rmtask]" );
	}
	else
		criticalError( mysql_error(), _("FOG :: Database error!") );
}

if ( $_GET["forcetask"] != null && is_numeric($_GET["forcetask"]) )
{
	$sql = "update tasks set taskForce = '1' where taskID = '" . mysql_real_escape_string( $_GET["forcetask"] ) . "'";
	if ( mysql_query( $sql, $conn ) )
	{
		msgBox( _("Task updated to force!") );
		lg( _("Task set to Force")." :: $_GET[forcetask]" );	
	}
	else
		criticalError( mysql_error(), _("FOG :: Database error!") );
}
$_SESSION["allow_ajax_tasks"] = true;
?>
<h2><?php echo(_("All Active Tasks")); ?></h2>

<table width="100%" border="0" cellpadding="0" cellspacing="0" id="search-content">
	<thead>
		<tr class="header">
			<td><?php echo(_("Hostname")); ?><br /><small><?php echo(_("MAC")); ?></small></td>
			<td width="120" align="center"><?php echo(_("Start Time")); ?></td>
			<td width="40" align="center"><?php echo(_("State")); ?></td>
			<td width="40" align="center"><?php echo(_("Type")); ?></td>
			<td width="40" align="center"><?php echo(_("Force")); ?></td>
			<td width="40" align="center"><?php echo(_("Kill")); ?></td>
		</tr>
	</thead>
	<tbody>
		<?php
		// Include AJAX data on page load
		require('ajax/tasks.active.php');
		?>
	</tbody>
</table>