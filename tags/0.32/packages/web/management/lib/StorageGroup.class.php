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

class StorageGroup
{
	private $intID;
	private $strName, $strDesc;
	private $members;
	
	function __construct( $id, $name, $desc )
	{
		$this->intID = $id;
		$this->strName = $name;
		$this->strDesc = $desc;
		$this->members = array();
	}	
	
	function getID() { return $this->intID; }
	function getName() { return $this->strName; }
	function getDescription() { return $this->strDesc; }
	function getMembers() { return $this->members; }
	
	function getMasterNode()
	{
		if ( $this->members != null )
		{
			for( $i = 0; $i < count( $this->members ); $i++ )
			{
				$mem = $this->members[$i];
				if ( $mem != null )
				{
					if ( $mem->isMaster() )
						return $mem;
				}
			}
		}
		return null;
	}
	
	function addMember( $storagenode )
	{
		if ( $storagenode != null )
		{
			$blFound = false;
			if ( $this->members != null )
			{
				for ( $i = 0; $i < count( $this->members ); $i++ )
				{
					$m = $this->members[$i];
					if ( $m != null )
					{
						if ( $storagenode->getID() == $m->getID() )
						{
							$blFound = true;
							break;
						}	
					}
				}
			}
			
			if ( ! $blFound )
			{
				$this->members[] = $storagenode;
				return true;
			}
		}
		return false;
	}
	
}

?>
