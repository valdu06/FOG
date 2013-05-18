<?php

session_cache_limiter("no-cache");
session_start();

require_once( "../../commons/config.php" );

$conn = mysql_connect( MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD);
if ( $conn )
{
	@mysql_select_db( MYSQL_DATABASE );
}

require_once( "../../commons/functions.include.php" );

// Blackout - 10:26 AM 25/05/2011
$Data = array();
$FetchDataInfo = array(	'sites' 	=> 'http://www.fogproject.org/globalusers/',
			'version'	=> 'http://freeghost.sourceforge.net/version/version.php');

foreach ($FetchDataInfo AS $key => $url)
{
	if ($FetchedData = Fetch($url))
	{
		$Data[$key] = $FetchedData;
	}
	else
	{
		$Data['error-' . $key] = _('Error contacting server');
	}
}

print json_encode($Data);