<?php
/*
 *  FOG is a computer imaging solution.
 *  Copyright (C) 2007  Chuck Syperski & Jian Zhang
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 */

// Require FOG Base
require_once('../commons/config.php');
require_once(BASEPATH . '/commons/init.php');

// Config load check
if (IS_INCLUDED !== true) die(_('Unable to load system configuration information'));

// Deprecated includes - TODO: remove when we know they arent required
//require_once(BASEPATH . '/management/lib/ImageMember.class.php');
//require_once(BASEPATH . '/management/lib/FOGCore.class.php');
//require_once(BASEPATH . '/management/lib/ScheduledTask.class.php');
//require_once(BASEPATH . '/management/lib/Timer.class.php');
//require_once(BASEPATH . '/management/lib/Imageable.interface.php');
//require_once(BASEPATH . '/management/lib/Host.class.php');
//require_once(BASEPATH . '/management/lib/Group.class.php');
//require_once(BASEPATH . '/management/lib/MACAddress.class.php');
//require_once(BASEPATH . '/management/lib/Image.class.php');
//require_once(BASEPATH . '/management/lib/StorageGroup.class.php');
//require_once(BASEPATH . '/management/lib/StorageNode.class.php');

// User session data
$currentUser = null;
if ($_SESSION['fog_user'] != null) $currentUser = unserialize($_SESSION['fog_user']);

// Process Login
require_once('./includes/processlogin.include.php');

// Login form + logout
if ($node == 'logout' || $currentUser == null || !$currentUser->isLoggedIn())
{
	if ($node == 'logout')
	{
		$_SESSION['fog_user'] = null;
		$currentUser  = null;
		session_destroy();
	}
	
	$_SESSION['allow_ajax_host'] = false;
	
	require_once('./includes/loginform.include.php');
}

// Ping Active
$_SESSION['FOGPingActive'] = ($core->getGlobalSetting('FOG_HOST_LOOKUP') == '1');

// Allow AJAX Tasks
$_SESSION['AllowAJAXTasks'] = 1;

if ($currentUser != null) $_SESSION['fog_user'] = serialize($currentUser);

// Determine content
// TODO: Move to array, iterate array, etc
// TODO: Need to make $node match ./includes/$includePage.include.php for all files... i.e. report -> reports, snap -> snapins
if ($node == 'images')
	$includePage = 'images';
else if ($node == 'host')
	$includePage = 'hosts';
else if ($node == 'group')
	$includePage = 'groups';
else if ($node == 'tasks')
	$includePage = 'tasks';
else if ($node == 'users')
	$includePage = 'users';
else if ($node == 'about')
	$includePage = 'about';
else if ($node == 'help')
	$includePage = 'help';
else if ($node == 'snap')
	$includePage = 'snapins';
else if ($node == 'report')
	$includePage = 'reports';
else if ($node == 'print')
	$includePage = 'printer';
else if ($node == 'service')
	$includePage = 'service';
else if ($node == 'plugin')
	$includePage = 'plugin';
else if ($node == 'storage')
	$includePage = 'storage';
else if ($node == 'hwinfo')
	$includePage = 'hwinfo';
else
	$includePage = 'dashboard';

$isHomepage = ($includePage == 'dashboard' ? true : false);

// Determine the current page's title
$pageTitles = array(
		'users'		=> _('User Management'),
		'host'		=> _('Host Management'),
		'group'		=> _('Group Management'),
		'images'	=> _('Image Management'),
		'storage'	=> _('Storage Management'),
		'snap'		=> _('Snap-in Management'),
		'print'		=> _('Printer Management'),
		'service'	=> _('FOG Configuration'),
		'tasks'		=> _('Task Management'),
		'report'	=> _('Reports'),
		'about'		=> _('Other Information'),
		'hwinfo'	=> _('Hardware Information'),
		'logout'	=> _('Logout'),
		);
// TODO: Include ID / Name of each item under each section
$pageTitle = ($pageTitles[$node] ? $pageTitles[$node] : 'Dashboard');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php print $pageTitle; ?> &gt; FOG &gt; Open Source Computer Cloning Solution</title>
	
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="x-ua-compatible" content="IE=8">
	
	<!-- Stylesheets -->
	<link rel="stylesheet" type="text/css" href="css/global.css" />
	<link rel="stylesheet" type="text/css" media="all" href="css/calendar/calendar-win2k-1.css"  />
	<link href="default.css" rel="stylesheet" type="text/css" />
