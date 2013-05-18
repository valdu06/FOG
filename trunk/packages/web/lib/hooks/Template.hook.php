<?php
/****************************************************
 * FOG Hook: Template
 *	Author:		Blackout
 *	Created:	8:57 AM 31/08/2011
 *	Revision:	$Revision: 743 $
 *	Last Update:	$LastChangedDate: 2011-09-04 01:50:55 +0000 (Sun, 04 Sep 2011) $
 ***/

// Hook Template
class HookTemplate extends Hook
{
	var $name = 'Hook Name';
	var $description = 'Hook Description';
	var $author = 'Hook Author';
	
	var $active = false;
	
	function HostData($arguments)
	{
		$this->log(print_r($arguments, 1));
	}
}

// Init class
$HookTemplate = new HookTemplate();

// Hook Event
$HookManager->register('HostData', array($HookTemplate, 'HostData'));