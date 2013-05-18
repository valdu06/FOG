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

?>
<h2><?php print _("All Current Groups"); ?></h2>
<?php

$sql = "select * from groups $orderby";
$res = mysql_query( $sql, $conn ) or die( mysql_error() );
if ( mysql_num_rows( $res ) > 0 )
{

	echo ( "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=100%>" );
	$cnt = 0;
	echo ( "<tr class=\"header\"><td>"._("Name")."</td><td width=\"55\">"._("Members")."</td><td width=\"55\">"._("Deploy")."</td><td class=\"c\" width=\"55\">"._("Multicast")."</td><td width=\"55\">"._("Advanced")."</td></tr>" );
	//echo ( "<tr class=\"header\"><td>"._("Name")."</td><td width=\"55\">"._("Members")."</td><td width=\"30\"></td><td class=\"c\" width=\"30\"></td><td width=\"30\"></td></tr>" );
	while( $ar = mysql_fetch_array( $res ) )
	{
		$bgcolor = "alt1";
		if ( $cnt++ % 2 == 0 ) $bgcolor = "alt2";

		$count = 0;
		$members = getImageMembersByGroupID( $conn, $ar["groupID"] );
		if ( $members != null )
			$count = count( $members );
		echo ( "<tr class=\"$bgcolor\"><td>$ar[groupName]</td><td class=\"c\">$count</td><td class=\"c\"><a href=\"?node=tasks&type=group&direction=down&noconfirm=" . $ar["groupID"] ."\"><span class=\"icon icon-download\" title=\"Deploy\"></span></a></td><td class=\"c\"><a href=\"?node=tasks&type=group&direction=downmc&noconfirm=" . $ar["groupID"] ."\"><span class=\"icon icon-multicast\" title=\"Deploy Multicast\"></span></a></td><td class=\"c\"><a href=\"?node=tasks&sub=advanced&groupid=" . $ar["groupID"] ."\"><span class=\"icon icon-advanced\" title=\"Advanced Deployment\"></span></a></td></tr>" );
	}
	echo ( "</table>" );
} 
else
{
	echo ( _("No Groups found") );
}