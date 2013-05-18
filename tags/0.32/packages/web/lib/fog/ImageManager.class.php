<?php
/*
 *  FOG is a computer imaging solution.
 *  Copyright (C) 2009  Chuck Syperski & Jian Zhang
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
 
class ImageManager
{
	private $db;

	function __construct( $db )
	{
		$this->db = $db;
	}
	
	public function getImageById( $imageid )
	{
		if ( $this->db != null && $imageid !== null && is_numeric( $imageid ) && $imageid >= 0 )
		{
			$sql = "select *, UNIX_TIMESTAMP(imageDateTime) as unxDate from images where imageID = '" . $this->db->escape($imageid) . "'";
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{

					$image = new Image( $ar["imageID"], $ar["imageDD"], $ar["imageName"], $ar["imageDesc"], $ar["imagePath"], $ar["imageCreateBy"], new Date($ar["unxDate"]) );
					return $image;
				}		
			}		
		}
		return null;
	}	
}
?>
