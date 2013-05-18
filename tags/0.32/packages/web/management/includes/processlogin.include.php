<?php

if ( IS_INCLUDED !== true ) die( _("Unable to load system configuration information.") );

$userMan = $core->getUserManager();

if( isset($_POST["ulang"]) && strlen(trim($_POST["ulang"])) > 0 )
{
	$_SESSION['locale'] = $_POST["ulang"];
	
	putenv("LC_ALL=".$_SESSION['locale']);
	setlocale(LC_ALL, $_SESSION['locale']);
	bindtextdomain("messages", "languages");
	textdomain("messages");
}

if ( $userMan != null && $_POST["uname"] != null && $_POST["upass"] != null && $conn != null  )
{
	$username = trim($_POST["uname"]);
	$password = trim($_POST["upass"]);
	
	if ( $userMan->isValidPassword( $password, $password, $core->getGlobalSetting("FOG_USER_MINPASSLENGTH"), $core->getGlobalSetting("FOG_USER_VALIDPASSCHARS")) && $userMan->isValidUsername( $username ) )
	{
		 $tmpUser = $userMan->attemptLogin($username, $password);
		 if ( $tmpUser != null && $tmpUser->getType() == User::TYPE_ADMIN )
		 {		 
			$currentUser = $tmpUser;
			$currentUser->setAuthTime(time());
			$currentUser->setAuthIp($_SERVER["REMOTE_ADDR"]);
			
			// Redirect after successful login - this will stop the "resend data" prompt if you refresh after logging in
			Header("Location: " . $_SERVER['PHP_SELF']);
		 }
		 else
		 {
		 	msgBox(_('Invalid Login'));
		 }
	}
	else
	{
		msgBox(_('Either the username or password contains invalid characters'));
	}
}