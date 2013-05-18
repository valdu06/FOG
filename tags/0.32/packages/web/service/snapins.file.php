<?php
/*
 *  FOG is a computer imaging solution.
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
	die( "#!db" );
}

$core = new Core( $db );
$hostManager = $core->getHostManager();
$serviceManager = $core->getClientServiceManager();
$taskMan = $core->getTaskManager();

$tmpMac = $_GET["mac"];
$arMacs = HostManager::parseMacList($tmpMac);

if ( $arMacs == null || count( $arMacs ) == 0 )
	die( "#!im" );

if ( isset( $_GET["taskid"] ) )
{
	try
	{
		$host = $hostManager->getHostByMacAddresses($arMacs);
		if ( $host != null )
		{
			$taskid = $_GET["taskid"];
			if ( ! $taskMan->getCountOfActiveTasksForHost( $host ) > 0)
			{
				$snapinTask = $serviceManager->getSnapinTaskById($taskid);
				if ( $snapinTask != null && $snapinTask->getHostId() == $host->getID() )
				{
					if ( file_exists( $snapinTask->getSnapin()->getFile() ) && is_readable(  $snapinTask->getSnapin()->getFile() ) )
					{
						header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
						header ("Content-Description: File Transfer");
						header ("Content-Type: application/octet-stream");
						header("Content-Length: " . filesize( $snapinTask->getSnapin()->getFile()));
						header("Content-Disposition: attachment; filename=" . basename($snapinTask->getSnapin()->getFile()));
						@readfile( $snapinTask->getSnapin()->getFile()); 	
						$serviceManager->completeSnapinTask($snapintask, -1, "Pending...");
					}		
					
				}
			}	
		}
	}
	catch( Exception $e ) {}
	
}

?>
