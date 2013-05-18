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
class FOGCore
{
	const TASK_UNICAST_SEND 	= 'd';
	const TASK_UNICAST_UPLOAD 	= 'u';
	const TASK_WIPE		 	= 'w';
	const TASK_DEBUG	 	= 'x';
	const TASK_MEMTEST	 	= 'm';
	const TASK_TESTDISK	 	= 't';
	const TASK_PHOTOREC	 	= 'r';
	const TASK_MULTICAST 		= 'c';
	const TASK_VIRUSSCAN	 	= 'v';
	const TASK_INVENTORY	 	= 'i';
	const TASK_PASSWORD_RESET 	= 'J';
	const TASK_ALL_SNAPINS	 	= 's';
	const TASK_SINGLE_SNAPIN 	= 'l';
	const TASK_WAKE_ON_LAN	 	= 'o';

	private $db;
	
	function __construct( $conn )
	{
		$this->db = $conn;
	}
	
	private function cleanOldUnrunScheduledTasks()
	{
		if ( $this->db != null )
		{
			$sql = "UPDATE 
					scheduledTasks 
				SET
					stActive = '0'
				WHERE 
					stType = '" . ScheduledTask::TASK_TYPE_SINGLE . "' and 
					stDateTime < (UNIX_TIMESTAMP() - " . Timer::TASK_SINGLE_FLEXTIME . ") and 
					stActive = '1'";
			
			mysql_query( $sql, $this->db ) or die( mysql_error($this->db) );
		}
	}
	
	function stopScheduledTask( $task )
	{
		if ( $task != null && $this->db != null )
		{
			if ( is_numeric( $task->getID() ) )
			{
				$sql = "UPDATE 
						scheduledTasks 
					SET
						stActive = '0'
					WHERE 
						stID = '" . $task->getID() . "'";
			
				if ( mysql_query( $sql, $this->db ) )
					return true;
			}
		}
		return false;
	}
	
	function getScheduledTasksByStorageGroupID( $groupid, $blIgnoreNonImageReady=false )
	{
		$arTasks = array();
		if ( $this->db != null && ((is_numeric( $groupid ) && $groupid >= 0) || $groupid = "%" ))
		{
			$this->cleanOldUnrunScheduledTasks();
			
			$sql = "SELECT 
					* 
				FROM 
					scheduledTasks 
				WHERE 
					stActive = '1'";
				

			$res = mysql_query( $sql, $this->db ) or die( mysql_error($this->db) );
			//echo mysql_num_rows( $res ) ;
			while( $ar = mysql_fetch_array( $res ) )
			{
				$timer = null;
				if ( $ar["stType"] == ScheduledTask::TASK_TYPE_SINGLE )
				{
					$timer = new Timer( $ar["stDateTime"] );
 				}
				else if ($ar["stType"] == ScheduledTask::TASK_TYPE_CRON )
				{
					$timer = new Timer( $ar["stMinute"], $ar["stHour"], $ar["stDOM"], $ar["stMonth"], $ar["stDOW"] );				
				}
				
				if ( $timer != null )
				{
					$group=null;
					$host=null;
					
					if ( $ar["stIsGroup"] == "0" )
						$host=$this->getHostById($ar["stGroupHostID"]);
					else if ( $ar["stIsGroup"] == "1" )
						$group = $this->getGroupById($ar["stGroupHostID"]);
					
					if ( $group != null || $host != null )
					{
						if ( $host != null )
						{
							if ( ($host->isReadyToImage() || $blIgnoreNonImageReady) && ( $groupid == "%" || $host->getImage()->getStorageGroup()->getID() == $groupid  ) )
							{
								$task = new ScheduledTask( $host, $group, $timer, $ar["stTaskType"], $ar["stID"] );
								$task->setShutdownAfterTask( $ar["stShutDown"] == 1 );
								$task->setOther1( $ar["stOther1"] );
								$task->setOther2( $ar["stOther2"] );
								$task->setOther3( $ar["stOther3"] );
								$task->setOther4( $ar["stOther4"] );
								$task->setOther5( $ar["stOther5"] );
								$arTasks[] = $task;
							}
						}
						else if ( $group != null )
						{
							if ( $group->getCount() > 0  )
							{
								$arRm = array();
								$hosts = $group->getMembers();
								for( $i = 0; $i < count($hosts); $i++ )
								{
									 if ( $hosts[$i] != null )
									 {
									 	$h = $hosts[$i];
									 	if ( ! ($h->isReadyToImage() &&  $h->getImage()->getStorageGroup()->getID() == $groupid ) )
									 	{
									 		$arRm[] = $h;
									 	}	
									 }
								}
								
								//echo ( "Before: " . $group->getCount() );
								for( $i = 0; $i < count($arRm); $i++ )
								{
									$group->removeMember( $arRm[$i] );
								}
								//echo ( "After: " . $group->getCount() );
								
								$task = new ScheduledTask( $host, $group, $timer, $ar["stTaskType"], $ar["stID"] );
								$task->setShutdownAfterTask( $ar["stShutDown"] == 1 );
								$task->setOther1( $ar["stOther1"] );
								$task->setOther2( $ar["stOther2"] );
								$task->setOther3( $ar["stOther3"] );
								$task->setOther4( $ar["stOther4"] );
								$task->setOther5( $ar["stOther5"] );
								$arTasks[] = $task;								
							}
						}
					}				
				}
			}

			
		}
		return $arTasks;
	}
	
