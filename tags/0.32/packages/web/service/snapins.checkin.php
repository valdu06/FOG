<?php
/*
 *  FOG - Free, Open-Source Ghost is a computer imaging solution.
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

/*
 *  Possible return codes
 *  "#!db" => Database error
 *  "#!im" => Invalid MAC Format
 *  "#!er" => Other error.
 *  "#!it" => Image task exists -> no snapins to be installed
 *  "#!ns" => No Snapins found
 *  "#!ok" => Job Exists -> GO!
 *
 */

$core = new Core( $db );
$hostManager = $core->getHostManager();
$serviceManager = $core->getClientServiceManager();
$taskMan = $core->getTaskManager();

$tmpMac = $_GET["mac"];
$arMacs = HostManager::parseMacList($tmpMac);

if ( $arMacs == null || count( $arMacs ) == 0 )
	die( "#!im" );

try
{
	$host = $hostManager->getHostByMacAddresses($arMacs);
	
	if ( $host != null )
	{	
		
		if ( ! $taskMan->getCountOfActiveTasksForHost( $host ) > 0)
		{
			if ( isset( $_GET["exitcode"] ) && is_numeric( $_GET["exitcode"] ) && isset( $_GET["taskid"] ) && is_numeric( $_GET["taskid"] ) )
			{
				$snapinTask = $serviceManager->getSnapinTaskById($_GET["taskid"]);
				if ( $snapinTask != null )
				{
					$serviceManager->completeSnapinTask($snapinTask, $_GET["exitcode"], $_GET["exitdesc"]);
					echo "#!ok";
				}
			}
			else
			{
				$snapins = $serviceManager->getAllActiveSnapinsForHost($host);
				if ( $snapins != null && count( $snapins ) > 0 )
				{

					$task = $snapins[0];
					if ( $task != null )
					{
							
						$serviceManager->checkInSnapinTask( $task );
						echo "#!ok\n";
						echo "JOBTASKID=" . trim($task->getId()) . "\n" ;
						echo "JOBCREATION=" . trim($task->getCreationDate()) . "\n";
						echo "SNAPINNAME=" . trim($task->getSnapin()->getName()) . "\n";
						echo "SNAPINARGS=" . trim( $task->getSnapin()->getArgs() ) . "\n";
						echo "SNAPINBOUNCE=" . (trim( $task->getSnapin()->reboot() ) ? "1" : "0") . "\n";
						echo "SNAPINFILENAME=" . trim( basename($task->getSnapin()->getFile()) ) . "\n";
						echo "SNAPINRUNWITH=" . trim( $task->getSnapin()->getRunWith() ) . "\n";
						echo "SNAPINRUNWITHARGS=" . trim( $task->getSnapin()->getRunWithArgs() );						
					}
					else
						echo "#!ns";
				}
				else 
					echo "#!ns";
			}
		}
		else
			echo "#!it";
	}
	else
		echo( "#!er:Host Not Found!" );
}
catch( Exception $e )
{
	die( "#!er:" . $e->getMessage() );	
}
?>
