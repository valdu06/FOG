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

?>
<h2><?php print _("All Current Storage Nodes"); ?></h2>
<?php

$sql = "SELECT 
		* 
	FROM 
		nfsGroupMembers 
		LEFT OUTER JOIN nfsGroups on ( nfsGroupMembers.ngmGroupID = nfsGroups.ngID ) 
	$orderby";
	
$res = mysql_query( $sql, $conn ) or die( mysql_error() );
if ( mysql_num_rows( $res ) > 0 )
{

	echo ( "<table cellpadding=0 cellspacing=0 border=0 width=100%>" );
	$cnt = 0;
	echo ( "<tr class=\"header\"><td>"._("Node Name")."</td><td>"._("Description")."</td><td>"._("Group")."</td><td>"._("Is Master")."</td><td>"._("Max Clients")."</td><td>"._("Is Enabled")."</td><td class=\"c\" width=\"40\">"._("Edit")."</td></tr>" );
	while( $ar = mysql_fetch_array( $res ) )
	{
		$bgcolor = "alt1";
		if ( $cnt++ % 2 == 0 ) $bgcolor = "alt2";
		echo ( "<tr class=\"$bgcolor\"><td><a href=\"?node=" . $_GET["node"] . "&sub=editnode&storagenodeid=" . $ar["ngmID"] . "\" title=\"Edit\">" . $ar["ngmMemberName"] . "</a></td><td>" . trimString($ar["ngmMemberDesc"], 20) ."</td><td>" . $ar["ngName"] ."</td><td>" . $ar["ngmIsMasterNode"] ."</td><td>" . $ar["ngmMaxClients"] ."</td><td>" . $ar["ngmIsEnabled"] ."</td><td><a href=\"?node=$_GET[node]&sub=editnode&storagenodeid=" . $ar["ngmID"] . "\"><span class=\"icon icon-edit\" title=\"Edit: " . $ar["ngmMemberName"] . "\"></span></td></tr>" );
	}
	echo ( "</table>" );
} 
else
{
	echo ( _("No Storage Groups Nodes Found!") );
}