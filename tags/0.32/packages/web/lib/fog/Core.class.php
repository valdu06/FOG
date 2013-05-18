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
 
class Core
{
	private $db;

	function __construct( $db ) 
	{
		$this->db = $db;
	}
	
	/**
	 * Constructs a new HostManager Object and returns it
	 *
	 * @return HostManager HostManager Object
	 */
	public function getHostManager()
	{
		return new HostManager( $this->db );
	}

	/**
	 * Constructs a new UserManager Object and returns it
	 *
	 * @return UserManager UserManager Object
	 */
	public function getUserManager()
	{
		return new UserManager( $this->db );
	}
	
	/**
	 * Constructs a new GroupManager Object and returns it
	 *
	 * @param string $hostManager The HostManager Object to use when creating the GroupManager
	 * @return GroupManager GroupManager Object
	 */
	public function getGroupManager($hostManager=null)
	{
		if ( $hostManager == null )
			return new GroupManager( $this->db, $this->getHostManager() );
		else
			return new GroupManager( $this->db, $hostManager );
	}
	
	public function getImageManager()
	{
		return new ImageManager( $this->db );
	}
	
	public function getTaskManager()
	{
		return new TaskManager( $this, $this->db );
	}

	public function getClientServiceManager()
	{
		return new ClientServiceManager( $this->db );
	}
	
	function getGlobalSetting( $key )
	{
		if ( $key != null && $this->db != null )
		{
			$sql = "SELECT settingValue FROM globalSettings WHERE settingKey = '" . $this->db->escape($key) . "'";
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{			
					return $ar["settingValue"];
				}
			}
		}
		return null;
	}
	
	function getMACManufacturer( $macprefix )
	{
		if ( $this->db && strlen( $macprefix ) == 8 )
		{
			$sql = "SELECT
					ouiMan
				FROM 
					oui
				WHERE
					ouiMACPrefix = '" . $this->db->escape( $macprefix ) . "'";
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{	
					return $ar["ouiMan"];
				}
			}
		}
		return _("n/a");
	}
	
	function addUpdateMACLookupTable( $macprefix, $strMan )
	{
		if ( $this->db && strlen( $macprefix ) == 8 && $strMan != null && strlen( $strMan ) > 0 )
		{
			if ( $this->doesMACLookCodeExist( $macprefix ) )
			{
				// update
				$sql = "UPDATE
						oui
					SET
						ouiMan = '" . $this->db->escape( $strMan ) . "'
					WHERE
						ouiMACPrefix = '" . $this->db->escape( $macprefix ) . "'";
				$this->db->executeUpdate( $sql );
				return true;
			}
			else
			{
				// insert
				$sql = "INSERT INTO
						oui
							(ouiMACPrefix, ouiMan)
						VALUES
							('" . $this->db->escape( $macprefix ) . "', '" . $this->db->escape( $strMan ) . "')";
				return $this->db->executeUpdate( $sql ) == 1;
			}
		}
		return false;
	}
	
	private function doesMACLookCodeExist( $macprefix )
	{
		if ( $this->db != null  )
		{
			if ( strlen( $macprefix ) == 8 )
			{
				$sql = "SELECT
						count(*) as cnt 
					FROM 
						oui
					WHERE
						ouiMACPrefix = '" . $this->db->escape( $macprefix ) . "'";
				if ( $this->db->executeQuery($sql) )
				{
					while( $ar = $this->db->getNext() )
					{	
						return $ar["cnt"] > 0;
					}
				}
				else
					throw new Exception( _("Unable to lookup mac prefix!") );
			}
			else
				throw new Exception( _("Invalid mac prefix")." " . $macprefix );	
		}
		else
			throw new Exception( _("Unable to lookup mac prefix!") );
	}
	
	function clearMACLookupTable()
	{
		if ( $this->db != null )
		{
			$sql = "DELETE 
				FROM 
					oui
				WHERE
					1=1";
			return ( $this->db->executeUpdate($sql) );

		}
		return false;
	}
	
	function getMACLookupCount()
	{
		if ( $this->db != null )
		{
			$sql = "SELECT 
					COUNT(*) AS cnt
				FROM 
					oui";
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{			
					return $ar["cnt"];
				}
			}					
		}
		return -1;
	}	
}

?>
