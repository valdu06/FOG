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

session_start();

// Allow AJAX check
if (!$_SESSION['AllowAJAXTasks']) die('FOG Session Invalid');

if(!isset($_SESSION["locale"]))
        $_SESSION['locale'] = "en_US";

putenv("LC_ALL=".$_SESSION['locale']);
setlocale(LC_ALL, $_SESSION['locale']);
bindtextdomain("messages", "../languages");
textdomain("messages");

function __autoload($class_name) 
{
	require( "../../lib/fog/" . $class_name . '.class.php');
}
 
if ( $_GET["prefix"] != null && strlen($_GET["prefix"]) >= 8 )
{
	require_once( "../../commons/config.php" );
	require_once( "../../commons/functions.include.php" ); 

	require_once( "../../lib/db/db.php" );

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
		die( _("Unable to connect to Database") . "<br />"._("Msg: ") . $e->getMessage() );
	}

	$core = new Core( $db );
	if ( $core->getMACLookupCount() > 0 )
	{
		$mac = new MACAddress( $_GET["prefix"] );
		if ( $mac != null )
		{
			$mac = $core->getMACManufacturer($mac->getMACPrefix());
			echo ($mac == 'n/a' ? 'Unknown' : $mac);
		}
	}
	else
		echo "<a href='?node=about&sub=maclist'>"._("Load MAC Vendors")."</a>";
}
else
	echo "n/a";
?>
