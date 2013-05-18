<?php
/*
 *  FOG - is a computer imaging solution.
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

if ( $_POST["add"] != null )
{
		if ( ! imageDefExists( $conn, $_POST[name] ) )
		{
			$name = mysql_real_escape_string( $_POST[name] );
			$description = mysql_real_escape_string( $_POST[description] );
			$file = mysql_real_escape_string( $_POST[file] );
			$user = mysql_real_escape_string( $currentUser->getUserName() );
			$storagenode = mysql_real_escape_string( $_POST["storagegroup"] );
			$dd = "0";
			if ( is_numeric($_POST["imagetype"]) )
				$dd = mysql_real_escape_string($_POST["imagetype"]);			
		
			if ( $file != null )
			{
				if ( is_numeric( $dd ) && $dd >= 0 )
				{
					if ( is_numeric($storagenode) && $storagenode != null && $storagenode > 0 )
					{
						$sql = "insert into images(imageName, imageDesc, imagePath, imageDateTime, imageCreateBy, imageDD, imageNFSGroupID) values('$name', '$description', '$file', NOW(), '" . mysql_real_escape_string( $currentUser->getUserName() ) . "', '$dd', '$storagenode' )";
						if ( mysql_query( $sql, $conn ) )
						{
							msgBox( _("Image created.")."<br />"._("You may now add another.") );
							lg( _("Image Added")." :: $name" );
						}
						else
						{
							msgBox( _("Failed to add image.") );
							lg( _("Failed to add image")." :: $name " . mysql_error()  );
						}
					}
					else
						msgBox( _("A Storage Group is required!") );
				}
				else
					msgBox( _("An image type is required!") );
			}
			else
				msgBox( _("An image file name is required!") );
		}
		else
			msgBox( _("An image already exists with this name!") );	
}
?>
<h2><?php print _("Add new image definition"); ?></h2>
<?php
echo ( "<form method=\"POST\" action=\"?node=$_GET[node]&sub=$_GET[sub]\">" );
echo ( "<center><table cellpadding=0 cellspacing=0 border=0 width=90%>" );
	echo ( "<tr><td>"._("Image Name").":</td><td><input class=\"smaller\" type=\"text\" name=\"name\" value=\"\" id=\"iName\" onblur=\"duplicateImageName();\" /></td></tr>" );
	echo ( "<tr><td>"._("Image Description").":</td><td><textarea class=\"smaller\" name=\"description\" rows=\"5\" cols=\"65\"></textarea></td></tr>" );
	echo ( "<tr><td>"._("Storage Group").":</td><td>" . getNFSGroupDropDown( $conn ) . "</td></tr>" );				
	$storagedir = "["._("StorageNodeRootDir")."]";
	$tmpSD = getStorageRootByGroupID( $conn, $ar["imageNFSGroupID"] );
	if ( $tmpSD != null )
		$storagedir = $tmpSD;	
	echo ( "<tr><td>"._("Image File").":</td><td>" . $storagedir . "<input class=\"smaller\" type=\"text\" name=\"file\" value=\"\" id=\"iFile\" /></td></tr>" );
	echo ( "<tr><td>"._("Image Type").":</td><td>" . getImageTypeDropDown(  ) . " <a href=\"javascript:popUpWindow('static/imagetypehelp.html');\"><img class=\"noBorder\" src=\"./images/help.png\" /></a></td></tr>" );				
	echo ( "<tr><td colspan=2><center><br /><input type=\"hidden\" name=\"add\" value=\"1\" /><input class=\"smaller\" type=\"submit\" value=\""._("Add")."\" /></center></td></tr>" );				
echo ( "</table></center>" );
echo ( "</form>" );