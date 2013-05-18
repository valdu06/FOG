<?php
/*
 *  FOG  is a computer imaging solution.
 *  Copyright (C) 2007  Chuck Syperski & Jian Zhang
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   any later version.
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

function __autoload($class_name) 
{
	require( "../lib/fog/" . $class_name . '.class.php');
}

require_once( "../commons/config.php" );
require_once( "../commons/functions.include.php" );

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
	die( base64_encode("#!db") );
}

/*
 *  Possible return codes
 *  "#!db" => Database error
 *  "#!im" => Invalid MAC Format
 *  "#!er" => Other error.
 *  "#!np" => No Printers found.
 *  "#!mg=x" => management level = x where x is 0, 1, or 2
 *
 */

$core = new Core( $db );
$hostManager = $core->getHostManager();
$serviceManager = $core->getClientServiceManager();

$tmpMac = $_GET["mac"];
$arMacs = HostManager::parseMacList($tmpMac);

if ( $arMacs == null || count( $arMacs ) == 0 )
	die( base64_encode("#!im"));


try
{
	$host = $hostManager->getHostByMacAddresses( $arMacs );
	if ( $host != null  )
	{
		$level = $host->getPrinterManagementLevel();
		if ( $level == null || $level == Host::PRINTER_MANAGEMENT_UNKNOWN )
			$level = Host::PRINTER_MANAGEMENT_NO_MANAGEMENT;

		echo ( base64_encode("#!mg=" . $level ) . "\n" );
		if ( $level > 0 )
		{
			$printers = $serviceManager->getAllPrintersForHost( $host );
			if ( $printers != null )
			{
				for( $i = 0; $i<count( $printers); $i++ )
				{
					$printer = $printers[$i];
					if ( $printer != null )
					{
						echo base64_encode($printer->getPort() . "|" .$printer->getInfFile() .  "|" .$printer->getModel() . "|".$printer->getAlias() . "|".$printer->getIp() . "|" . ($printer->isDefault() ? "1" : "0") );
						echo ( "\n" );
					}
				}
			}
		}
	}
	else
		die( base64_encode("#!im"));
}
catch( Exception $e )
{
	die( base64_encode("#!er: " . $e->getMessage()) );
}
?>
