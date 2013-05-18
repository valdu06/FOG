<?php
/*
 *  FOG is a computer imaging solution.
 *  Copyright (C) 2010  Chuck Syperski & Jian Zhang
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
 
class Snapin
{
	private $id;
	private $name, $description, $file, $args, $creator, $runWith, $runWithArgs;
	private $objDate;
	private $blReboot;

	function __construct( $id, $name, $description, $file, $args, $dateObj, $creator, $reboot, $runwith, $runwithargs ) 
	{
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
		$this->file = $file;
		$this->args = $args;
		$this->creator = $creator;
		$this->blReboot = $reboot;
		$this->runWith = $runwith;
		$this->objDate = $dateObj;
		$this->runWithArgs = $runwithargs;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function getFile()
	{
		return $this->file;
	}	

	public function getArgs()
	{
		return $this->args;
	}	

	public function getCreator()
	{
		return $this->creator;
	}	

	public function reboot()
	{
		return $this->blReboot;
	}	

	public function getRunWith()
	{
		return $this->runWith;
	}	

	public function getRunWithArgs()
	{
		return $this->runWithArgs;
	}	

	public function getDate()
	{
		return $this->objDate;
	}	
}
?>
