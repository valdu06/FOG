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

	private $intID;
	private $strName;
	private $strDesc;
	private $strPath;
	private $strDateTime;
	private $strCreatedBy;
	private $strImageType;
	private $storageGroup;

	function __construct( $id, $name, $desc, $path, $datetime, $creator, $imgtype, $objgroup=null )
	{
		$this->intID = $id;
		$this->strName = $name;
		$this->strDesc = $desc;
		$this->strPath = $path;
		$this->strDateTime = $datetime;
		$this->strCreatedBy = $creator;
		$this->strImageType = $imgtype;
		$this->storageGroup = $objgroup;
	}

	function getID() { return $this->intID; }
	function getName() { return $this->strName; }
	function getDescription() { return $this->strDesc; }
	function getPath() { return $this->strPath; }
	function getDateTime() { return $this->strDateTime; }
	function getCreator() { return $this->strCreatedBy; }
	function getImageType() { return $this->strImageType; }
	
	function setStorageGroup( $objsg ) { $this->storageGroup = $objsg; }
	function getStorageGroup() { return $this->storageGroup; }
}
 
?>
