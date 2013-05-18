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

class StorageNode
{
	private $intID;
	private $strName, $strDesc;
	private $blIsMaster, $blEnabled;
	private $strRoot;
	private $strHostIP;
	private $intMaxClients;
	private $strUser, $strPass;
	
	function __construct( $id, $name, $desc, $isMaster, $isEnabled, $root, $ip, $maxclients, $user, $pass )
	{
		$this->intID = $id;
		$this->strName = $name;
		$this->strDesc = $desc;
		$this->blIsMaster = $isMaster;
		$this->blEnabled = $isEnabled;
		$this->strRoot = $root;
		$this->strHostIP = $ip;
		$this->intMaxClients = $maxclients;
		$this->strUser = $user;
		$this->strPass = $pass;
	}	

	function getID() { return $this->intID; }
	function getName() { return $this->strName; }
	function getDescription() { return $this->strDesc; }
	function isMaster() { return $this->blIsMaster; }	
	function isEnabled() { return $this->blEnabled; }
	function getRoot() { return $this->strRoot; }
	function getHostIP() { return $this->strHostIP; }
	function getMaxClients() { return $this->intMaxClients; }	
	function getUser() { return $this->strUser; }
	function getPass() { return $this->strPass; }	
	
}

?>
