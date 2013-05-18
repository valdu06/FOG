<?php
/*
 *  FOG - Free, Open-Source Ghost is a computer imaging solution.
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

if ( IS_INCLUDED !== true ) die( _("Unable to load system configuration information.") );

if ( $_POST["add"] != null )
{
	try
	{
		$img = $core->getImageManager()->getImageById( $_POST["image"] );
		$host = new Host(-1, $_POST["host"], $_POST["description"], $_POST["ip"], null, new MACAddress($_POST["mac"]), $_POST["os"]);
		$host->setImage( $img );
		$host->setKernel($_POST["kern"]);
		$host->setKernelArgs($_POST["args"]);
		$host->setDiskDevice( $_POST["dev"] );
		$host->setADUsage($_POST["domain"] == "on");
		$host->setupAD( $_POST["domainname"], $_POST["ou"], $_POST["domainuser"], $_POST["domainpassword"] );
		if ( $core->getHostManager()->addHost( $host, $currentUser ) )
		{
			msgBox( _("Host Added, you may now add another.") );
			lg( _("New Host Added via management form").": ". $hostname );		
		}
	}
	catch( Exception $e )
	{
		msgBox( _("Failed to add host! " ) . $e->getMessage() );
		lg( _("Failed add add new host via management form ").": " . $hostname  . " " . $e->getMessage());
	}
}
?>
<h2><?php print _("Add new host definition"); ?></h2>
<?php
echo ( "<form method=\"POST\" action=\"?node=$_GET[node]&sub=$_GET[sub]\">" );
echo ( "<center><table cellpadding=0 cellspacing=0 border=0 width=90%>" );
	echo ( "<tr><td>"._("Host Name").":*</td><td><input class=\"smaller\" type=\"text\" name=\"host\" value=\"\" /></td></tr>" );
	echo ( "<tr><td>"._("Host IP").":</td><td><input class=\"smaller\" type=\"text\" name=\"ip\" value=\"\" /></td></tr>" );
	echo ( "<tr><td>"._("Primary MAC").":*</td><td><input class=\"smaller\" type=\"text\" id='mac' name=\"mac\" value=\"\" /> &nbsp; <span id='priMaker'></span> </td></tr>" );
	echo ( "<tr><td>"._("Host Description").":</td><td><textarea name=\"description\" rows=\"5\" cols=\"40\"></textarea></td></tr>" );
	echo ( "<tr><td>"._("Host Image").":</td><td>" );
		echo getImageDropDown( $conn );
	echo ( "</td></tr>" );
	echo ( "<tr><td>"._("Host OS").":</td><td>" );		
		echo ( getOSDropDown( $conn ) );
	echo ( "</td></tr>" );
	echo ( "<tr><td>"._("Host Kernel").":</td><td><input class=\"smaller\" type=\"text\" name=\"kern\" /></td></tr>" );		
	echo ( "<tr><td>"._("Host Kernel Arguments").":</td><td><input class=\"smaller\" type=\"text\" name=\"args\" /></td></tr>" );	
	echo ( "<tr><td>"._("Host Primary Disk").":</td><td><input class=\"smaller\" type=\"text\" name=\"dev\" /></td></tr>" );		
	echo ( "</table>" );
	echo ( "<table cellpadding=0 cellspacing=0 border=0 width=90%>" );

	echo ( "<p class=\"titleBottomLeft\">"._("Active Directory")."</p>" );
				
	echo ( "<table cellpadding=0 cellspacing=0 border=0 width=90%>" );
		echo ( "<tr><td>"._("Join Domain after image task").":</td><td><input id='adEnabled' type=\"checkbox\" name=\"domain\" /></td></tr>" );
		echo ( "<tr><td>"._("Domain name").":</td><td><input id=\"adDomain\" class=\"smaller\" type=\"text\" name=\"domainname\" /></td></tr>" );				
		echo ( "<tr><td>"._("Organizational Unit").":</td><td><input id=\"adOU\" class=\"smaller\" type=\"text\" name=\"ou\" /> <span class=\"lightColor\">"._("(Blank for default)")."</span></td></tr>" );				
		echo ( "<tr><td>"._("Domain Username").":</td><td><input id=\"adUsername\" class=\"smaller\" type=\"text\" name=\"domainuser\" /></td></tr>" );						
		echo ( "<tr><td>"._("Domain Password").":</td><td><input id=\"adPassword\" class=\"smaller\" type=\"text\" name=\"domainpassword\" /> <span class=\"lightColor\">"._("(Must be encrypted)")."</span></td></tr>" );											
		echo ( "<tr><td colspan=2><center><br /><input type=\"hidden\" name=\"add\" value=\"1\" /><input class=\"smaller\" type=\"submit\" value=\""._("Add")."\" /></center></td></tr>" );				
	echo ( "</table>" );	
echo ( "</table></center>" );
echo ( "</form>" );