</head>
<body>

<!-- FOG Message Boxes -->
<div id="loader-wrapper"><div id="loader"><div id="progress"></div></div></div>

<!-- Main -->
<div id="wrapper">
	<!-- Header -->
	<div id="header">
		<div id="logo">
			<h1><a href="#"><img src="images/fog.png" /><sup><?php echo FOG_VERSION ?></sup></a></h1>
			<h2>Open Source Computer Cloning Solution</h2>
		</div>
		<div id="menu">
			<?php
			require_once('./includes/mainmenu.include.php');
			?>
		</div>
	</div>
	<!-- Content -->
	<div id="content"<?php print ($isHomepage ? ' class="dashboard"' : ''); ?>>
		<h1><?php print $pageTitle; ?></h1>
		<div id="content-inner">
			<?php
			require_once("./includes/{$includePage}.include.php");
			?>
		</div>
	</div>
<?php
if (!$isHomepage) {
?>
	<!-- Menu -->
	<div id="sidebar">
		<?php
		require_once('./includes/submenu.include.php');
		?>
	</div>
<?php
}
?>
</div>

<!-- Footer -->
<div id="footer">FOG: Chuck Syperski &amp; Jian Zhang, FOG WEB UI: Peter Gilchrist</div>

<!-- Variables -->
<div class="fog-variable" id="FOGPingActive"><?php print ($core->getGlobalSetting('FOG_HOST_LOOKUP') == '1' ? '1' : '0'); ?></div>

<!-- JavaScript -->
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/calendar/jquery.dynDateTime.js"></script>
<script type="text/javascript" src="js/calendar/calendar-en.js"></script>
<script type="text/javascript" src="js/jquery.tipsy.js"></script>
<script type="text/javascript" src="js/jquery.progressbar.js"></script>
<script type="text/javascript" src="js/fog.js"></script>
<script type="text/javascript" src="js/fog.main.js"></script>
<?php
// TODO: Move to array
if ($node == 'tasks' && $sub == 'active')
{
	?>
	<script type="text/javascript" src="js/fog.tasks.active.js"></script>
	<?php
}
else if ($node == 'tasks' && $sub == 'confirm')
{
	?>
	<script type="text/javascript" src="js/fog.tasks.confirm.js"></script>
	<?php
}
else if ($node == 'host' && $sub == 'edit')
{
	?>
	<script type="text/javascript" src="js/fog.hosts.js"></script>
	<script type="text/javascript" src="js/fog.adpop.js"></script>
	<?php
}
else if ($node == 'group' && $sub == 'edit')
{
	?>
	<script type="text/javascript" src="js/fog.adpop.js"></script>
	<?php
}
else if ($node == 'host' && $sub == 'add')
{
	?>
	<script type="text/javascript" src="js/fog.hosts.add.js"></script>
	<script type="text/javascript" src="js/fog.adpop.js"></script>
	<?php
}
else if ($node == 'about' && $sub == 'maclist')
{
	?>
	<script type="text/javascript" src="js/fog.about.maclist.js"></script>
	<?php
}
else if ($node == 'about' && $sub == 'kernel')
{
	?>
	<script type="text/javascript" src="js/fog.about.kernel.js"></script>
	<?php
}
else if ($isHomepage)
{
	?>
	<script type="text/javascript" src="js/jquery.flot.js"></script>
	<script type="text/javascript" src="js/jquery.flot.pie.js"></script>
	<script type="text/javascript" src="js/fog.dashboard.js"></script>
	<?php
	
	// Include 'excanvas' for HTML5 <canvas> support in IE 6/7/8/9...
	// I hate IE soooo much, only Microsoft would allow people to still use outdated browsers
	if (preg_match('#MSIE [6|7|8|9]#', $_SERVER['HTTP_USER_AGENT']))
	{
		?>
		<script type="text/javascript" src="js/excanvas.js"></script>
		<?php
	}
}
?>

</body>
</html>
