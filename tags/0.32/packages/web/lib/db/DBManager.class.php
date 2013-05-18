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

class DBManager
{
	const DBTYPE_UNKNOWN 	= -1;
	const DBTYPE_MYSQL 	= 1;
	const DBTYPE_MSSQL 	= 2;
	const DBTYPE_ORACLE 	= 3;

	private $intDBType;
	private $strHost, $strUser, $strPass, $strSchema;
	private $intPort;

	function __construct( $dbtype ) 
	{
		$this->intDBType = $dbtype;
	}
	
	function setHost( $host, $port=null )
	{
		$this->strHost = $host;
		$this->intPort = $port;
	}
	
	function setCredentials( $username, $password="" )
	{
		$this->strUser = $username;
		$this->strPass = $password;
	}
	
	function setSchema( $schema )
	{
		$this->strSchema = $schema;
	}
	
	function connect()
	{
		try
		{
			switch( $this->intDBType )
			{
				case self::DBTYPE_MYSQL:
					$db = new MySql();
					$db->setCredentials( $this->strUser, $this->strPass );
					$db->setHost( $this->strHost, $this->intPort );
					$db->setSchema( $this->strSchema );
					if ( $db->connect() )
						return $db;
					break;
				case self::DBTYPE_MSSQL:
					break;
				case self::DBTYPE_ORACLE:
					$db = new Oracle();
					$db->setCredentials( $this->strUser, $this->strPass );
					$db->setHost( $this->strHost, $this->intPort );
					$db->setSchema( $this->strSchema );
					if ( $db->connect() )
						return $db;				
					break;								
				default:
					throw new Exception('Unknown database type');
			}
			
			return null;
		}
		catch( Exception $e )
		{
			throw $e;
			return null;
		}
	}
}

?>
