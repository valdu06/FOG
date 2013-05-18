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
	
if ( IS_INCLUDED !== true ) die( _("Unable to load system configuration information.") );

if ( $_GET["updatestatus"] == "1" )
{
	$value = "0";
	if ( $_POST["en"] == "on" )
		$value = "1";
	$sql = "UPDATE 
			globalSettings
		SET
			settingValue = '$value'
		WHERE
			settingKey = 'FOG_SERVICE_HOSTNAMECHANGER_ENABLED'";
	if ( ! mysql_query( $sql, $conn ) )
	{
		criticalError( mysql_error(), _("FOG :: Database error!") );
	}
}

?>
	<h2><?php echo(_("Configure Hostname Changer Service Module")); ?></h2>
	
	<p class="l padded">
		<?php echo(_("The hostname changer is a service module that rename the client's hostname after imaging.  This service also handles Microsoft Active Directory integration.")); ?>
	</p>
	<p class="titleBottomLeft"><?php echo(_("Service Status")); ?></p>	
<?php		
	echo ( "<form method=\"post\" action=\"?node=$_GET[node]&sub=$_GET[sub]&updatestatus=1\">" );
		echo ( "<center><table cellpadding=0 cellspacing=0 border=0 width=90%>" );
			$enabled = getSetting( $conn, "FOG_SERVICE_HOSTNAMECHANGER_ENABLED" );
			$checked = "";
			if ( $enabled == "1" )
			{
				$checked = " checked=\"checked\" ";
			}
			echo ( "<tr><td width=\"270\">&nbsp;"._("Hostname Changer Enabled?")."</td><td>&nbsp;<input type=\"checkbox\" name=\"en\" $checked /></td><td><span class=\"icon icon-help hand\" title=\"" . _("This setting will globally enable or disable the hostname changer service module.  If you disable the module, it will be disabled for all clients, regardsless of the host specific setting.") . "\"></span></td></tr>" );
			echo ( "<tr><td colspan='3'><center><input type=\"submit\" value=\""._("Update")."\" /></center></td></tr>" );			
		echo ( "</table></center>" );
	echo ( "</form>" );
?>