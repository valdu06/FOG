<?php
/****************************************************
 * FOG Hook: HookDebugger
 *	Author:		Blackout
 *	Created:	8:57 AM 31/08/2011
 *	Revision:	$Revision: 744 $
 *	Last Update:	$LastChangedDate: 2011-09-04 02:02:48 +0000 (Sun, 04 Sep 2011) $
 ***/

// HookDebugger class
class HookDebugger extends Hook
{
	var $name = 'HookDebugger';
	var $description = 'Prints all Hook data to the web page and/or file when a hook is encountered';
	var $author = 'Blackout';
	
	var $active = false;
	
	var $logLevel = 9;
	var $logToFile = false;		// Logs to: lib/hooks/HookDebugger.log
	var $logToBrowser = true;
	
	function run($arguments)
	{
		$this->log(print_r($arguments, 1));
	}
}

// Debug all events
$HookDebugger = new HookDebugger();
foreach ($HookManager->events AS $event)
{
	$HookManager->register($event, array($HookDebugger, 'run'));
}