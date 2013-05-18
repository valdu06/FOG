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
 
class Group
{
	private $intID;
	private $strName, $strDesc, $strCreateBy;
	private $date;							// Date Object
	private $arMembers;
	
	function __construct( $id=null, $name=null, $desc=null, $d=null, $createby=null)
	{
		$this->intID 					= $id;
		$this->date 					= $d;
		$this->strName					= $name;
		$this->strDesc 					= $desc;
		$this->strCreateBy				= $createby;
		$this->arMembers				= array();
	}

	public function clearHosts()
	{
		$this->arMembers = array();
	}

	public function addHost( $host )			
	{ 
		return false;
	}
	
	public function getHosts()
	{
		return $this->arMembers;
	}
	
	public function getHostCount()
	{
		if ( $this->getHosts() != null )
			return count( $this->getHosts() );
		
		return 0;
	}
	
	public function hasHost( $h )
	{
		return false;
	}
	

	public function setID( $id )				{ $this->intID = $id; }
	public function getID()					{ return $this->intID; 	}
	
	public function setName( $n )				{ $this->strName = $hn; }
	public function getName( )				{ return $this->strName; }
	
	public function setDescription( $desc )			{ $this->strDesc = $desc; }
	public function getDescription( )			{ return $this->strDesc; }
	
}
?>
