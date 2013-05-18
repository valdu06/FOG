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
<h2><?php print _("All Current Hosts"); ?></h2>
<?php

$hostMan = $core->getHostManager();
$taskMan = $core->getTaskManager();

$hosts = $hostMan->getAllHosts($ordering);
if ( $hosts != null && count( $hosts ) > 0 )
{
	echo ( "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=100%>" );
	$cnt = 0;
	echo ( "<tr class=\"header\"><td>"._("Host Name")."</td><td width=\"170\">"._("MAC")."</td><td width=\"55\" class=\"c\">"._("Deploy")."</td><td width=\"55\" class=\"c\">"._("Upload")."</td><td width=\"55\" class=\"c\">"._("Advanced")."</td></tr>" );
	//echo ( "<tr class=\"header\"><td>"._("Host Name")."</td><td width=\"170\">"._("MAC")."</td><td width=\"30\" class=\"c\"></td><td width=\"30\" class=\"c\"></td><td width=\"30\" class=\"c\"></td></tr>" );
	for( $i = 0; $i < count( $hosts ); $i++ )
	{
			/* @var $host Host */
			$host = $hosts[$i];
		
			if ( $host != null )
			{
				$imgUp = "<a href=\"?node=tasks&type=host&direction=up&noconfirm=" . $host->getID() ."\"><span class=\"icon icon-upload\" title=\"Upload\"></span></a>";
				$imgDown = "<a href=\"?node=tasks&type=host&direction=down&noconfirm=" . $host->getID() ."\"><span class=\"icon icon-download\" title=\"Deploy\"></span></a>";
				$imgAdvanced = "<a href=\"?node=tasks&sub=advanced&hostid=" . $host->getID() ."\"><span class=\"icon icon-advanced\" title=\"Advanced Deployment\"></span></a>";
				if ( $taskMan->getCountOfActiveTasksForHost( $host ) > 0 )
				{
					$imgAdvanced = $imgUp = $imgDown = "<a href=\"?node=tasks&sub=active\"><span class=\"icon icon-taskrunning\" title=\"Task running\"></span></a>";				
				}

				$mac = $host->getMAC();
				$strMac = "";
				if ( $mac != null )
					$strMac = $mac->getMACWithColon();
				
				echo ( "<tr class=\"$bgcolor\"><td>".$host->getHostname()."</td><td>" . $strMac . "</td><td class=\"c\">$imgDown</td><td class=\"c\">$imgUp</td><td class=\"c\">$imgAdvanced</td></tr>" );
			}
	}
	echo ( "</table>" );
}
else
	echo ( _("No hosts found") );
