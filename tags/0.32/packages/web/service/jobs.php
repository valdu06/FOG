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

/*
 *  Possible return codes
 *  "#!db" => Database error
 *  "#!im" => Invalid MAC Format
 *  "#!er" => Other error.
 *  "#!ok" => Job Exists -> GO!
 *  "#!nj" => No Job Exists
 *
 */

$mac 		= $_GET["mac"];
$arMacs 	= HostManager::parseMacList($mac);

if ( $arMacs == null || count( $arMacs ) == 0 )
	die( "#!im" );
	
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
$taskMan = $core->getTaskManager();
$hostMan = $core->getHostManager();

try
{
	$host = $hostMan->getHostByMacAddresses( $arMacs );
	
	if ( $host != null )
	{	
		if ( $taskMan->getCountOfActiveTasksForHost( $host ) > 0)
			echo "#!ok";
		else
			echo "#!nj";
	}
	else
		die( "#!er: "._("Host Not found.") );	
}
catch( Exception $e )
{
	die( "#!er:" . $e->getMessage()  );	
}
		
?>
