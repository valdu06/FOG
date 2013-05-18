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

$storagedir = "["._("StorageNodeRootDir")."]";

if ( $_GET["rmimageid"] != null && is_numeric( $_GET["rmimageid"] ) )
{
	$rmid = mysql_real_escape_string( $_GET["rmimageid"] );
	
	$sql = "SELECT imageNFSGroupID FROM images WHERE imageID = '$rmid'";
	$res = mysql_query( $sql, $conn ) or die( mysql_error() );
	$nfsGroup = -1;
	while( $ar = mysql_fetch_array( $res ) )
	{
		$nfsGroup = $ar["imageNFSGroupID"];
	}
	$tmpSD = getStorageRootByGroupID( $conn, $nfsGroup );
	if ( $tmpSD != null )
		$storagedir = $tmpSD;	
	
	if ( $_GET["confirm"] != "1" )
	{
		$sql = "select * from images where imageID = '$rmid'";
		$res = mysql_query( $sql, $conn ) or die( mysql_error() );
		if ( $ar = mysql_fetch_array( $res ) )
		{
			?>
			<h2><?php print _("Confirm Image Removal"); ?></h2>
			<?php
			echo ( "<center><table cellpadding=0 cellspacing=0 border=0 width=90%>" );
				echo ( "<tr><td>"._("Image Name").":</td><td>" . $ar["imageName"] . "</td></tr>" );
				echo ( "<tr><td>"._("Image Description").":</td><td>" . $ar["imageDesc"] . "</td></tr>" );
				echo ( "<tr><td>"._("Image File").":</td><td>" . $storagedir . $ar["imagePath"] . "</td></tr>" );
				echo ( "<tr><td colspan=2><center><br /><form method=\"POST\" action=\"?node=$_GET[node]&sub=$_GET[sub]&rmimageid=$_GET[rmimageid]&confirm=1\"><input type=\"submit\" value=\""._("Delete only the image definition.")."\" /></form><br /><form method=\"POST\" action=\"?node=$_GET[node]&sub=$_GET[sub]&rmimageid=$_GET[rmimageid]&confirm=1&killfile=1\"><input type=\"submit\" value=\""._("Delete image definition, and image file.")."\" /></form></center></td></tr>" );				
			echo ( "</table></center>" );		
		}
	}
	else
	{
		$output = "";
		?>
		<h2><?php print _("Image Removal Results"); ?></h2>
		<?php
		if ( $_GET["killfile"] == "1" )
		{
			$sql = "select imagePath, imageDD from images where imageID = '" . $rmid . "'";
			$res = mysql_query( $sql, $conn ) or die( mysql_error() );
			$file = null;
			$imageType = null;
			while( $ar = mysql_fetch_array( $res ) )
			{
				$file = $ar["imagePath"];
				$imageType = $ar["imageDD"];
			}
			
			if ( $file !== null )
			{
				if ( $imageType == ImageMember::IMAGETYPE_MULTIPART_SINGLEDRIVE || $imageType == ImageMember::IMAGETYPE_MULTIPART_ALLDRIVES )
				{
					if ( ftpDeleteImageDir($conn, $rmid) )
					{
						$output .= _("Image file has been deleted.")."<br />";
					}
					else
					{
						$output .= _("Failed to delete image file.")."<br />";
					}
				}
				else
				{
					if ( ftpDeleteImage($conn, $rmid) )
					{
						$output .= _("Image file has been deleted.")."<br />";
					}
					else
					{	
						$output .= _("Failed to delete image file.")."<br />";
					}
				}
			}
			else
				$output .= _("Failed to locate image file.")."<br />";
		}
		$sql = "delete from images where imageID = '" . $rmid . "'";
		if ( mysql_query( $sql, $conn ) )
		{
			$output .= _("Image definition has been removed.")."<br />";
			lg( _("image deleted")." :: $_GET[delid]" );				
		}
		else
			$output .= mysql_error();
			
		echo $output;
	}
	echo ( "</div>" );	
}
else
{
	if ( $_POST["update"] == "1" && is_numeric( $_POST["imgid"] ) )
	{
		if ( ! imageDefExists( $conn, $_POST["name"], $_POST["imgid"] ) )
		{
			$name = mysql_real_escape_string( $_POST["name"] );
			$description = mysql_real_escape_string( $_POST["description"] );
			$file = mysql_real_escape_string( $_POST["file"] );
			$imgid = mysql_real_escape_string( $_POST["imgid"] );
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
						$sql = "update images set imageName = '$name', imageDesc = '$description', imagePath ='$file', imageDD = '$dd', imageNFSGroupID = '$storagenode' where imageID = '$imgid'";
						if ( mysql_query( $sql, $conn ) )
						{
							msgBox( _("Image updated").": $name" );
							lg( _("Image updated").": $name" );
						}
						else
						{
							msgBox( _("Failed to update image")."." );
							lg( _("Failed to update image")." :: $name " . mysql_error()  );
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
	<h2><?php print _("Edit image definition"); ?></h2>
	<?php
	
	$sql = "select * from images where imageID = '" . mysql_real_escape_string( $_GET["imageid"] ) . "'";
	$res = mysql_query( $sql, $conn ) or die( mysql_error() );
	if ( $ar = mysql_fetch_array( $res ) )
	{
		echo ( "<center>" );
		if ( $_GET["tab"] == "gen" || $_GET["tab"] == "" )
		{
			echo ( "<form method=\"POST\" action=\"?node=$_GET[node]&sub=$_GET[sub]&imageid=$_GET[imageid]\">" );
			echo ( "<table cellpadding=0 cellspacing=0 border=0 width=90%>" );
				echo ( "<tr><td>"._("Image Name").":</td><td><input type=\"text\" name=\"name\" value=\"" . $ar["imageName"] . "\" /></td></tr>" );
				echo ( "<tr><td>"._("Image Description").":</td><td><textarea name=\"description\" rows=\"10\" cols=\"40\">" . $ar["imageDesc"] . "</textarea></td></tr>" );
				echo ( "<tr><td>"._("Storage Group").":</td><td>" . getNFSGroupDropDown( $conn, "storagegroup", $ar["imageNFSGroupID"] ) . "</td></tr>" );				
				$tmpSD = getStorageRootByGroupID( $conn, $ar["imageNFSGroupID"] );
				if ( $tmpSD != null )
					$storagedir = $tmpSD;
				echo ( "<tr><td>"._("Image File").":</td><td>" . $storagedir . "<input type=\"text\" name=\"file\" value=\"" . $ar["imagePath"] . "\" /></td></tr>" );
				echo ( "<tr><td>"._("Image Type").":</td><td>" . getImageTypeDropDown( "imagetype", $ar["imageDD"] ) . " <a href=\"javascript:popUpWindow('static/imagetypehelp.html');\"><img class=\"noBorder\" src=\"./images/help.png\" /></a></td></tr>" );
				echo ( "<tr><td colspan=2><center><br /><input type=\"hidden\" name=\"update\" value=\"1\" /><input type=\"hidden\" name=\"imgid\" value=\"" . $ar["imageID"] . "\" /><input type=\"submit\" value=\""._("Update")."\" /></center></td></tr>" );				
			echo ( "</table>" );
			echo ( "</form>" );
		}
		else if ( $_GET["tab"] == "delete" )
		{
			echo ( "<p>"._("Are you sure you would like to remove this image?")."</p>" );
			echo ( "<p><a href=\"?node=" . $_GET["node"] . "&sub=" . $_GET["sub"] . "&rmimageid=" . $ar["imageID"] . "\"><img class=\"link\" src=\"images/delete.png\"></a></p>" );
		}
		echo ( "</center>" );
	}
}