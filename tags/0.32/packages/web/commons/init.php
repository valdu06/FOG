<?php
// Blackout - 3:15 PM 1/05/2011

// Sanitize valid input variables
foreach (array('groupid','node','id','imageid','sub','snapinid','userid','storagegroupid','storagenodeid','crit','sort', 'userid', 'confirm', 'tab') AS $x) if ($_REQUEST[$x]) $$x = addslashes($_REQUEST[$x]); unset($x);

// Init
session_start();
@error_reporting(0);
@set_magic_quotes_runtime(0);

// Includes
require_once(BASEPATH . "/commons/functions.include.php");
require_once(BASEPATH . "/lib/db/db.php");

// Auto loader
if (!function_exists('__autoload'))
{
	function __autoload($className) 
	{
		try
		{
			$paths = array(BASEPATH . "/lib/fog", BASEPATH . "/management/lib", BASEPATH . "/lib/db");
		       
			foreach ($paths as $p)
			{
				$filename = "{$className}.class.php";
				$filepath = rtrim($p, '/') . '/' . $filename;

				if (file_exists($filepath))
				{
					
					if (!include($filepath)) throw new Exception("Found class file, but could not include file: $filepath");
					return true;
				}
			}
			
			throw new Exception("Could not found class file: $filename, Paths: " . implode(', ', $paths));
		}
		catch (Exception $e)
		{
			die("Failed to load library. Class: $className, Error: " . $e->getMessage());
		}
	}
}

// Database - TODO: Make all database queries use this connection
try
{
	$dbman = new DBManager(DB_ID);
	$dbman->setHost(DB_HOST);
	$dbman->setCredentials(DB_USERNAME, DB_PASSWORD);
	$dbman->setSchema(DB_NAME);
	$db = $dbman->connect();
}
catch( Exception $e )
{
	die( _('Unable to connect to database.') );
}

// Core
$core = new Core( $db );

// Database - this connection gets used... why did we connect using the above?
$conn = mysql_connect(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD);
if ($conn)
{
	if ( getCurrentDBVersion( $conn ) != FOG_SCHEMA )
	{
		if ( $_GET["redir"] != "1" )
		{
			header('Location: ../commons/schemaupdater/index.php?redir=1');
			exit;
		}
	}
	//if (!mysql_select_db(MYSQL_DATABASE, $conn)) die(_('Unable to select database'));
}
else
{
	die(_('Unable to connect to Database'));
}

// Locale & languages
if (!isset($_SESSION['locale'])) $_SESSION['locale'] = $core->getGlobalSetting('FOG_DEFAULT_LOCALE');
putenv('LC_ALL='.$_SESSION['locale']);
setlocale(LC_ALL, $_SESSION['locale']);
bindtextdomain('messages', 'languages');
textdomain('messages');

