<?php
/****************************************************
 * FOG Hook: Example Change Table Header
 *	Author:		Blackout
 *	Created:	8:57 AM 31/08/2011
 *	Revision:	$Revision: 743 $
 *	Last Update:	$LastChangedDate: 2011-09-04 01:50:55 +0000 (Sun, 04 Sep 2011) $
 ***/

// Example class
class TestHookChangeTableHeader extends Hook
{
	var $name = 'ChangeTableHeader';
	var $description = 'Remove & add table header columns';
	var $author = 'Blackout';
	
	var $active = false;
	
	function HostTableHeader($arguments)
	{
		// DEBUG output
		foreach ($arguments['templates'] AS $i => $data)
		{
			$this->log(sprintf('Table Rows: i: %s Data: %s', $i, print_r($data, 1)));
		}
		
		// Rename column 'Host Name' -> 'Chicken Sandwiches'
		$arguments['templates'][2] = 'Chicken Sandwiches';
		
		// Override column values & attributes
		$arguments['templates'][5] = 'Edit Me !!';
		$arguments['attributes'][5] = array('width' => '40', 'class' => 'c');
	}
}

// Example: Change Table Header and Data
$HookManager->register('HostTableHeader', array(new TestHookChangeTableHeader(), 'HostTableHeader'));