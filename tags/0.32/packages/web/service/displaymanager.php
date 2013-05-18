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

$tmpMac = $_GET["mac"];
$arMacs = HostManager::parseMacList($tmpMac);

if ( $arMacs == null || count( $arMacs ) == 0 )
	die( "#!im" );

// first get the global display setting values
$x = $core->getGlobalSetting( "FOG_SERVICE_DISPLAYMANAGER_X" );
$y = $core->getGlobalSetting( "FOG_SERVICE_DISPLAYMANAGER_Y" );
$r = $core->getGlobalSetting( "FOG_SERVICE_DISPLAYMANAGER_R" );

// now see if we have some host specific settings
try
{
	$host = $hostManager->getHostByMacAddresses($arMacs);
	$sRes = $serviceManager->getScreenResolutionSettingsForHost( $host );
	if ( $sRes != null && $sRes->isValid() )
	{
		$x = $sRes->getWidth();
		$y = $sRes->getHeight();
		$r = $sRes->getRefresh();
	}
}
catch( Exception $e )
{
	die( "#!er:" . $e->getMessage() );	
}
$string = $x . "x" . $y . "x" . $r;
die( base64_encode( $string ) );
?>
