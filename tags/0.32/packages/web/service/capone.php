<?php
/*
 *  FOG is a computer imaging solution.
 *  Copyright (C) 2008  Chuck Syperski & Jian Zhang
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
@error_reporting(0);
require_once( "../commons/config.php" );
require_once( "../commons/functions.include.php" );



$conn = @mysql_connect( MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD);
if ( $conn )
{
	if ( ! @mysql_select_db( MYSQL_DATABASE, $conn ) ) die( "#!db" );
	
	if ( $_GET["action"] == "dmi" )
	{
		echo getSetting( $conn, "FOG_PLUGIN_CAPONE_DMI" );
	}
	else if ( $_GET["action"] == "imagelookup" && $_GET["key"] != null )
	{
		$key = mysql_real_escape_string( trim(base64_decode( trim($_GET["key"]) )) );
		$sql = "SELECT 
				* 
			FROM 
				capone
				INNER JOIN images on (cImageID = imageID )
				INNER JOIN supportedOS on ( cOSID = osValue )
			WHERE 
				cKey = '" . $key . "' or
				INSTR( '$key', cKey ) = 1
			ORDER BY 
				imageName";

		$res = mysql_query( $sql, $conn ) or die( mysql_error() );
		if ( mysql_num_rows( $res ) > 0 )
		{
			while( $ar = mysql_fetch_array( $res ) )
			{
				$imgType = "";
				switch( $ar["imageDD"] )
				{
					case 0:
						$imgType = "n";
						break;
					case 1:
						$imgType = "dd";
						break;
					case 2:
						$imgType = "mps";
						break;
					case 3:
						$imgType = "mpa";
						break;
				}
				echo base64_encode( getSetting( $conn, "FOG_NFS_DATADIR" ) . $ar["imagePath"] . "|" . $ar["osValue"] . "|" . $imgType  )  . "\n";
			}
		}
		else
			echo base64_encode( "null" );
	}
}

?>
