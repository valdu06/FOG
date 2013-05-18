<?php
/****************************************************
 * FOG Hook Manager
 *	Author:		Blackout
 *	Created:	8:57 AM 31/08/2011
 *	Revision:	$Revision: 792 $
 *	Last Update:	$LastChangedDate: 2011-10-07 02:02:58 +0000 (Fri, 07 Oct 2011) $
 ***/

class HookManager
{
	public $logLevel = 0;
	
	private $data;
	private $events = array(
		// Global
		'CSS',
		'JavaScript',
		'MainMenuData',				// data => array
		'SubMenuData',				// FOGSubMenu => FOGSubMenu Object
		'MessageBox',				// data => string
		
		// Host Management
		// List / Search
		'HostTableHeader',
		'HostData',
		'HostAfterTable',
		// Edit
		'HostEditUpdate',			// host => Host Object
		'HostEditUpdateSuccess',		// host => Host Object
		'HostEditUpdateFail',			// host => Host Object
		'HostEditConfirmMACUpdate',		// host => Host Object
		'HostEditConfirmMACUpdateSuccess',	// host => Host Object, mac = MACAddress Object
		'HostEditConfirmMACUpdateFail',		// host => Host Object, mac = MACAddress Object
		'HostEditADUpdate',
		'HostEditADUpdateSuccess',
		'HostEditADUpdateFail',
		'HostEditAddSnapinUpdate',
		'HostEditAddSnapinUpdateSuccess',
		'HostEditAddSnapinUpdateFail',
		'HostEditRemoveSnapinUpdate',
		'HostEditRemoveSnapinUpdateSuccess',
		'HostEditRemoveSnapinUpdateFail',
		
		// Group Management
		'GroupTableHeader',
		'GroupData',
		'GroupAfterTable',
		
		// Image Management
		'ImageTableHeader',
		'ImageData',
		'ImageAfterTable',
		
		// Storage Node Management
		// All Storage Nodes
		'StorageGroupTableHeader',
		'StorageGroupData',
		'StorageGroupAfterTable',
		// All Storage Groups
		'StorageNodeTableHeader',
		'StorageNodeData',
		'StorageNodeAfterTable',
		
		// Snapin Management
		'SnapinTableHeader',
		'SnapinData',
		'SnapinAfterTable',
		
		// Printer Management
		'PrinterTableHeader',
		'PrinterData',
		'PrinterAfterTable',
		
		// Task Management
		// Active Tasks
		'TasksActiveTableHeader',
		'TasksActiveData',
		'TasksActiveAfterTable',
		'TasksActiveRemove',
		'TasksActiveRemoveSuccess',
		'TasksActiveRemoveFail',
		'TasksActiveForce',
		'TasksActiveForceSuccess',
		'TasksActiveForceFail',
		// Search
		'TaskData',
		'TasksSearchTableHeader',
		// List Hosts
		'TasksListHostTableHeader',
		'TasksListHostData',
		'TasksListHostAfterTable',
		// List Group
		'TasksListGroupTableHeader',
		'TasksListGroupData',
		'TasksListGroupAfterTable',
		// Scheduled Tasks
		'TasksScheduledTableHeader',
		'TasksScheduledData',
		'TasksScheduledAfterTable',
		'TasksScheduledRemove',
		'TasksScheduledRemoveSuccess',
		'TasksScheduledRemoveFail',
		// Active Multicast Tasks
		'TasksActiveMulticastTableHeader',
		'TasksActiveMulticastData',
		'TasksActiveMulticastAfterTable',
		// Active Snapins
		'TasksActiveSnapinsTableHeader',
		'TasksActiveSnapinsData',
		'TasksActiveSnapinsAfterTable',
		'TasksActiveSnapinsRemove',			// id => snapinID, hostID => hostID
		'TasksActiveSnapinsRemoveSuccess',		// id => snapinID, hostID => hostID
		'TasksActiveSnapinsRemoveFail',			// id => snapinID, hostID => hostID
		
		// User Management
		'USER_DATA',
		'USER_ADD_SUCCESS',				// User Object
		'USER_ADD_FAIL',				// User Object
		'USER_DELETE_SUCCESS',				// User Object
		'USER_DELETE_FAIL',				// User Object
		'USER_UPDATE_SUCCESS',				// User Object
		'USER_UPDATE_FAIL',				// User Object
		
		// Login
		'Login',					// username => string, password => string
		'LoginSuccess',					// username => string, password => string, user => User Object
		'LoginFail',					// username => string, password => string
		
		// Logout
		'Logout',
	);
	
