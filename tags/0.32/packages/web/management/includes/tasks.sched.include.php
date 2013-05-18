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
	
if ( IS_INCLUDED !== true ) die( _("Unable to load system configuration information.") );

if ( $_GET["rmid"] != null && is_numeric($_GET["rmid"]) )
{
	$core = new FOGCore($conn);
	if ( $core->stopScheduledTask( new ScheduledTask(null, null, null, null, $_GET["rmid"] ) ) )
	{
		msgBox( _("Scheduled Task removed!") );
		lg( _("Scheduled Task deleted")." :: $_GET[rmid]" );
	}
	else
		criticalError( mysql_error(), _("FOG :: Database error!") );
}

?>
<h2><?php print _("All Scheduled Tasks"); ?></h2>
<?php

$core = new FOGCore($conn);
$tasks = $core->getScheduledTasksByStorageGroupID( "%", true );
if ( $tasks != null && count($tasks) > 0 )
{
	echo ( "<center><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">" );
	echo ( "<tr class=\"header\"><td>"._("Run time")."</td><td>"._("Task Type")."</td><td>"._("Is Group")."</td><td>"._("Group/Host Name")."</td><td>"._("Kill")."</td></tr>" );
	for( $i = 0; $i < count( $tasks ); $i++ )
	{
		$task = $tasks[$i];
		if ( $task != null )
		{
			$timer = $task->getTimer();
			if ( $timer != null )
			{
				$bgcolor = "alt1";
				if ( $i % 2 == 0 ) $bgcolor = "alt2";
				$taskType = getImageAction( $task->getTaskType() );
			
				$hostGroupName = "";
				if ( $task->isGroupBased() )
				{
					$group = $task->getGroup();
					if ( $group != null )
						$hostGroupName = $group->getName();
				}
				else
				{
					$host = $task->getHost();
					if ( $host != null )
						$hostGroupName = $host->getHostName();					
				}
			
				echo ( "<tr class=\"$bgcolor\">" );
					echo ( "<td>" . trimString(  $timer->toString(), 35 ) . "</td><td>" . trimString(  $taskType, 20 ) . "</td><td>" . ($task->isGroupBased() ? "Yes" : "No") . "</td><td>" . trimString($hostGroupName, 20) . "</td><td><a href=\"?node=$_GET[node]&sub=$_GET[sub]&rmid=" . $task->getID() . "\"><span class=\"icon icon-kill\" title=\"Kill task\"></span></a></td>" );
				echo ( "</tr>" );
			}
		}
	}
	echo ( "</table></center>" );
} 
else
{
	echo ( "<center><b>"._("No scheduled Tasks found")."</b></center>" );
}