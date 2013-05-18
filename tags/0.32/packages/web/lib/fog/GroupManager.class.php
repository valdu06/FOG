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
 
class GroupManager
{
	private $db;
	private $hostManager;							// used only to populate host members of group

	function __construct( $db, $hostman )
	{
		$this->db = $db;
		$this->hostManager = $hostman;
	}

	public function getGroupByName($name)
	{
		if ( $this->db != null && $name != null )
		{
			$sql = "SELECT
						groupID 
					FROM 
						groups 
					WHERE 
						groupName = '" . $this->db->escape($name) . "'";
			
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{
					return $this->getGroupById($ar["groupID"], false);		
				}
			}
		}
		return null;
	}
	
	/**
	 * creates a new group
	 *
	 * @return Integer The auto insert ID of the new group
	 */
	public function createGroup($name, $user)
	{
		if ($this->db != null && ! $this->doesGroupExist( $name ) && $name != null && strlen( $name ) > 0 && $user != null)
		{
			$sql = "INSERT 
						into 
					groups(groupName, groupCreateBy, groupDateTime) 
					values( '" . $this->db->escape($name) . "', '" . $this->db->escape($user->getUserName()) . "', NOW() )";
			if ( $this->db->executeUpdate($sql) == 1 )
				return $this->db->getInsertID();
						
		}
		return -1;
	}
	
	/**
	 * Adds a host to a group by Host ID
	 *
	 * @return boolean
	 */
	public function addHostToGroup($groupid, $hostid)
	{
		if ( $this->db != null && $groupid >= 0 && $hostid >= 0  && is_numeric( $groupid ) && is_numeric($hostid))
		{
			$sql = "INSERT 
						into 
					groupMembers(gmHostID, gmGroupID) 
					values( '" . $hostid . "', '" . $groupid . "' )";
			return ( $this->db->executeUpdate($sql) == 1 );			
		}
		return false;
	}
	
	/**
	 * Checks if a group exists with a given name
	 *
	 * @return boolean
	 */
	public function doesGroupExist( $name, $excludeid=-1)
	{
		if ( $this->db != null )
		{
			$sql = "SELECT 
						COUNT(*) AS c 
					FROM 
						groups
					WHERE 
						groupName = '" . $this->db->escape( $name ) . "' and
						groupID <> '" . $this->db->escape( $excludeid ) . "'";
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
					return $ar["c"] > 0;
			}
		}
		throw new Exception( _("Database Error!"));
	}
	
	/**
	 * Returns all groups known on the system without the host object populated
	 *
	 * @return Group[] An array of group objects without the hosts populated!
	 */
	public function getAllGroups()
	{
		$arGroups = array();
		if ( $this->db != null )
		{
			$sql = "SELECT 
					*,
					UNIX_TIMESTAMP(groupDateTime) as unxDate 
				FROM
					groups 
				ORDER BY
					groupName";
			
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{
					$arGroups[] = new Group($ar["groupID"], $ar["groupName"], $ar["groupDesc"], new Date($ar["unxDate"]), $ar["groupCreateBy"]);				
				}
			}	
		}
		return $arGroups;
	}
	
	/**
	 * 
	 *
	 * @return Group Group Object
	 */
	public function getGroupsWithMember( $hostid )
	{
		if ( $this->db != null && $hostid !== null && is_numeric( $hostid ) && $hostid >= 0 )
		{
			$sql = "SELECT 
						gmGroupID
					FROM
						groupMembers 
					WHERE
						gmHostID = '" . $hostid . "'";
			
			$arGroupIDs = array();
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{
					$arGroupIDs[] = $ar["gmGroupID"];
				}
				
				$arGroups = array();
				if ( $arGroupIDs != null )
				{
					for( $i = 0; $i < count( $arGroupIDs); $i++ )
					{
						$tmpGroup = $this->getGroupById( $arGroupIDs[$i], false );
						if ( $tmpGroup != null )
							$arGroups[] = $tmpGroup;
					}
				}
				return $arGroups;
			}
		}
		return null;
	}
	
	public function getGroupById( $groupid, $blPopulateHosts=true )
	{
		if ( $this->db != null && $groupid !== null && is_numeric( $groupid ) && $groupid >= 0 )
		{
			$sql = "SELECT 
					*, 
					UNIX_TIMESTAMP(groupDateTime) as unxDate 
				FROM 
					groups 
				WHERE
					groupID = '" . $this->db->escape($groupid) . "'";
					
			if ( $this->db->executeQuery($sql) )
			{
				$group = null;
				while( $ar = $this->db->getNext() )
					$group = new Group( $ar["groupID"], $ar["groupName"], $ar["groupDesc"], new Date($ar["unxDate"]), $ar["groupCreateBy"]);

				
				if ( $group != null && $blPopulateHosts && $this->hostManager != null )
				{
					// add members to group
					$sql = "SELECT 
								gmHostID
							FROM 
								groupMembers
							WHERE
								gmGroupID = '" . $this->db->escape( $group->getID() ) . "'
							ORDER BY
								gmID";
					
					$arHosts = array();
					if ( $this->db->executeQuery($sql) )
					{
						while( $ar = $this->db->getNext() )
							$arHosts[] = $ar["gmHostID"];
						
						
						if ( $arHosts != null )
						{
							for( $i = 0; $i < count( $arHosts); $i++ )
							{
								$h = $this->hostManager->getHostById( $arHosts[$i] );
								if ( $h != null )
									$group->addHost( $h );
							}
						}
					}
				}
				
				
				return $group;		
			}		
		}
		return null;
	}
	
	
	
	public function search( $crit, $sortingOpts )
	{
		$arResults = array();
		if ( $this->db != null && $crit != null )
		{
			
		}
		return $arResults;
	}
	
	// function either returns true or throws an exception
	public function updateGroup( $group )
	{
		throw new Exception( _("Not implemented") );
		if ( $this->db != null && $host != null )
		{
			if ( ( self::UPDATE_GENERAL & $flags ) == 1 )
			{
				if ( ! $this->updateGeneral( $host ) )
					return false;
			}
		
			return true;
		}
		return false;
	}
}
?>
