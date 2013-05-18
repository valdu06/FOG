<?php
/*
 *  FOG is a computer imaging solution.
 *  Copyright (C) 2007  SyperiorSoft Inc. (Chuck Syperski & Jian Zhang)
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

/*
 *  Possible return codes
 *  "#!db" => Database error
 *  "#!im" => Invalid MAC Format
 *  "#!ac" => Invalid Action Command
 *  "#!nh" => Host Not Found
 *  "#!us" => Invalid User
 *  "#!er" => Other error.
 *  "#!ok" => record accepted!
 *
 */

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

$mac 		= strtolower( base64_decode($_GET["mac"]));
$arMacs 	= HostManager::parseMacList($mac);

if ( $arMacs == null || count( $arMacs ) == 0 )
	die( "#!im" );

$hostMan = $core->getHostManager();
$serviceMan = $core->getClientServiceManager();

try
{
	$host = $hostMan->getHostByMacAddresses( $arMacs );

	if ( $host != null  )
	{
		$action = strtolower( base64_decode($_GET["action"] ) );
		$user = trim( strtolower( base64_decode( $_GET["user"] ) ) );

		$arUser = explode( chr(92), $user );

		if ( count( $arUser ) == 2 )
			$user = $arUser[1];

		$desc = "";

		$date = new Date(time());
		$tmpDate = base64_decode( $_GET["date"] );
		if ( $tmpDate != null && strlen( $tmpDate ) > 0 )
		{
			$date = new Date(strtotime($tmpDate));
			$desc = _("Replay from journal: real insert time"). " " . $date->toString("M j, Y g:i:s a");			
		}
			
		$actionText = "";
		if ( $action == "login" )
		{
			if ( $user == null ) 
				die( "#!us" );
			$actionText = "1";
		}
		else if ( $action == "start" )
			$actionText = "99";
		else
			$actionText = "0";			

		$loginEntry = new LoginEntry(-1, $host->getId(), $user, $actionText, $desc, $date);

		if ( $serviceMan->addLoginEntry($loginEntry) )
			echo "#!ok";
		else
			echo "#!db" ;
	}
	else
		die( "#!im" );
}
catch( Exception $e )
{
	die( "#!er: " . $e->getMessage() );
}	
?>
