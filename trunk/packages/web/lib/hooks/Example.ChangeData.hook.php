<?php
/****************************************************
 * FOG Hook: Example Change Hostname
 *	Author:		Blackout
 *	Created:	8:57 AM 31/08/2011
 *	Revision:	$Revision: 743 $
 *	Last Update:	$LastChangedDate: 2011-09-04 01:50:55 +0000 (Sun, 04 Sep 2011) $
 ***/

// Example class
class TestHookChangeHostname extends Hook
{
	var $name = 'ChangeHostname';
	var $description = 'Appends "Chicken-" to all hostnames ';
	var $author = 'Blackout';
	
	var $active = false;
	
	function HostData($arguments)
	{
		foreach ($arguments['data'] AS $i => $data)
		{
			// DEBUG
			//$this->log(sprintf('Renaming Host: i: %s Data: %s', $i, print_r($data, 1)));
			
			// Rename host
			$arguments['data'][$i]['hostname'] = 'Chicken-' . $data['hostname'];
		}
	}
}

// Example: Test by changing all hostnames in Host Management
$HookManager->register('HostData', array(new TestHookChangeHostname(), 'HostData'));