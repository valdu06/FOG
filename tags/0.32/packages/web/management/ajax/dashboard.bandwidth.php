<?php

session_cache_limiter("no-cache");
session_start();

// Allow AJAX check
if (!$_SESSION['AllowAJAXTasks']) die('FOG Session Invalid');

require_once( "../../commons/config.php" );

$conn = mysql_connect( MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD);
if ( $conn )
{
	@mysql_select_db( MYSQL_DATABASE );
}

require_once( "../../commons/functions.include.php" );

// Blackout - 1:34 PM 2/06/2011
$Data = array();

$StorageNodes = mysql_query("SELECT * FROM nfsGroupMembers WHERE ngmIsEnabled = '1'", $conn ) or die( mysql_error() );

// Loop each storage node -> grab stats
while ($Node = mysql_fetch_array($StorageNodes))
{
	// TODO: Need to move interface to per storage group server
	$URL = "http://" . $Node['ngmHostname'] . getSetting($conn, "FOG_NFS_BANDWIDTHPATH") . '?dev=' . $Node['ngmInterface'];
	
	// Fetch bandwidth stats from remote server
	if ($FetchedData = Fetch($URL))
	{
		// Legacy client
		if (preg_match('/(.*)##(.*)/U', $FetchedData, $match))
		{
			$Data[$Node['ngmMemberName']] = array('rx' => $match[1], 'tx' => $match[2]);
		}
		else
		{
			$Data[$Node['ngmMemberName']] = json_decode($FetchedData, true);
		}
	}	
}

print json_encode($Data);