	function getGroupById( $id )
	{
		if ( $this->db != null && is_numeric( $id ) && $id >= 0 )
		{
			$sql = "SELECT 
					groupID,
					groupName,
					groupDesc, 
					gmHostID as hostid 
				FROM 
					groups
					inner join groupMembers on ( groups.groupID = groupMembers.gmGroupID )
				WHERE 
					groupID = $id";		
			$res = mysql_query( $sql, $this->db ) or die( mysql_error() );		

			$group = null;
			while( $ar = mysql_fetch_array( $res ) )
			{
				if ( $group == null )
				{
					$group = new Group($ar["groupID"], $ar["groupName"], $ar["groupDesc"] );
				}
				
				$hid = $ar["hostid"];
				if ( $hid !== null && is_numeric( $hid ) )
				{
					$host = $this->getHostById( $hid );
					if ( $host != null )
						$group->addMember( $host );
				}
			}		
			return $group;	
		}
		return null;
	}
	
	function getHostById( $id )
	{
		if ( $this->db != null && is_numeric( $id ) && $id >= 0 )
		{
			$sql = "SELECT 
					* 
				FROM 
					hosts 
				WHERE 
					hostID = '" . $id . "'";
			$res = mysql_query( $sql, $this->db ) or die( mysql_error() );
			while( $ar = mysql_fetch_array( $res ) )
			{
				$host = new Host( $ar["hostID"], $ar["hostName"], $ar["hostDesc"], $ar["hostIP"], $ar["hostCreateDate"], new MACAddress($ar["hostMAC"]), $ar["hostOS"] );
				$host->setPrinterManagementLevel( $ar["hostPrinterLevel"] );
				$host->setADUsage( $ar["hostUseAD"] == "1" );
				$host->setupAD($ar["hostADDomain"], $ar["hostADOU"], $ar["hostADUser"], $ar["hostADPass"]);
				
				$host->setImage( $this->getImageById( $ar["hostImage"] ) );
				
				return $host;
			}
		}
		return null;
	}
	
	function getImageById( $id )
	{
		if ( $this->db != null && is_numeric( $id ) && $id >= 0 )
		{
			$sql = "SELECT 
					* 
				FROM 
					images 
				WHERE 
					imageID = '" . $id . "'";
					
			$res = mysql_query( $sql, $this->db ) or die( mysql_error($this->db) );
			while( $ar = mysql_fetch_array( $res ) ) 
			{
				$image = new Image( $ar["imageID"], $ar["imageName"], $ar["imageDesc"], $ar["imagePath"], $ar["imageDateTime"], $ar["imageCreateBy"], $ar["imageDD"] );
				$storageGroupId = $ar["imageNFSGroupID"];
				$image->setStorageGroup( $this->getStorageGroupById( $storageGroupId ) );
				return $image;								
			}
		}
		return null;
	}
	
	private function populateStorageNodes( $storagegroup )
	{
		if ( $this->db != null && $storagegroup != null )
		{
			$sql = "SELECT 
					* 
				FROM 
					nfsGroupMembers 
				WHERE 
					ngmID = '" . $storagegroup->getID() . "'";
			//echo $sql;
			$res = mysql_query( $sql, $this->db ) or die( mysql_error( $this->db ) );
			
			$nodes = array();
			//echo "dd";
			while( $ar = mysql_fetch_array( $res ) )
			{
				//echo $ar["ngmIsMaster"];
				$sn = new StorageNode($ar["ngmID"], $ar["ngmMemberName"], $ar["ngmMemberDescription"], $ar["ngmIsMasterNode"] == "1", $ar["ngmIsEnabled"] == "1", $ar["ngmRootPath"], $ar["ngmHostname"], $ar["ngmMaxClients"], $ar["ngmUser"], $ar["ngmPass"]);
				$storagegroup->addMember( $sn );
			}
			return true;
		}
		return false;
	}
	
	function getStorageGroupById( $id )
	{
		if ( $this->db != null && is_numeric( $id ) && $id >= 0 )
		{
			$sql = "SELECT 
					* 
				FROM 
					nfsGroups 
				WHERE 
					ngID = '" . $id . "'";
			$res = mysql_query( $sql, $this->db ) or die( mysql_error($this->db) );
			while( $ar = mysql_fetch_array( $res ) )
			{
				$sg = new StorageGroup($ar["ngID"], $ar["ngName"], $ar["ngDesc"]);				
				$this->populateStorageNodes( $sg );				
				return $sg;
			}
		}
		return null;		
	}
}
?>
