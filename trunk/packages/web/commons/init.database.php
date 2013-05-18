<?php
/****************************************************
 * FOG Database Initialization
 *	Author:		Blackout
 *	Created:	5:49 PM 27/09/2011
 *	Revision:	$Revision: 836 $
 *	Last Update:	$LastChangedDate: 2012-01-12 03:58:27 +0000 (Thu, 12 Jan 2012) $
 ***/

// Init
require_once(BASEPATH . '/commons/init.php');

// Database
$DatabaseManager = new DatabaseManager(DATABASE_TYPE, DATABASE_HOST, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);

// Update FOGCore with new database connection
$DB = $FOGCore->DB = $DatabaseManager->connect()->DB;

// FOG Locales
if (!isset($_SESSION['locale']))
{
	// Get locale from DB
	$_SESSION['locale'] = $FOGCore->getSetting('FOG_DEFAULT_LOCALE');
	
	// Set locale
	putenv('LC_ALL=' . $_SESSION['locale']);
	setlocale(LC_ALL, $_SESSION['locale']);
}






// Legacy - Clean up when DB classes have been normalized
$conn = $FOGCore->DB->getLink();
if ($FOGCore)
{
	// LEGACY
	$FOGCore->conn = $conn;
	$FOGCore->db = $FOGCore->DB;
}
if (!$conn)
{
	die(_('Unable to connect to Database'));
}