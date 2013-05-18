<?php
/*
 *  FOG is a computer imaging solution.
 *  Copyright (C) 2007  Chuck Syperski & Jian Zhang
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
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

if ( $_GET["groupid"] != null && is_numeric( $_GET["groupid"] ) )
{

	$groupid = mysql_real_escape_string( $_GET["groupid"] );

	if ( $_POST["update"] == "1" )
	{
		if ( ! groupExists( $conn, $_POST["name"], $groupid ) )
		{
			$name = mysql_real_escape_string( $_POST["name"] );
			$description = mysql_real_escape_string( $_POST["description"] );
			$kargs = mysql_real_escape_string( $_POST["args"] );
			$kernel = trim(mysql_real_escape_string( $_POST["kern"] ));	
			$device = trim(mysql_real_escape_string( $_POST["dev"] ));
			
			$sql = "update groups set groupName = '" . $name . "', groupDesc = '$description' where groupID = '" . mysql_real_escape_string( $_GET[groupid] ) . "'";
			mysql_query( $sql, $conn ) or die( mysql_error() );
			msgBox( _("Group Updated!") );
			lg( _("updated group")." $name" );
			
			$members = getImageMembersByGroupID( $conn, $groupid );

			if ( $members != null )
			{
				for( $i =0; $i < count( $members ); $i++ )
				{
					if ( $members[$i] != null )
					{	
						if ( $kargs != null )
						{								
							$sql = "update hosts set hostKernelArgs = '" . $kargs . "' where hostID = '" . $members[$i]->getID() . "'";	
							if ( ! mysql_query( $sql, $conn ) )
							{
								die( mysql_error() );
		
							}
						}						
						
						if ( $kernel != null )
						{
							$sql = "update hosts set hostKernel = '" . $kernel . "' where hostID = '" . $members[$i]->getID() . "'";	
							if ( ! mysql_query( $sql, $conn ) )
							{
								die( mysql_error() );
		
							}							
						}
						
						if ( $device != null )
						{
							$sql = "update hosts set hostDevice = '" . $device . "' where hostID = '" . $members[$i]->getID() . "'";	
							if ( ! mysql_query( $sql, $conn ) )
							{
								die( mysql_error() );
		
							}							
						}						
					}
				}
			}
			
		}
		else
		{
			msgBox( _("Unable to update because the group name you picked already exists") );
		}
	}

	if ( $_POST["updatead"] == "1" )
	{
		$updated = 0;
		$members = getImageMembersByGroupID( $conn, $groupid );
		if ( $members != null )
		{
			$useAD = "0";
			if ( $_POST["domain"] == "on" )
				$useAD = "1";

			$adDomain = mysql_real_escape_string( $_POST["domainname"] );
			$adOU = mysql_real_escape_string( $_POST["ou"] );
			$adUser = mysql_real_escape_string( $_POST["domainuser"] );
			$adPass = mysql_real_escape_string( $_POST["domainpassword"] );

			for( $i =0; $i < count( $members ); $i++ )
			{
				if ( $members[$i] != null )
				{
					$sql = "update hosts set hostUseAD = '$useAD', hostADDomain = '$adDomain', hostADOU = '$adOU', hostADUser = '$adUser', hostADPass = '$adPass' where hostID = '" . $members[$i]->getID() . "'";
					if ( mysql_query( $sql, $conn ) )
					{
						$updated++;
					}
					else
					{
						die( mysql_error() );

					}
				}
			}
			msgBox( $updated . _(" hosts have been updated.") );
		}
	}

	if ( $_POST["gsnapinadd"] == "1" )
	{
		$update = 0;
		$existing = 0;
		$failed = 0;
		$members = getImageMembersByGroupID( $conn, $groupid );
		if ( $members != null )
		{
			$snapinid = mysql_real_escape_string($_POST["snap"]);
			if ( is_numeric( $snapinid ) )
			{
				for( $i =0; $i < count( $members ); $i++ )
				{
					if ( $members[$i] != null )
					{
						if ( ! isHostAssociatedWithSnapin( $conn, $members[$i]->getID(), $snapinid ) )
						{
							$returnVal = null;
							if ( addSnapinToHost( $conn, $members[$i]->getID(), $snapinid, $returnVal ) )
							{
								$update++;
							}
							else
								$failed++;
						}
						else
							$existing++;
					}
				}
				msgBox( $update . _(" hosts have been updated.")."<br />" . $failed . _(" hosts have failed.")."<br />" . $existing . _(" were already linked.") );
			}
		}
	}


	if ( $_POST["gsnapindel"] == "1" )
	{
		$removed = 0;
		$members = getImageMembersByGroupID( $conn, $groupid );
		if ( $members != null )
		{
			$snapinid = mysql_real_escape_string($_POST["snap"]);
			if ( is_numeric( $snapinid ) )
			{
				for( $i =0; $i < count( $members ); $i++ )
				{
					if ( $members[$i] != null )
					{
						if ( isHostAssociatedWithSnapin( $conn, $members[$i]->getID(), $snapinid ) )
						{
							$returnVal = null;
							if ( deleteSnapinFromHost( $conn, $members[$i]->getID(), $snapinid, $returnVal )  )
							{
								$removed++;
							}
						}
					}
				}
				msgBox( $removed . _(" hosts have been updated.") );
			}
		}
	}


	if ( $_POST["image"] != null && is_numeric( $_POST["image"] ) )
	{
		$updated = 0;
		$members = getImageMembersByGroupID( $conn, $groupid );
		if ( $members != null )
		{
			for( $i =0; $i < count( $members ); $i++ )
			{
				if ( $members[$i] != null )
				{
					if ( $members[$i]->getID() != null )
					{
						$sql = "update hosts set hostImage = '" . mysql_real_escape_string( $_POST["image"] ) . "' where hostID = '" . mysql_real_escape_string( $members[$i]->getID() ) . "'";
						if ( mysql_query( $sql, $conn ) )
						{
							$updated++;
							lg( _("updated image for host ") . $members[$i]->getID() );
						}
					}
				}
			}
		}
		msgBox( _("$updated hosts updated") );
	}

	if ( $_POST["grpos"] !== null && is_numeric( $_POST["grpos"] ) )
	{
		$updated = 0;
		$members = getImageMembersByGroupID( $conn, $groupid );
		if ( $members != null )
		{
			for( $i =0; $i < count( $members ); $i++ )
			{
				if ( $members[$i] != null )
				{
					if ( $members[$i]->getID() != null )
					{
						$sql = "update hosts set hostOS = '" . mysql_real_escape_string( $_POST["grpos"] ) . "' where hostID = '" . mysql_real_escape_string( $members[$i]->getID() ) . "'";
						if ( mysql_query( $sql, $conn ) )
						{
							$updated++;
							lg( _("updated os for host ") . $members[$i]->getID() );
						}
					}
				}
			}
		}
		msgBox( _("$updated hosts updated") );
	}

	if ( $_GET["updatemodulestatus"] == "1" )
	{
		$blUpdateDirCleanup = true;
		$blUpdateUserCleanup = true;
		$blUpdateDisplayMan = true;
		$blUpdateALO = true;
		$blUpdateGF = true;
		$blUpdateSnapin = true;
		$blUpdateHNC = true;
		$blUpdateCU = true;
		$blUpdateHR = true;
		$blUpdatePM = true;
		$blUpdateTR = true;
		$blUpdateUT = true;

		$dircleanupstate = "0";
		$usercleanupstate = "0";
		$displaymanstate = "0";
		$alostate = "0";
		$gfstate = "0";
		$snapinstate = "0";
		$HNCstate="0";
		$CUstate = "0";
		$HRstate = "0";
		$PMstate = "0";
		$TRstate = "0";
		$UTstate = "0";

		if ( $_POST["clientupdater"] == "nc" ) $blUpdateCU = false;
		if ( $_POST["clientupdater"] == "on" ) $CUstate = "1";
		
		if ( $_POST["hostregister"] == "nc" ) $blUpdateHR = false;
		if ( $_POST["hostregister"] == "on" ) $HRstate = "1";
		
		if ( $_POST["printermanager"] == "nc" ) $blUpdatePM = false;
		if ( $_POST["printermanager"] == "on" ) $PMstate = "1";
		
		if ( $_POST["taskreboot"] == "nc" ) $blUpdateTR = false;
		if ( $_POST["taskreboot"] == "on" ) $TRstate = "1";
		
		if ( $_POST["usertracker"] == "nc" ) $blUpdateUT = false;
		if ( $_POST["usertracker"] == "on" ) $UTstate = "1";

		if ( $_POST["dircleanen"] == "nc" ) $blUpdateDirCleanup = false;
		if ( $_POST["dircleanen"] == "on" ) $dircleanupstate = "1";

		if ( $_POST["usercleanen"] == "nc" ) $blUpdateUserCleanup = false;
		if ( $_POST["usercleanen"] == "on" ) $usercleanupstate = "1";

		if ( $_POST["displaymanager"] == "nc" ) $blUpdateDisplayMan = false;
		if ( $_POST["displaymanager"] == "on" ) $displaymanstate = "1";

		if ( $_POST["alo"] == "nc" ) $blUpdateALO = false;
		if ( $_POST["alo"] == "on" ) $alostate = "1";

		if ( $_POST["gf"] == "nc" ) $blUpdateGF = false;
		if ( $_POST["gf"] == "on" ) $gfstate = "1";
		
		if ( $_POST["snapin"] == "nc" ) $blUpdateSnapin = false;
		if ( $_POST["snapin"] == "on" ) $snapinstate = "1";		
		
		if ( $_POST["hostnamechanger"] == "nc" ) $blUpdateHNC = false;
		if ( $_POST["hostnamechanger"] == "on" ) $HNCstate = "1";			

		// determine if a request to change the screen resolution has been requested
		$blUpdateX = false;
		$blUpdateY = false;
		$blUpdateR = false;

		$x = mysql_real_escape_string( $_POST["x"] );
		$y = mysql_real_escape_string( $_POST["y"] );
		$r = mysql_real_escape_string( $_POST["r"] );

		if ( is_numeric( $x ) && $x > 0 )
			$blUpdateX = true;

		if ( is_numeric( $y ) && $y > 0 )
			$blUpdateY = true;

		if ( is_numeric( $r ) && $r > 0 )
			$blUpdateR = true;

		$tme = mysql_real_escape_string( $_POST["tme"] );

		$updated = 0;
		$members = getImageMembersByGroupID( $conn, $groupid );
		if ( $members != null )
		{
			for( $i =0; $i < count( $members ); $i++ )
			{
				if ( $members[$i] != null )
				{
					if ( $members[$i]->getID() != null )
					{
						$hid = mysql_real_escape_string( $members[$i]->getID() );
						if ( $blUpdateDirCleanup )
							setHostModuleStatus( $conn, $dircleanupstate, $hid, 'dircleanup' );

						if ( $blUpdateUserCleanup )
							setHostModuleStatus( $conn, $usercleanupstate, $hid, 'usercleanup' );

						if ( $blUpdateDisplayMan )
							setHostModuleStatus( $conn, $displaymanstate, $hid, 'displaymanager' );

						if ( $blUpdateALO )
							setHostModuleStatus( $conn, $alostate, $hid, 'autologout' );

						if ( $blUpdateGF )
							setHostModuleStatus( $conn, $gfstate, $hid, 'greenfog' );

						if ( $blUpdateSnapin )
							setHostModuleStatus( $conn, $snapinstate, $hid, 'snapin' );

						if ( $blUpdateHNC )
							setHostModuleStatus( $conn, $HNCstate, $hid, 'hostnamechanger' );

						if ( $blUpdateCU )
							setHostModuleStatus( $conn, $CUstate, $hid, 'clientupdater' );
							
						if ( $blUpdateHR )
							setHostModuleStatus( $conn, $HRstate, $hid, 'hostregister' );
							
						if ( $blUpdatePM )
							setHostModuleStatus( $conn, $PMstate, $hid, 'printermanager' );
							
						if ( $blUpdateTR )
							setHostModuleStatus( $conn, $TRstate, $hid, 'taskreboot' );
							
						if ( $blUpdateUT )
							setHostModuleStatus( $conn, $UTstate, $hid, 'usertracker' );																												

						if ( $blUpdateX || $blUpdateY || $blUpdateR )
						{
							$sql = "SELECT
									COUNT(*) as cnt
								FROM
									hostScreenSettings
								WHERE
									hssHostID = '$hid'";
							$res = mysql_query( $sql, $conn ) or criticalError( mysql_error(), _("FOG :: Database error!") );

							$blFound = false;
							while( $ar = mysql_fetch_array( $res ) )
							{
								if ( $ar["cnt"] > 0 ) $blFound = true;
							}

							if ( $blFound )
							{
								$sql = "UPDATE
										hostScreenSettings
										set
											hssWidth = '$x',
											hssHeight = '$y',
											hssRefresh = '$r'
										WHERE
											hssHostID = '$hid'";
							}
							else
							{
								$sql = "INSERT INTO hostScreenSettings(hssHostID, hssWidth, hssHeight, hssRefresh) values('$hid', '$x', '$y', '$r')";
							}

							if ( ! mysql_query( $sql, $conn ) )
								criticalError( mysql_error(), _("FOG :: Database error!") );
						}

						if ( is_numeric( $tme ) && $tme >= 0 )
						{
							$blFound = false;
							$sql = "SELECT
									COUNT(*) as cnt
								FROM
									hostAutoLogOut
								WHERE
									haloHostID = '$hid'";
							$res = mysql_query( $sql, $conn ) or criticalError( mysql_error(), _("FOG :: Database error!") );
							while( $ar = mysql_fetch_array( $res ) )
							{
								if ( $ar["cnt"] > 0 )
									$blFound = true;
							}

							if ( $blFound )
							{
								$sql = "UPDATE hostAutoLogOut set haloTime = '$tme' WHERE haloHostID = '$hid'";
							}
							else
							{
								$sql = "INSERT INTO hostAutoLogOut(haloTime, haloHostID) values('$tme', '$hid')";
							}

							if ( ! mysql_query( $sql, $conn ) )
								criticalError( mysql_error(), _("FOG :: Database error!") );
						}

						$updated++;
					}
				}
			}
		}
		msgBox( "$updated "._("hosts updated") );
	}

	$sql = "select * from groups where groupID = '" . $groupid . "'";
	$res = mysql_query( $sql, $conn ) or die( mysql_error() );


	if ( $ar = mysql_fetch_array( $res ) )
	{
		echo ( "<div class=\"scroll\">" );
			if ( $_GET["tab"] == "gen" || $_GET["tab"] == "" )
			{
				?>
				<h2><?php print _("Modify Group") ." ". $ar["groupName"]; ?></h2>
				<?php
				echo ( "<form method=\"POST\" action=\"?node=" . $_GET["node"] . "&sub=" . $_GET["sub"] . "&groupid=" . $_GET["groupid"] . "\">" );
					echo ( "<center><table cellpadding=0 cellspacing=0 border=0 width=90%>" );
					echo ( "<tr><td>"._("Group Name").":</td><td><input class=\"smaller\" type=\"text\" name=\"name\" value=\"$ar[groupName]\" /></td></tr>" );
					echo ( "<tr><td>"._("Group Description").":</td><td><textarea name=\"description\" rows=\"3\" cols=\"40\">$ar[groupDesc]</textarea></td></tr>" );
					echo ( "<tr><td>"._("Group Kernel").":</td><td><input class=\"smaller\" type=\"text\" name=\"kern\" /></td></tr>" );	
					echo ( "<tr><td>"._("Group Kernel Arguments").":</td><td><input class=\"smaller\" type=\"text\" name=\"args\" /></td></tr>" );	
					echo ( "<tr><td>"._("Group Primary Disk").":</td><td><input class=\"smaller\" type=\"text\" name=\"dev\" /></td></tr>" );	
					echo ( "<tr><td colspan=2><center><br /><input type=\"hidden\" name=\"update\" value=\"1\" /><input class=\"smaller\" type=\"submit\" value=\""._("Update")."\" /></center></td></tr>" );
					echo ( "</table></center>" );
				echo ( "</form>" );
			}

			if ( $_GET["tab"] == "image"  )
			{
				?>
				<h2><?php print _("Image Association for $ar[groupName]"); ?></h2>
				<?php
				echo ( "<div class=\"hostgroup\">" );
					echo ( "<form method=\"POST\" action=\"?node=$_GET[node]&sub=$_GET[sub]&groupid=$_GET[groupid]&tab=$_GET[tab]\">" );
					$sql = "select * from images order by imageName";
					$res = mysql_query( $sql, $conn ) or die( mysql_error() );
					echo( "<select name=\"image\" size=\"1\">" );
					echo ( "<option value=\"\">"._("Do Nothing")."</option>" );
					while( $ar1 = mysql_fetch_array( $res ) )
					{
						echo ( "<option value=\"" . $ar1["imageID"] . "\" >" . $ar1["imageName"] . "</option>" );
					}
					echo ( "</select>" );
					echo ( "<p><input class=\"smaller\" type=\"submit\" value=\""._("Update Images")."\" /></p>" );
					echo ( "</form>" );
				echo ( "</div>" );
			}

			if ( $_GET["tab"] == "os"  )
			{
				?>
				<h2><?php print _("Operating System Association for $ar[groupName]"); ?></h2>
				<?php
				echo ( "<div class=\"hostgroup\">" );
					echo ( "<form method=\"POST\" action=\"?node=$_GET[node]&sub=$_GET[sub]&groupid=$_GET[groupid]&tab=$_GET[tab]\">" );
					echo ( getOSDropDown( $conn, $name="grpos" ) );
					echo ( "<p><input class=\"smaller\" type=\"submit\" value=\""._("Update Operating System")."\" /></p>" );
					echo ( "</form>" );
				echo ( "</div>" );
			}

			if ( $_GET["tab"] == "tasks" )
			{
				?>
				<h2><?php print _("Basic Imaging Tasks"); ?></h2>
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
				<td class="c" width="50"><a href="?node=tasks&type=group&direction=down&noconfirm=<?php echo $_GET["groupid"]; ?>"><img src="./images/senddebug.png" /><p><?php echo(_("Deploy")); ?></p></a></td>
				<td><p><?php echo(_("Deploy action will send an image saved on the FOG server to the client computer with all included snapins.")); ?></p></td>
				</tr>
				<tr>
				<td class="c" width="50"><a href="?node=tasks&sub=advanced&groupid=<?php echo $_GET["groupid"]; ?>"><img src="./images/host-advanced.png" /><p><?php echo(_("Advanced")); ?></p></a></td>
				<td><p><?php echo(_("View advanced tasks for this group.")); ?></p></td>
				</tr>
				</table>
				<?php
			}

			if ( $_GET["tab"] == "service" )
			{
				?>
				<h2><?php print _("Service Configuration"); ?></h2>
				<?php
				echo ( "<form method=\"post\" action=\"?node=$_GET[node]&sub=$_GET[sub]&groupid=$_GET[groupid]&tab=$_GET[tab]&updatemodulestatus=1\">" );
					echo ( "<center><table cellpadding=0 cellspacing=0 border=0 width=90%>" );
						echo ( "<tr>
							<td width=\"270\">&nbsp;"._("Set Hostname Changer status on all hosts to").":</td>
							<td>&nbsp;<select name=\"hostnamechanger\" size=\"1\">
							  <option value=\"nc\" label=\"Not Configured\">"._("Not Configured")."</option>
							  <option value=\"on\" label=\"Enabled\">"._("Enabled")."</option>
							  <option value=\"\" label=\"Disabled\">"._("Disabled")."</option>
							  </select>
							</td>
							<td><span class=\"icon icon-help hand\" title=\"" . _("This setting will enable or disable the hostname changer service module on this specific host.  If the module is globally disabled, this setting is ignored.") . "\"></span>
							</td>
							</tr>" );
						echo ( "<tr>
							  <td width=\"270\">&nbsp;"._("Set Directory Cleaner status on all hosts to").":</td>
							  <td>&nbsp;<select name=\"dircleanen\" size=\"1\">
								<option value=\"nc\" label=\"Not Configured\">"._("Not Configured")."</option>
								<option value=\"on\" label=\"Enabled\">"._("Enabled")."</option>
								<option value=\"\" label=\"Disabled\">"._("Disabled")."</option>
							  </select></td>
							  <td><span class=\"icon icon-help hand\" title=\"" . _("This setting will enable or disable the directory cleaner service module on this specific host.  If the module is globally disabled, this setting is ignored." ) . "\"></span></td></tr>" );
						echo ( "<tr>
							<td width=\"270\">&nbsp;"._("Set User Cleanup status on all hosts to").":</td>
							<td>&nbsp;<select name=\"usercleanen\" size=\"1\">
								<option value=\"nc\" label=\"Not Configured\">"._("Not Configured")."</option>
								<option value=\"on\" label=\"Enabled\">"._("Enabled")."</option>
								<option value=\"\" label=\"Disabled\">"._("Disabled")."</option>
								</select></td>
							<td><span class=\"icon icon-help hand\" title=\"" . _("This setting will enable or disable the user cleanup service module on this specific host.  If the module is globally disabled, this setting is ignored.") . "\"></span></td></tr>" );
				echo ( "<tr><td width=\"270\">&nbsp;"._("Set Display Manager status on all hosts to").":</td><td>&nbsp;<select name=\"displaymanager\" size=\"1\"><option value=\"nc\" label=\"Not Configured\">"._("Not Configured")."</option><option value=\"on\" label=\"Enabled\">"._("Enabled")."</option><option value=\"\" label=\"Disabled\">"._("Disabled")."</option></select></td><td><span class=\"icon icon-help hand\" title=\"" . _("This setting will enable or disable the display manager service module on this specific host.  If the module is globally disabled, this setting is ignored." ) . "\"></span></td></tr>" );
						echo ( "<tr><td width=\"270\">&nbsp;"._("Set Auto Log Out on all hosts to").":</td><td>&nbsp;<select name=\"alo\" size=\"1\"><option value=\"nc\" label=\"Not Configured\">"._("Not Configured")."</option><option value=\"on\" label=\"Enabled\">"._("Enabled")."</option><option value=\"\" label=\"Disabled\">"._("Disabled")."</option></select></td><td><span class=\"icon icon-help hand\" title=\"" . _("This setting will enable or disable the auto log out service module on this specific host.  If the module is globally disabled, this setting is ignored." ) . "\"></span></td></tr>" );
						echo ( "<tr><td width=\"270\">&nbsp;"._("Set Green FOG on all hosts to").":</td><td>&nbsp;<select name=\"gf\" size=\"1\"><option value=\"nc\" label=\"Not Configured\">"._("Not Configured")."</option><option value=\"on\" label=\"Enabled\">"._("Enabled")."</option><option value=\"\" label=\"Disabled\">"._("Disabled")."</option></select></td><td><span class=\"icon icon-help hand\" title=\"" . _("This setting will enable or disable the green fog service module on this specific host.  If the module is globally disabled, this setting is ignored." ) . "\"></span></td></tr>" );
						echo ( "<tr><td width=\"270\">&nbsp;"._("Set Snapin Client on all hosts to").":</td><td>&nbsp;<select name=\"snapin\" size=\"1\"><option value=\"nc\" label=\"Not Configured\">"._("Not Configured")."</option><option value=\"on\" label=\"Enabled\">"._("Enabled")."</option><option value=\"\" label=\"Disabled\">"._("Disabled")."</option></select></td><td><span class=\"icon icon-help hand\" title=\"" . _("This setting will enable or disable the snapin service module on this specific host.  If the module is globally disabled, this setting is ignored.") . "\"></span></td></tr>" );						
						echo ( "<tr><td width=\"270\">&nbsp;"._("Set Client Updater on all hosts to").":</td><td>&nbsp;<select name=\"clientupdater\" size=\"1\"><option value=\"nc\" label=\"Not Configured\">"._("Not Configured")."</option><option value=\"on\" label=\"Enabled\">"._("Enabled")."</option><option value=\"\" label=\"Disabled\">"._("Disabled")."</option></select></td><td><span class=\"icon icon-help hand\" title=\"" . _("This setting will enable or disable the client updater service module on this specific host.  If the module is globally disabled, this setting is ignored.") . "\"></span></td></tr>" );												
						echo ( "<tr><td width=\"270\">&nbsp;"._("Set Host Register on all hosts to").":</td><td>&nbsp;<select name=\"hostregister\" size=\"1\"><option value=\"nc\" label=\"Not Configured\">"._("Not Configured")."</option><option value=\"on\" label=\"Enabled\">"._("Enabled")."</option><option value=\"\" label=\"Disabled\">"._("Disabled")."</option></select></td><td><span class=\"icon icon-help hand\" title=\"" . _("This setting will enable or disable the client updater service module on this specific host.  If the module is globally disabled, this setting is ignored.") . "\"></span></td></tr>" );												
						echo ( "<tr><td width=\"270\">&nbsp;"._("Set Printer Manager on all hosts to").":</td><td>&nbsp;<select name=\"printermanager\" size=\"1\"><option value=\"nc\" label=\"Not Configured\">"._("Not Configured")."</option><option value=\"on\" label=\"Enabled\">"._("Enabled")."</option><option value=\"\" label=\"Disabled\">"._("Disabled")."</option></select></td><td><span class=\"icon icon-help hand\" title=\"" . _("This setting will enable or disable the printer manager service module on this specific host.  If the module is globally disabled, this setting is ignored.") . "\"></span></td></tr>" );												
						echo ( "<tr><td width=\"270\">&nbsp;"._("Set Task Reboot on all hosts to").":</td><td>&nbsp;<select name=\"taskreboot\" size=\"1\"><option value=\"nc\" label=\"Not Configured\">"._("Not Configured")."</option><option value=\"on\" label=\"Enabled\">"._("Enabled")."</option><option value=\"\" label=\"Disabled\">"._("Disabled")."</option></select></td><td><span class=\"icon icon-help hand\" title=\"" . _("This setting will enable or disable the task reboot service module on this specific host.  If the module is globally disabled, this setting is ignored.") . "\"></span></td></tr>" );												
						echo ( "<tr><td width=\"270\">&nbsp;"._("Set User Tracker on all hosts to").":</td><td>&nbsp;<select name=\"usertracker\" size=\"1\"><option value=\"nc\" label=\"Not Configured\">"._("Not Configured")."</option><option value=\"on\" label=\"Enabled\">"._("Enabled")."</option><option value=\"\" label=\"Disabled\">"._("Disabled")."</option></select></td><td><span class=\"icon icon-help hand\" title=\"" . _("This setting will enable or disable the user tracker service module on this specific host.  If the module is globally disabled, this setting is ignored.") . "\"></span></td></tr>" );								
						echo ( "<tr><td colspan='3'><center><br /><input type=\"submit\" value=\""._("Update")."\" /></center></td></tr>" );
					echo ( "</table></center>" );

					echo ( "<p class=\"titleBottomLeft\">"._("Group Screen Resolution")."</p>" );
						echo ( "<center><table cellpadding=0 cellspacing=0 border=0 width=90%>" );
							echo ( "<tr><td width=\"270\">&nbsp;"._("Screen Width (in pixels)")."</td><td>&nbsp;<input type=\"text\" name=\"x\" value=\"$x\"/></td><td><span class=\"icon icon-help hand\" title=\"" . _("This setting defines the screen horizontal resolution to be used with this host.  Leaving this field blank will force this host to use the global default setting") . "\"></span></td></tr>" );
							echo ( "<tr><td width=\"270\">&nbsp;"._("Screen Height (in pixels)")."</td><td>&nbsp;<input type=\"text\" name=\"y\" value=\"$y\"/></td><td><span class=\"icon icon-help hand\" title=\"" . _("This setting defines the screen vertial resolution to be used with this host.  Leaving this field blank will force this host to use the global default setting") . "\"></span></td></tr>" );
							echo ( "<tr><td width=\"270\">&nbsp;"._("Screen Refresh Rate")."</td><td>&nbsp;<input type=\"text\" name=\"r\" value=\"$r\" /></td><td><span class=\"icon icon-help hand\" title=\"" . _("This setting defines the screen refresh rate to be used with this host.  Leaving this field blank will force this host to use the global default setting") . "\"></span></td></tr>" );
							echo ( "<tr><td colspan='3'><center><br /><input type=\"submit\" value=\""._("Update")."\" /></center></td></tr>" );
						echo ( "</table></center>" );

					echo ( "<p class=\"titleBottomLeft\">"._("Auto Log Out Settings")."</p>" );
						echo ( "<center><table cellpadding=0 cellspacing=0 border=0 width=90%>" );
							echo ( "<tr><td width=\"270\">&nbsp;"._("Auto Log Out Time (in minutes)")."</td><td>&nbsp;<input type=\"text\" name=\"tme\" value=\"$tme\"/></td><td><span class=\"icon icon-help hand\" title=\"" . _("This setting defines the time to auto log out this host.") . "\"></span></td></tr>" );
							echo ( "<tr><td colspan='3'><center><br /><input type=\"submit\" value=\""._("Update")."\" /></center></td></tr>" );
						echo ( "</table></center>" );
				echo ( "</form>" );
			}

			if ( $_GET["tab"] == "snapadd"  )
			{
				?>
				<h2><?php print _("Add Snapin to all hosts in ") . $ar["groupName"]; ?></h2>
				<?php
				echo ( "<div class=\"hostgroup\">" );
					echo ( "<form method=\"POST\" action=\"?node=" . $_GET["node"] . "&sub=" . $_GET["sub"] . "&groupid=" . $_GET["groupid"] . "&tab=$_GET[tab]\">" );
					echo ( getSnapinDropDown( $conn ) );
					echo( "<p><input type=\"hidden\" name=\"gsnapinadd\" value=\"1\" /><input type=\"submit\" value=\""._("Add Snapin")."\" /></p>" );
					echo ( "</form>" );
				echo ( "</div>" );
			}

			if ( $_GET["tab"] == "snapdel" )
			{
				?>
				<h2><?php print _("Remove Snapin to all hosts in ") . $ar["groupName"]; ?></h2>
				<?php
				echo ( "<div class=\"hostgroup\">" );
					echo ( "<form method=\"POST\" action=\"?node=" . $_GET["node"] . "&sub=" . $_GET["sub"] . "&groupid=" . $_GET["groupid"] . "&tab=$_GET[tab]\">" );
					echo ( getSnapinDropDown( $conn ) );
					echo( "<p><input type=\"hidden\" name=\"gsnapindel\" value=\"1\" /><input type=\"submit\" value=\""._("Remove Snapin")."\" /></p>" );
					echo ( "</form>" );
				echo ( "</div>" );
			}

			if ( $_GET["tab"] == "ad" )
			{
				?>
				<h2><?php print _("Modify AD information for ") . $ar["groupName"]; ?></h2>
				<?php
				echo ( "<form method=\"POST\" action=\"?node=" . $_GET["node"] . "&sub=" . $_GET["sub"] . "&groupid=" . $_GET["groupid"] . "&tab=$_GET[tab]\">" );
				echo ( "<table cellpadding=0 cellspacing=0 border=0 width=90%>" );
					echo ( "<tr><td>"._("Join Domain after image task").":</td><td><input id='adEnabled' type=\"checkbox\" name=\"domain\" /></td></tr>" );
					echo ( "<tr><td>"._("Domain name").":</td><td><input id=\"adDomain\" class=\"smaller\" type=\"text\" name=\"domainname\" /></td></tr>" );
					echo ( "<tr><td>"._("Organizational Unit").":</td><td><input  id=\"adOU\" class=\"smaller\" type=\"text\" name=\"ou\" /> <span class=\"lightColor\">"._("(Blank for default)")."</span></td></tr>" );
					echo ( "<tr><td>"._("Domain Username").":</td><td><input id=\"adUsername\" class=\"smaller\" type=\"text\" name=\"domainuser\" /></td></tr>" );
					echo ( "<tr><td>"._("Domain Password").":</td><td><input id=\"adPassword\" class=\"smaller\" type=\"text\" name=\"domainpassword\" /> <span class=\"lightColor\">"._("(Must be encrypted)")."</span></td></tr>" );
					echo ( "<tr><td colspan=2><center><br /><input type=\"hidden\" name=\"updatead\" value=\"1\" /><input class=\"smaller\" type=\"submit\" value=\""._("Update")."\" /></center></td></tr>" );
				echo ( "</table>" );
				echo ( "</form>" );
			}

			if ( $_GET["tab"] == "member" )
			{
				?>
				<h2><?php print _("Modify Membership for ") . $ar["groupName"]; ?></h2>
				<?php
				echo ( "<form method=\"POST\" action=\"?node=$_GET[node]&sub=$_GET[sub]&groupid=$_GET[groupid]&tab=$_GET[tab]\">" );
				echo ( "<center><table cellpadding=0 cellspacing=0 border=0 width=100%>" );
				if ( $_GET["delhostid"] != null && is_numeric( $_GET["delhostid"] ) )
				{
					$sql = "delete from groupMembers where gmGroupID = '" . mysql_real_escape_string( $_GET["groupid"] ) . "' and gmHostID = '" . mysql_real_escape_string( $_GET["delhostid"] ) . "'";
					if ( !mysql_query( $sql, $conn ) )
						msgBox( _("Failed to remove host from group!") );

				}


				$members = getImageMembersByGroupID( $conn, $ar["groupID"] );
				if ( $members != null )
				{
					for( $i = 0; $i < count( $members ); $i++ )
					{
						if ( $members[$i] != null )
						{
							$bgcolor = "alt1";
							if ( $i % 2 == 0 ) $bgcolor = "alt2";
							echo ( "<tr class=\"$bgcolor\"><td>&nbsp;" . $members[$i]->getHostName() . "</td><td>&nbsp;" . $members[$i]->getIPaddress() . "</td><td>&nbsp;" . $members[$i]->getMAC() . "</td><td><a href=\"?node=$_GET[node]&sub=$_GET[sub]&groupid=" . $_GET["groupid"] . "&tab=$_GET[tab]&delhostid=" . $members[$i]->getID() . "\"><img src=\"images/deleteSmall.png\" class=\"link\" /></a></td></tr>" );
						}
					}
				}
				echo ( "</table></center>" );
				echo ( "</form>" );
			}

			if ( $_GET["tab"] == "del" )
			{
				?>
				<h2><?php print _("Delete Group"); ?></h2>
				<?php
				echo ( "<p>"._("Click on the icon below to delete this group from the FOG database.")."</p>" );
				echo ( "<p><a href=\"?node=$_GET[node]&sub=$_GET[sub]&delgroupid=" . $ar["groupID"] . "\"><img class=\"link\" src=\"images/delete.png\"></a></p>" );
			}

		echo ( "</div>" );
	}
}
else if ( $_GET["delgroupid"] != null )
{
	if ( $_GET["delgroupid"] != null && is_numeric( $_GET["delgroupid"] ) )
	{
		$delid = mysql_real_escape_string( $_GET["delgroupid"] );
		if ( $_GET["confirm"] != 1 )
		{
			$sql = "select * from groups where groupID = '" . $delid . "'";
			$res = mysql_query( $sql, $conn ) or die( mysql_error() );
			if ( $ar = mysql_fetch_array( $res ) )
			{
				?>
				<h2><?php print _("Confirm Group Removal"); ?></h2>
				<?php
				echo ( "<form method=\"POST\" action=\"?node=$_GET[node]&sub=$_GET[sub]&delgroupid=$_GET[delgroupid]&confirm=1\">" );
				echo ( "<center><table cellpadding=0 cellspacing=0 border=0 width=90%>" );
					echo ( "<tr><td>"._("Group Name").":</td><td>" . $ar["groupName"] . "</td></tr>" );
				echo ( "<tr><td colspan=2><center><br /><input class=\"smaller\" type=\"submit\" value=\""._("Yes, delete only this group")."\" /></center></td></tr>" );
				echo ( "</table></center>" );
				echo ( "</form>" );
				
				echo ( "<br /><br /><br /><br />" );
				
				echo ( "<form method=\"POST\" action=\"?node=$_GET[node]&sub=$_GET[sub]&delgroupid=$_GET[delgroupid]&confirm=1&allhosts=1\">" );
				echo ( "<center><table cellpadding=0 cellspacing=0 border=0 width=90%>" );
					echo ( "<tr><td colspan=2><center><br /><input class=\"smaller\" type=\"submit\" value=\""._("Yes, delete group & ALL hosts objects")."\" /></center></td></tr>" );
				echo ( "</table></center>" );
				echo ( "</form>" );
			}
		}
		else
		{
			// Check if we should remove all the host objects
			if ( $_GET["allhosts"] == "1" )
			{
				$members = getImageMembersByGroupID( $conn, $delid );
				if ( $members != null )
				{
					for( $i =0; $i < count( $members ); $i++ )
					{
						if ( $members[$i] != null )
						{
							$sql = "DELETE FROM hosts WHERE hostID = '" . mysql_real_escape_string($members[$i]->getID()) . "'";
							if ( ! mysql_query( $sql, $conn ) )
							{
								criticalError( mysql_error(), _("FOG :: Database error!") );
							}
						}									
					}
				}
			}


			$sql = "delete from groups where groupID = '" . $delid . "'";
			if ( mysql_query( $sql, $conn ) )
			{
				// now delete all the associations
				$sql = "delete from groupMembers where gmGroupID = '" . $delid . "'";
				if ( mysql_query( $sql, $conn ) )
				{
					lg( _("Deleted group")." $_GET[delgroupid]" );
					?>
					<h2><?php print _("Group Removal Complete"); ?></h2>
					<p><?php print _("Group has been deleted."); ?></p>
					<?php
				}
				else
					echo ( mysql_error() );
			}
			else
				echo ( mysql_error() );

		}
	}
}
else
{
	echo _("Invalid group information.");
}
