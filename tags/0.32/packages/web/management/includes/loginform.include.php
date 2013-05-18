<?php

if ( IS_INCLUDED !== true ) die( _("Unable to load system configuration information.") );

function getLanguages()
{
	$path = "languages";
	$dir_handle = @opendir($path) or die(_("Unable to open")." $path");
	//echo "<option value=\"\"></option>";
	while ($file = readdir($dir_handle))
	{
		if (!preg_match('#^\.#', $file) && is_dir("$path/$file"))
		{
			echo "<option value=\"$file\"";
			if(strtolower($_SESSION['locale']) == strtolower($file))
				echo " selected=\"selected\" ";
			echo ">$file</option><br/>";
		}
	}
	closedir($dir_handle);
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Login &gt; FOG &gt; Open Source Computer Cloning Solution</title>
	
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="x-ua-compatible" content="IE=8">
	
	<!-- Stylesheets -->
	<link rel="stylesheet" type="text/css" href="css/global.css" />
	<link rel="stylesheet" type="text/css" media="all" href="css/calendar/calendar-win2k-1.css"  />
	<link href="default.css" rel="stylesheet" type="text/css" />
</head>
<body>

<!-- FOG Message boxes -->
<div id="loader"></div>

<!-- Main -->
<div id="wrapper">
	<!-- Header -->
	<div id="header" class="login">
		<div id="logo">
			<h1><a href="#"><img src="images/fog.png" /><sup><?php echo FOG_VERSION ?></sup></a></h1>
			<h2>Open Source Computer Cloning Solution</h2>
		</div>
	</div>
	<!-- Content -->
	<div id="content" class="dashboard">
		<h1><?php print _("FOG Management Login"); ?></h1>
		<div id="content-inner">
			<form method="post" action="?node=login" id="login-form">
				<label for="username"><?php print _("Username"); ?></label>
				<input type="text" class="input" name="uname" id="username" />
				<label for="password"><?php print _("Password"); ?></label>
				<input type="password" class="input" name="upass" id="password" />
				<label for="language"><?php print _("Language"); ?></label>
				<select name="ulang" id="language" /><?php getLanguages(); ?></select>
				<label for="login-form-submit"></label>
				<input type="submit" value="<?php print _("Login"); ?>" id="login-form-submit" />
			</form>
				
			<div id="login-form-info">
				<div><?php print _("Estimated FOG sites"); ?>: <b><span class="icon icon-loading-grey"></span></b></div>
				<div><?php print _("Latest Version"); ?>: <b><span class="icon icon-loading-grey"></span></b></div>	
			</div>
		</div>
	</div>
</div>

<!-- Footer -->
<div id="footer">FOG: Chuck Syperski & Jian Zhan, FOG WEB UI: Peter Gilchrist</div>

<!-- JavaScript -->
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/fog.login.js"></script>

</body>
</html>
<?php

// Exit after login form has been shown
exit;