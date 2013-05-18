<?php
/*
 *  FOG is a computer imaging solution.
 *  Copyright (C) 2010 SyperiorSoft Inc. (Chuck Syperski & Jian Zhang)
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
@error_reporting(0);

if ( IS_INCLUDED !== true ) die( "Unable to load system configuration information." );

require_once( "../lib/db/db.php" );

try
{
	$dbman = new DBManager( DB_ID );
	$dbman->setHost( DB_HOST );
	$dbman->setCredentials( DB_USERNAME, DB_PASSWORD );
	$dbman->setSchema( DB_NAME );
	$db = $dbman->connect();
}
catch( Exception $e )
{
	die( "#!db" );
}

require_once( "./lib/ReportMaker.class.php" );

$core = new Core( $db );
$hostMan = $core->getHostManager();

?>
<h2><?php print _("Host Listing Export")." <a href=\"export.php?type=csv\" target=\"_blank\"><img class=\"noBorder\" src=\"images/csv.png\" /></a>"; ?></h2>
<?php

$report = new ReportMaker();
																																																																																																																																																																																																																										
$report->addCSVCell("Host ID");
$report->addCSVCell("Host Name");		
$report->addCSVCell("Host Description");		
$report->addCSVCell("Host Primary MAC");
$report->addCSVCell("Host Pending MAC");
$report->endCSVLine();												
	
$hosts = $hostMan->getAllHostsWithPendingMacs();

if ( $hosts != null && count( $hosts ) > 0 )
{
	for( $i = 0; $i < count( $hosts );$i++ )
	{
		$host = $hosts[$i];
		if ( $host != null )
		{
			$macs = $hostMan->getPendingMacAddressesForHost( $host );
			if ( $macs != null )
			{
				for( $z = 0; $z < count( $macs ); $z++ )
				{
					$mac = $macs[$z];
					if ( $mac != null )
					{
						$report->addCSVCell($host->getID());
						$report->addCSVCell($host->getHostname());		
						$report->addCSVCell($host->getDescription());		
						$report->addCSVCell((String)$host->getMAC());
						$report->addCSVCell((String)$mac);
						$report->endCSVLine();
					}
				}
			}
		}
	}
}
else
{
	$report->addCSVCell("No Results Found");
	$report->endCSVLine();												
}

echo ( "<p>Reporting Complete!</p>" );

$_SESSION["foglastreport"] = serialize( $report );