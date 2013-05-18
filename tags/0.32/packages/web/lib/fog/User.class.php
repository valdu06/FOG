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
 
class User
{
	private $strUserName, $strAuthIP, $strTime, $strType, $strPass;
	private $intID;

	const TYPE_ADMIN = '0';
	const TYPE_MOBILE = '1';

	function __construct($id, $username, $authIp, $authTime, $type)
	{
		$this->intID = $id;
		$this->strUserName = $username;
		$this->strAuthIP = $authIp;
		$this->strTime = $authTime;
		$this->strType = $type;
		$this->strPass = null;
	}
	
	public function setPassword( $p )	{ $this->strPass = $p; }
	public function getPassword()		{ return $this->strPass; }
	
	public function getID() 		{ return $this->intID; }
	public function setID( $id )		{ $this->intID = $id; }
	
	public function setUserName($s)		{ $this->strUserName = $s; }
	public function getUserName() 		{ return $this->strUserName; }
	
	public function setAuthIp($ip)		{ $this->strAuthIP = $ip; }
	public function getAuthIp() 		{ return $this->strAuthIP; }

	public function setAuthTime($t) 	{ $this->strTime = $t; }	
	public function getAuthTime() 		{ return $this->strTime; }
	
	public function isLoggedIn() 		{ return true; }
	
	public function setType($t)		{ $this->strType = $t; }
	public function getType() 		{ return $this->strType; }
}
?>
