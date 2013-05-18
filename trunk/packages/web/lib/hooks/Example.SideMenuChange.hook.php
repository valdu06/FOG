<?php
/****************************************************
 * FOG Hook: Example.SideMenuChange
 *	Author:		Blackout
 *	Created:	12:10 PM 4/09/2011
 *	Revision:	$Revision: 746 $
 *	Last Update:	$LastChangedDate: 2011-09-04 02:22:33 +0000 (Sun, 04 Sep 2011) $
 ***/

// Hook Template
class HookSubMenuData extends Hook
{
	var $name = 'SubMenuData';
	var $description = 'Example showing how to manipulate SubMenu Data. Adds Menu items under "Host Management"';
	var $author = 'Blackout';
	
	var $active = false;
	
	function SubMenuData($arguments)
	{
		if ($GLOBALS['node'] == 'host')
		{
			// Add a new item under 'Host Management'
			$arguments['FOGSubMenu']->addItems('host', array(_('New Hook Item') => 'http://www.google.com', _('New Hook Item 2') => "newhookitem2"));
			
			if ($GLOBALS['id'])
			{
				// Add a new item under 'Host Management' per Host
				$arguments['FOGSubMenu']->addItems('host', array(_('New Hook Item') => "http://www.google.com", _('New Hook Item 2') => "newhookitem2"), 'id', $GLOBALS['hostname']);
			}
		}
	}
}

// Init class
$HookSubMenuData = new HookSubMenuData();

// Hook Event
$HookManager->register('SubMenuData', array($HookSubMenuData, 'SubMenuData'));