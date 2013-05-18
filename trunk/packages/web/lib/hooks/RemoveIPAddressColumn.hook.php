<?php
/****************************************************
 * FOG Hook: Remove 'IP Address' column
 *	Author:		Blackout
 *	Created:	1:52 PM 3/09/2011
 *	Revision:	$Revision: 743 $
 *	Last Update:	$LastChangedDate: 2011-09-04 01:50:55 +0000 (Sun, 04 Sep 2011) $
 ***/

// RemoveIPAddressColumn class
class RemoveIPAddressColumn extends Hook
{
	var $name = 'RemoveIPAddressColumn';
	var $description = 'Removes the "IP Address" column from Host Lists';
	var $author = 'Blackout';
	
	var $active = false;
	
	function HostTableHeader($arguments)
	{
		// Remove IP Address column by removing its column template
		unset($arguments['templates'][4]);
	}
	
	function HostData($arguments)
	{
		// Remove IP Address column by removing its column template
		unset($arguments['templates'][4]);
	}
}

// Init
$RemoveIPAddressColumn = new RemoveIPAddressColumn();

// Register hooks
$HookManager->register('HostTableHeader', array($RemoveIPAddressColumn, 'HostTableHeader'));
$HookManager->register('HostData', array($RemoveIPAddressColumn, 'HostData'));