	function __construct()
	{
		// Cannot load on init as each hook requires $HostManager - this variable isnt avaiable until __construct() returns
		//$this->load();
	}
	
	function register($event, $function)
	{
		try
		{
			if (!is_array($function) || count($function) != 2)
			{
				throw new Exception('Function is invalid');
			}
			
			if (!method_exists($function[0], $function[1]))
			{
				throw new Exception('Function does not exist');
			}
			
			if (!in_array($event, $this->events))
			{
				throw new Exception('Invalid event');
			}
			
			if (!($function[0] instanceof Hook))
			{
				throw new Exception('Not a valid hook class');
			}
			
			$this->log(sprintf('Registering Hook: Event: %s, Function: %s', $event, print_r($function, 1)));

			$this->data[$event][] = $function;
			
			return true;
		}
		catch (Exception $e)
		{
			$this->log(sprintf('Could not register Hook: Error: %s, Event: %s, Function: %s', $e->getMessage(), $event, print_r($function, 1)));
		}
			
		return false;
	}
	
	function unregister()
	{
	
	}
	
	function processEvent($event, $arguments = array())
	{
		if ($this->data[$event])
		{
			foreach ($this->data[$event] AS $function)
			{
				// Is hook active?
				if ($function[0]->active)
				{
					$this->log(sprintf('Running Hook: Event: %s, Class: %s', $event, get_class($function[0]), $function[0]));
				
					call_user_func($function, array_merge(array('event' => $event), (array)$arguments));
				}
				else
				{
					$this->log(sprintf('Inactive Hook: Event: %s, Class: %s', $event, get_class($function[0]), $function[0]));
				}
			}
		}
	}
	
	function load()
	{
		global $HookManager;
	
		$hookDirectory = BASEPATH . '/lib/hooks/';
		$hookIterator = new DirectoryIterator($hookDirectory);
		foreach ($hookIterator AS $fileInfo)
		{
			if ($fileInfo->isFile() && substr($fileInfo->getFilename(), -8) == 'hook.php')
			{
				include($hookDirectory . '/' . $fileInfo->getFilename());
			}
		}
	}
	
	// Moved to OutputManager - remove once all code has been converted
	function processHeaderRow($templateData, $attributeData = array(), $wrapper = 'td')
	{
		// Loop data
		foreach ($templateData AS $i => $content)
		{
			// Create attributes data
			$attributes = array();
			foreach ((array)$attributeData[$i] as $attributeName => $attributeValue)
			{
				// Format into HTML attributes -> push into attributes array
				$attributes[] = sprintf('%s="%s"', $attributeName, $attributeValue);
			}

			// Push into results array
			$result[] = sprintf('<%s%s>%s</%s>',	$wrapper,
								(count($attributes) ? ' ' . implode(' ', $attributes) : ''),
								$content,
								$wrapper);
			
			// Reset
			unset($attributes);
		}
		
		// Return result
		return implode("\n", $result);
	}
	
	// Moved to OutputManager - remove once all code has been converted
	function processRow($data, $templateData, $attributeData = array(), $wrapper = 'td')
	{
		// Loop template data
		foreach ($templateData AS $i => $template)
		{
			// Create find and replace arrays for data
			foreach ($data AS $dataName => $dataValue)
			{
				$dataFind[] = '#%' . $dataName . '%#';
				$dataReplace[] = $dataValue;
			}
			// Remove any other data keys not found
			$dataFind[] = '#%\w+%#';
			$dataReplace[] = '';
			
			// Create attributes data
			$attributes = array();
			foreach ((array)$attributeData[$i] as $attributeName => $attributeValue)
			{
				// Format into HTML attributes -> push into attributes array
				$attributes[] = sprintf('%s="%s"', $attributeName, $attributeValue);
			}
			
			// Replace variables in template with data -> wrap in $wrapper -> push into $result
			$result[] = sprintf('<%s%s>%s</%s>',	$wrapper,
								(count($attributes) ? ' ' . implode(' ', $attributes) : ''),
								preg_replace($dataFind, $dataReplace, $template),
								$wrapper);
			
			// Reset
			unset($dataFind, $dataReplace);
		}
		
		// Return result
		return implode("\n", $result);
	}
	
	private function log($txt, $level = 1)
	{
		if (!$this->isAJAXRequest() && $this->logLevel >= $level)
		{
			printf('[%s] %s%s', date("d-m-Y H:i:s"), trim(preg_replace(array("#\r#", "#\n#", "#\s+#", "# ,#"), array("", " ", " ", ","), $txt)), "<br />\n");
		}
	}
	
	function isAJAXRequest()
	{
		return (strtolower(@$_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ? true : false);
	}
}