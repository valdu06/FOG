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

if ( $_GET["rmtaskmc"] != null && is_numeric($_GET["rmtaskmc"]) )
{	
	deleteMulticastJob( $conn, mysql_real_escape_string( $_GET["rmtaskmc"] ) );
}

?>
<h2><?php print _("All Active Multicast Tasks"); ?></h2>
<?php

$sql = "SELECT 
		count(hosts.hostID) as cnt, 
		multicastSessions.msName,
		multicastSessions.msStartDateTime,
		multicastSessions.msState,
		multicastSessions.msPercent,
		multicastSessions.msID
	FROM 
		(select * from multicastSessions where msState in (0,1)) multicastSessions  
		inner join multicastSessionsAssoc on ( multicastSessionsAssoc.msID = multicastSessions.msID )
		inner join ( select * from tasks where taskState in (0,1) ) tasks on ( multicastSessionsAssoc.tID = tasks.taskID )
		inner join hosts on (taskHostID = hostID)
	GROUP BY
		multicastSessions.msID";	
$res = mysql_query( $sql, $conn ) or criticalError( mysql_error(), _("FOG :: Database error!") );
if ( mysql_num_rows( $res ) > 0 )
{

	echo ( "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=100%>" );
	$cnt = 0;
	echo ( "<tr class=\"header\"><td>"._("Task Name")."</td><td>"._("Hosts")."</td><td>"._("Start Time")."</td><td>"._("State")."</td><td>"._("Status")."</td><td>"._("Kill")."</td></tr>" );
	while( $ar = mysql_fetch_array( $res ) )
	{
		$bgcolor = "alt1";
		if ( $cnt++ % 2 == 0 ) $bgcolor = "alt2";
		if ( $ar[iState] > 0 )
			$bgcolor = "#B8E2B6";

		$state = state2text($ar["msState"]);
		if ( $ar["taskState"] == 0 && hasCheckedIn( $conn, $ar["taskID"] ) )
			$state = "In Line";			

		$hname = $ar["hostName"];
		
		if ( $ar["taskForce"] == "1" )
			$hname = "* " . $hname;
			
		echo ( "<tr class=\"$bgcolor\"><td>" . $ar["msName"] . "</td><td>" . $ar["cnt"] . "</td><td>" . $ar["msStartDateTime"] . "</td><td>" . $state . "</td><td>" . $ar["msPercent"] . "%</td><td><a href=\"?node=" . $_GET["node"] . "&sub=" . $_GET["sub"] . "&rmtaskmc=" . $ar["msID"] . "\"><span class=\"icon icon-kill\" title=\"Kill task\"></span></a></td></tr>" );
	}
	echo ( "</table>" );
} 
else
{
	echo ( "<p class=\"c\">"._("No Active Tasks found")."</p>" );
}