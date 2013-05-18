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

if ( IS_INCLUDED !== true ) die( _("Unable to load system configuration information.") );

if ( $_GET["rmsnapinid"] != null && is_numeric($_GET["rmsnapinid"]) && $_GET["hostid"] != null && is_numeric($_GET["hostid"]) )
{
	if ( cancelSnapinsForHost( $conn, $_GET["hostid"], $_GET["rmsnapinid"] ) )
	{
		msgBox(_("Snapin Task Removed!"));
		lg(_("Snapin Task Removed").": " . $_GET["rmtasksnap"]);
	}
	else
	{
		msgBox(_("Failed to remove snapin task"));
	}
}

?>
<h2><?php print _("All Active Snapins"); ?></h2>
<?php

$sql = "SELECT 
		* 
	FROM 
		snapinTasks
		inner join snapinJobs on ( snapinTasks.stJobID = snapinJobs.sjID )
		inner join hosts on ( snapinJobs.sjHostID = hosts.hostID )
		inner join snapins on ( snapins.sID = snapinTasks.stSnapinID )
	WHERE
		stState in ( '0', '1' )";
		
$res = mysql_query( $sql, $conn ) or die( mysql_error() );
if ( mysql_num_rows( $res ) > 0 )
{

	echo ( "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=100%>" );
	$cnt = 0;
	echo ( "<tr class=\"header\"><td>"._("Host Name")."</td><td>"._("Snapin")."</td><td>"._("Start Time")."</td><td>"._("State")."</td><td>"._("Kill")."</td></tr>" );
	while( $ar = mysql_fetch_array( $res ) )
	{
		$bgcolor = "alt1";
		if ( $cnt++ % 2 == 0 ) $bgcolor = "alt2";
		if ( $ar[iState] > 0 )
			$bgcolor = "#B8E2B6";

		$state = "N/A";
		if ( $ar["stState"] == 0 )
			$state = "Queued";
		else if ( $ar["stState"] == 1 )
		{	
			$state = "In Progress";
			$bgcolor = "#B8E2B6";				
		}
		
		$hname = $ar["hostName"];

		echo ( "<tr class=\"$bgcolor\"><td>" . $hname . "</td><td>" . $ar["sName"] . "</td><td>" . $ar["sjCreateTime"] . "</td><td>" . $state . "</td><td><a href=\"?node=$_GET[node]&sub=$_GET[sub]&rmsnapinid=" . $ar["sID"] . "&hostid=" . $ar["hostID"] . "\"><span class=\"icon icon-kill\" title=\"Kill task\"></span></a></td></tr>" );
	}
	echo ( "</table>" );
} 
else
{
	echo ( ""._("No Active Snapins found")."" );
}