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
 
class Image
{
	const IMAGE_TYPE_SINGLE_PARTITION_NTFS = 0;
	const IMAGE_TYPE_DD = 1;
	const IMAGE_TYPE_MULTIPARTITION_SINGLE_DISK = 2;
	const IMAGE_TYPE_MULTIPARTITION_MULTIDISK = 3;

	private $intID, $intImageType;
	private $strName, $strDescription, $strPath, $strCreateBy; 
	private $storageGroup;
	private $date;							
	
	function __construct( $id=null, $type=null, $name=null, $desc=null, $path=null, $by=null, $date=null, $storagegroup=null )
	{
		$this->intID 						= $id;
		$this->intImageType 					= $type;
		$this->strName 						= $name;
		$this->strDescription 					= $desc;
		$this->strPath						= $path;
		$this->strCreateBy					= $by;
		$this->storageGroup					= $storagegroup;
		$this->date						= $date;
	}

	public function setID( $id )				{ $this->intID = $id; }
	public function getID()					{ return $this->intID; 	}

	public function setType( $t )				{ $this->intImageType = $t; }
	public function getType()				{ return $this->intImageType; 	}
	
	public function setName( $n )				{ $this->strName = $n; }
	public function getName()				{ return $this->strName;}
	
	public function setDescription( $d )			{ $this->strDescription = $d; }
	public function getDescription()			{ return $this->strDescription; 	}
	
	public function setPath( $p )				{ $this->strPath = $p; }
	public function getPath()				{ return $this->strPath; }
	
	public function setCreator( $c )			{ $this->strCreateBy = $c; }
	public function getCreator()				{ return $this->strCreateBy; 	}					
	
	public function setStorageGroup( $s )			{ $this->storageGroup = $s; }
	public function getStorageGroup()			{ return $this->storageGroup; 	}
	
	public function setDate( $d )				{ $this->date = $d; }
	public function getDate()				{ return $this->date; 	}		
}
?>
