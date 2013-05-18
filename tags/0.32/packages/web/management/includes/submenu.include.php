<?php

// START TESTING
if (!function_exists('__autoload')) {
	function __autoload($class_name) 
	{
		require( "../../lib/fog/" . $class_name . '.class.php');
	}
}


if (file_exists('../../commons/config.php')) require_once( "../../commons/config.php" );
if (file_exists('../../commons/functions.include.php')) require_once( "../../commons/functions.include.php" );

// Core DB API
if (file_exists('../../lib/db/db.php')) require_once( "../../lib/db/db.php" );



try
{
	$dbman = new DBManager( DB_ID );
	$dbman->setHost( DB_HOST );
	$dbman->setCredentials( DB_USERNAME, DB_PASSWORD );
	$dbman->setSchema( DB_NAME );
	$db = $dbman->connect();
}
catch( Exception $e )
{
	die( _("Unable to connect to Database") . "<br />"._("Msg: ") . $e->getMessage() );
}


$core = new Core( $db );

$hostMan = $core->getHostManager();
$groupMan = $core->getGroupManager();
if ($id) $host = $hostMan->getHostById( $id );

if ( IS_INCLUDED !== true ) die( _("Unable to load system configuration information.") );
if ( $currentUser != null && $currentUser->isLoggedIn() )
{
	/*
	// FOGSubMenu How-To: addItems
	// ----------------------
	// Add "Main Menu" items for NODE
	$FOGSubMenu->addItems('NODE', array('Title' => 'link'));
	// Add "NODE Menu" items for NODE, if $nodeid (global) is set
	$FOGSubMenu->addItems('NODE', array('Title' => 'link'), 'nodeid', 'NODE Menu');
	// Add "NODE Menu" items for NODE, if $nodeid (global) is set, custom external link
	$FOGSubMenu->addItems('NODE', array('Title' => 'http://google.com'), 'nodeid', 'NODE Menu');
	// Add "NODE Menu" items for NODE, if $nodeid (global) is set, custom node link (nodeid is appended)
	$FOGSubMenu->addItems('NODE', array('Title' => '?node=blah'), 'nodeid', 'NODE Menu');
	// Add "NODE Menu" items for NODE, if $nodeid (global) is set, custom node link (nodeid is appended)
	$FOGSubMenu->addItems('NODE', array('Title' => '/blah/index.php'), 'nodeid', 'NODE Menu');
	
	// FOGSubMenu How-To: addNotes
	// ----------------------
	// Add static Note
	$FOGSubMenu->addNotes('NODE', array('Title' => 'Information'), 'id variable');
	// Add Note with Callback
	$FOGSubMenu->addNotes('NODE', create_function('', 'return array("banana" => "chicken");'), 'id variable');
	*/
	
	// Variables
	$hostname = ($host ? $host->getHostname() : '-');
	
	// FOGSubMenu namespace
	$FOGSubMenu = new FOGSubMenu();
	
	// About Page
	if ($node == 'about')
	{
		$FOGSubMenu->addItems('about', array(	_('Version Info')	=> 'ver',
							_('License')		=> 'lic',
							_('Kernel Updates')	=> 'kernel',
							_('PXE Boot Menu')	=> 'pxemenu',
							_('Client Updater')	=> 'clientup',
							_('MAC Address List')	=> 'maclist',
							_('FOG Settings')	=> 'settings',
							_('Server Shell')	=> 'shell',
							_('Log Viewer')		=> 'log',
							_('FOG Sourceforge Page')	=> 'http://www.sf.net/projects/freeghost',
							_('FOG Home Page')	=> 'http://freeghost.sf.net/',
					));
	}

	// Group Management
	if ($node == "group")
	{
		$FOGSubMenu->addItems('group', array(	_('New Search')		=> 'search',
							_('List All Groups')	=> 'list',
					));
		if ($groupid)
		{
			// Group Management: Edit
			$FOGSubMenu->addItems('group', array(	_('General')		=> "gen",
								_('Basic Tasks')	=> "tasks",
								_('Membership')		=> "member",
								_('Image Association')	=> "image",
								_('OS Association')	=> "os",
								_('Add Snap-ins')	=> "snapadd",
								_('Remove Snap-ins')	=> "snapdel",
								_('Service Settings')	=> "service",
								_('Active Directory')	=> "ad",
								_('Printers')		=> "$_SERVER[PHP_SELF]?node=$node&sub=printers&groupid=$groupid",
								_('Delete')		=> "del",
						), 'groupid', 'Group Menu');
			
			// Group Management: Notes
			$FOGSubMenu->addNotes('group', array(	_("Group")		=> getGroupNameByID( $conn, $groupid ),
								_("Members")		=> count(getImageMembersBygroupid( $conn, $groupid )),
						), 'groupid');
		}
	}
	
	// Host Management
	if ($node == "host")
	{
		$FOGSubMenu->addItems('host', array(	_('New Search')		=> 'newsearch',
							_('List All Hosts')	=> 'list',
							_('Add New Host')	=> 'add',
							_('Upload Hosts')	=> 'upload',
					));
		
		if ($id)
		{
			// Host Management: Edit
			$FOGSubMenu->addItems('host', array(	_('General')		=> "gen",
								_('Basic Tasks')	=> "tasks",
								_('Active Directory')	=> "ad",
								_('Printers')		=> "$_SERVER[PHP_SELF]?node=$node&sub=printers&id=$id",
								_('Snap-ins')		=> "snapins",
								_('Service Settings')	=> "service",
								_('Hardware')		=> "$_SERVER[PHP_SELF]?node=$node&sub=inv&id=$id",
								_('Virus History')	=> "virus",
								_('Login History')	=> "$_SERVER[PHP_SELF]?node=$node&sub=loginhist&id=$id",
								_('Delete')		=> "delete",
						), 'id', $hostname);

			// Host Management: Notes
			$FOGSubMenu->addNotes('host', array(	_('Host')	=> trimString( stripslashes($hostname), 20 ),
								_('MAC')	=> stripslashes(($host ? $host->getMAC() : '')),
								// TODO: make work - need to update HostManager to have $this->image on construction if $id exists
								//_('Image')	=> stripslashes($host->getImage()),
								_('O/S')	=> stripslashes(($host ? $host->getOSName() : '')),
						), 'id');

			// Primary Group
			$group = $groupMan->getGroupsWithMember($id);
			if ($group[0]) $FOGSubMenu->addNotes('host', array(_("Primary Group")	=> $group[0]->getName()), 'id');
		}
	}
	
	// Image Management
	if ($node == "images")
	{
		$FOGSubMenu->addItems('images', array(	_('New Search')		=> 'search',
							_('List All Images')	=> 'list',
							_('New Image')		=> 'add',
					));

		if ($imageid)
		{
			// Image Management: Edit
			$FOGSubMenu->addItems('images', array(	_('General')		=> "",
								_('Delete')		=> "delete",
						), 'imageid', 'Image Menu');
						
			// Image Management: Notes
			$FOGSubMenu->addNotes('images',  create_function('', '	$allImages = mysql_query("select * from images where imageID = \'' . $imageid . '\'");
										while ($image = mysql_fetch_array($allImages))
										{
											$x[("Image Name")] = $image["imageName"];
										}
										return $x;')
						, 'imageid');
		}
	}
	
	
	// Printer Management
	if ($node == "print")
	{
		$FOGSubMenu->addItems('print', array(	_('New Search')		=> 'search',
							_('List All Printers')	=> 'list',
							_('Add New Printer')	=> 'add',
					));
		
		if ($id)
		{
			// Printer Management
			$FOGSubMenu->addItems('print', array(	_('General')		=> "$_SERVER[PHP_SELF]?node=$node&sub=$sub&id=$id",
								_('Delete')		=> "$_SERVER[PHP_SELF]?node=$node&sub=delete&id=$id",
						), 'id', 'Printer Menu');

			// Printer Note
			$res = mysql_query( "select * from printers where pID = '$id'", $conn ) or die( mysql_error() );
			if ( $ar = mysql_fetch_array( $res ) )
			{
				$FOGSubMenu->addNotes('print', array('Model' => stripslashes($ar["pModel"]), 'Alias' => stripslashes($ar["pAlias"])));
			}
		}

	}
	
	// Reports Management
	if ($node == "report")
	{
		$FOGSubMenu->addItems('report', array(	_('Home')	=> 'home'));
		
		// Dynamically read php files and push into side menu
		$dh = opendir( getSetting($conn, "FOG_REPORT_DIR" ) );
		$included_in_fog = array("Equipment Loan.php" => _("Equipment Loan"), "Host List.php" => _("Host List"), "Imaging Log.php" => _("Imaging Log"), "Inventory.php" => _("Inventory"), "Snapin Log.php" => _("Snapin Log"), "User Login Hist.php" => _("User Login Hist"), "Virus History.php" => _("Virus History"));
		if ( $dh != null )
		{
			while ( ! (($f = readdir( $dh )) === FALSE) )
			{
				if ( is_file( getSetting($conn, "FOG_REPORT_DIR" ) . $f ) )
				{	
					if ( endswith( $f, ".php" ) )
					{
						$FOGSubMenu->addItems('report', array(	($included_in_fog[$f] ? $included_in_fog[$f] : substr( $f, 0, strlen( $f ) -4 )) => "$_SERVER[PHP_SELF]?node=$node&sub=file&f=" . base64_encode($f)));
					}
				}
			}
		}
		
		$FOGSubMenu->addItems('report', array(	_('Upload a Report')	=> 'upload'));
	}
	
	// Service Management
	if ($node == "service")
	{
		$FOGSubMenu->addItems('service', array(	_('Auto Log Out')	=> 'alo',
							_('Client Updater')	=> 'clientupdater',
							_('Directory Cleaner')	=> 'dircleaner',
							_('Display Manager')	=> 'displaymanager',
							_('Green FOG')		=> 'greenfog',
							_('Hostname Changer')	=> 'hostnamechanger',
							_('Host Registration')	=> 'hostregister',
							_('Printer Manager')	=> 'printermanager',
							_('Snapin Client')	=> 'snapin',
							_('Task Reboot')	=> 'taskreboot',
							_('User Cleanup')	=> 'usercleanup',
							_('User Tracker')	=> 'usertracker',
					));
	}
	
	// Snapin Management
	if ($node == "snap")
	{
		$FOGSubMenu->addItems('snap', array(	_('New Search')		=> 'search',
							_('List All Snap-ins')	=> 'list',
							_('New Snapin')		=> 'add',
					));
		
		if ($snapinid)
		{
			// Snapin Management: Per Snapin
			$FOGSubMenu->addItems('snap', array(	_('General')		=> "$_SERVER[PHP_SELF]?node=$node&sub=edit&snapinid=$snapinid&tab=gen",
								_('Delete')		=> "$_SERVER[PHP_SELF]?node=$node&sub=edit&snapinid=$snapinid&tab=delete",
						), 'snapinid', 'Snapin Menu');

			// Snapin Management: Notes
			$res = mysql_query( "select * from snapins where sID = '$snapinid'", $conn ) or die( mysql_error() );
			if ( $ar = mysql_fetch_array( $res ) )
			{
				$FOGSubMenu->addNotes('snap', array('Name' => stripslashes($ar["sName"])));
			}
		}
	}
	
	// Storage Management
	if ($node == "storage")
	{
		$FOGSubMenu->addItems('storage', array(	_('All Storage Groups')		=> 'groups',
							_('Add Storage Group')		=> 'addgroup',
							_('All Storage Nodes')		=> 'nodes',
							_('Add Storage Nodes')		=> 'addnode',
					));
		
		if ($storagegroupid)
		{
			$FOGSubMenu->addItems('storage', array(	_('General')			=> 'gen',
								_('Delete')			=> 'delete'
						), 'storagegroupid', _('Storage Group Menu'));
			
			$res = mysql_query( "select * from nfsGroups where ngID = '$storagegroupid'", $conn ) or die( mysql_error() );
			if ( $ar = mysql_fetch_array( $res ) )
			{
				$FOGSubMenu->addNotes('storage', array(_("Group Name") => stripslashes($ar["ngName"])));
			}
		}
		
		if ($storagenodeid)
		{
			$FOGSubMenu->addItems('storage', array(	_('General')			=> "$_SERVER[PHP_SELF]?node=$node&sub=editnode&storagenodeid=$storagenodeid&tab=gen",
								_('Delete')			=> "$_SERVER[PHP_SELF]?node=$node&sub=editnode&storagenodeid=$storagenodeid&tab=delete"
						), 'storagenodeid', _("Storage Node Menu"));

			$res = mysql_query( "select * from nfsGroupMembers where ngmID = '$storagenodeid'", $conn ) or die( mysql_error() );
			if ( $ar = mysql_fetch_array( $res ) )
			{
				$FOGSubMenu->addNotes('storage', array(_("Node Name") => stripslashes($ar["ngmMemberName"])));
			}
		}
	}
	
	// Task Management
	if ($node == "tasks")
	{
		$FOGSubMenu->addItems('tasks', array(	_('New Search')			=> 'search',
							_('List All Groups')		=> 'listgroups',
							_('List All Hosts')		=> 'listhosts',
							_('Active Tasks')		=> 'active',
							_('Scheduled Tasks')		=> 'sched',
							_('Active Multicast Tasks')	=> 'activemc',
							_('Active Snap-ins')		=> 'activesnapins',
					));
	}
	
	// User Management
	if ($node == "users")
	{
		$FOGSubMenu->addItems('users', array(	_('List All Users')		=> 'list',
							_('New User')			=> 'add',
					));
		
		if ($userid)
		{
			// User Management: Per User
			$FOGSubMenu->addItems('users', array(	_('General')			=> "$_SERVER[PHP_SELF]?node=$node&sub=edit&userid=$userid",
								_('Delete')			=> "$_SERVER[PHP_SELF]?node=$node&sub=edit&tab=delete&userid=$userid",
						), 'userid', 'User Menu');
			
			// User Management: Notes
			$userMan = $core->getUserManager();
			$user = $userMan->getUserById( $userid );
			
			if ( $user != null )
			{
				$FOGSubMenu->addNotes('users', array(_("Username") => $user->getUserName()));
			}
		}
	}
	
	// Plugins
	if ($node == "plugin")
	{
		$FOGSubMenu->addItems('plugin', array(	_('Home')		=> 'home',
							_('Installed Plugin')	=> 'installed',
							_('Activate Plugin')	=> 'activate',
					));
	}
	
	// HWInfo - linked to from Dashboard
	if ($node == "hwinfo")
	{
		$FOGSubMenu->addItems('hwinfo', array(	_('Home')		=> 'home'));
	}
	
	if ($node == "help")
	{
		$FOGSubMenu->addItems('help', array(	_('Home')		=> 'home'));
	}
				
	
	// DEBUG
	//print "<pre>";
	//print_r(htmlspecialchars($FOGSubMenu->get($node)));
	
	print $FOGSubMenu->get($node);
}


class FOGSubMenu
{
	// Variables
	var $DEBUG = 0;
	var $version = 0.1;
	
	// These are used when another $sub is called
	// Include code needs to be rewritten to fix this correctly
	// i.e. Host Management -> Printers
	var $defaultSubs = array('host' => 'edit', 'group' => 'edit');
	
	// Constructor
	function __construct()
	{
		$this->items = array();
		$this->info = array();
	}
	
	// Add menu items
	function addItems($node, $items, $ifVariable = '', $ifVariableTitle = '')
	{
		// TODO: Clean up - use this below to start
		// No check variable? then Main Menu
		//$ifVariable = ($ifVariable == '' ? 'Main Menu' : $ifVariable);
		
		// No ifVariable to check, this must be a main menu item
		if (!$ifVariable)
		{
			if (is_array($this->items[$node]['Main Menu']))
			{
				$this->items[$node]['Main Menu'] = array_merge($this->items[$node]['Main Menu'], $items);
			}
			else
			{
				$this->items[$node]['Main Menu'] = $items;
			}
		}
		// ifVariable passed to be checked, if it is set then add to menu
		elseif (isset($GLOBALS[$ifVariable]))
		{
			foreach ($items AS $title => $link)
			{
				if (!$this->isExternalLink($link)) $items[$title] = "$link&$ifVariable=" . $GLOBALS[$ifVariable];
			}
			
			if (is_array($this->items[$node][$ifVariableTitle]))
			{
				$this->items[$node][$ifVariableTitle] = array_merge($this->items[$node][$ifVariableTitle], $items);
			}
			else
			{
				$this->items[$node][$ifVariableTitle] = $items;
			}
		}
	}
	
	// Add notes below menu items
	function addNotes($node, $data, $ifVariable = '') {
		if (is_callable($data))
		{
			$data = $data();
		}

		if (is_array($data))
		{
			foreach ($data AS $title => $info)
			{
				$x[] = "<h3>" . $this->fixTitle($title) . "</h3>\n\t<p>$info</p>";
			}
		}
		
		if ($ifVariable == '' || $GLOBALS[$ifVariable]) $this->notes[$node][] = implode("\n", (array)$x);
	}
	
	// Get menu items & notes for $node
	function get($node)
	{
		global $sub;
	
		// Menu Items
		if ($this->items[$node])
		{
			foreach ($this->items[$node] AS $title => $data)
			{
				//$this->debug($data);
				
				$output .= "<h2>" . $this->fixTitle($title) . "</h2>\n\t\t<ul>\n";
				foreach ($data AS $label => $link)
				{
					$output .= "\t\t\t" . '<li><a href="' . (!$this->isExternalLink($link) ? $_SERVER['PHP_SELF'] . "?node=$node&sub=" . ($sub && $title != "Main Menu" ? ($this->defaultSubs[$node] ? $this->defaultSubs[$node] : $sub) . "&tab=" : '') . $link : $link) . '">' . $label . '</a></li>' . "\n";
				}
				$output .= "\t\t</ul>\n";
			}
		}
		
		// Notes
		if ($this->notes[$node])
		{
			$output .= '<div id="sidenotes">' . "\n\t" . implode("\t\n", $this->notes[$node]) . "\n" . '</div>';
		}
		
		return $output;
	}
	
	// Pretty up section titles
	function fixTitle($title)
	{
		if (preg_match('#[[:space:]]#', $title))
		{
			$e = explode(' ', $title);
			$e[0] = "<b>$e[0]</b>";
			$title = implode(' ', $e);
		}
		else if (preg_match('#-#', $title))
		{
			$e = explode('-', $title);
			$e[0] = "<b>$e[0]</b>";
			$title = implode('-', $e);
		}
		
		return $title;
	}
	
	// Test if the link is a node link or an external link
	function isExternalLink($link) {
		if (substr($link, 0, 4) == 'http' || $link{0} == '/' ||  $link{0} == '?') return true;
		return false;
	}
	
	// Debug
	function debug($txt) {
		if ($this->DEBUG) print '[' . date("m/d/y H:i:s") . "] " . htmlspecialchars(is_array($txt) ? print_r($txt, 1) : $txt) . "\n";
	}
}
?>
