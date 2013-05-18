<?php
require_once( "../commons/config.php" );
require_once( "../commons/functions.include.php" );

$conn = mysql_connect( MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD);
if ( $conn )
{
	if ( ! mysql_select_db( MYSQL_DATABASE, $conn ) ) die( _("Unable to select database") );
}
else
{
	die( _("Unable to connect to Database") );
}


$storagedir = getSetting($conn, "FOG_NFS_DATADIR");
$bytes = ( disk_total_space( $storagedir ) - disk_free_space( $storagedir ) );
$gb = round( ( ( ($bytes / 1024) / 1024) /1024), 2);
echo ( $gb . " GB"); 
?>
