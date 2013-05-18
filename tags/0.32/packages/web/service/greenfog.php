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
$serviceManager = $core->getClientServiceManager();

$gfs = $serviceManager->getGreenFOGActions();

if ( $gfs != null )
{
	for( $i = 0; $i < count( $gfs ); $i++ )
	{
		$gf = $gfs[$i];
		if ( $gf != null )
			echo base64_encode( $gf->getHour() . "@" . $gf->getMinute() . "@" . $gf->getAction() ) . "\n";
	}
}

 
?>
