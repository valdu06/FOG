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
 
class HostManager
{
	const SORT_HOST_ASC = 1;
	const SORT_HOST_DESC = 2;
	const SORT_IP_ASC = 3;
	const SORT_IP_DESC = 4;
	const SORT_MAC_ASC = 5;
	const SORT_MAC_DESC = 6;	


	const UPDATE_GENERAL = 1;						// includes everything on the General page including additional MAC addresses
	const UPDATE_AD = 2;
	const UPDATE_PRINTERS = 4;

	const UPDATE_ALL = 7;

	private $db;

	public static function parseMacList( $stringlist )
	{
		if ( $stringlist != null && strlen( $stringlist ) > 0 )
		{
			$arParts = explode("|",$stringlist );
			$arMacs = array();
			for( $i = 0; $i < count( $arParts ); $i++ )
			{
				$part = trim($arParts[$i]);
				if ( $part != null && strlen( $part ) > 0 )
				{
					$tmpMac = new MACAddress( $part );
					if ( $tmpMac->isValid()  )
						$arMacs[] = $tmpMac;
				} 
			}
			return $arMacs;
		}
		return null;
	}

	function __construct( $db )
	{
		$this->db = $db;
	}
	
	public function getHostById( $hostid )
	{
		if ( $this->db != null && $hostid !== null && is_numeric( $hostid ) && $hostid >= 0 )
		{
			$sql = "SELECT 
					*, 
					UNIX_TIMESTAMP(hostCreateDate) as unxDate 
				FROM 
					hosts 
				WHERE
					hostID = '" . $this->db->escape($hostid) . "'";
					
			if ( $this->db->executeQuery($sql) )
			{
				$host = null;
				$imageID = -1;
				while( $ar = $this->db->getNext() )
				{
					//$ar[""]
					$host = new Host( $ar["hostID"], $ar["hostName"], $ar["hostDesc"], $ar["hostIP"], new Date($ar["unxDate"]), new MACAddress($ar["hostMAC"]), $ar["hostOS"]);
					$host->setPrinterManagementLevel( $ar["hostPrinterLevel"] );
					
					$useAD = $ar["hostUseAD"] == "1";
					$domain = null;
					$ou=null;
					$user=null;
					$pass=null;
					if ( $useAD )
					{
						$domain = $ar["hostADDomain"];
						$ou=$ar["hostADOU"];
						$user=$ar["hostADUser"];
						$pass=$ar["hostADPass"];
					}
					
					$host->setADInformation( $useAD, $domain, $ou, $user, $pass );
					$host->setKernel( $ar["hostKernel"] );
					$host->setKernelArgs($ar["hostKernelArgs"]);
					$host->setDiskDevice($ar["hostDevice"]);
					
					$imageID = $ar["hostImage"];
				}
				
				if ( $host != null )
				{
					// get additional mac address
					$sql = "SELECT 
							hmMAC
						FROM 
							hostMAC
						WHERE
							hmHostID = '" . $this->db->escape( $host->getID() ) . "'
						ORDER BY
							hmID";
						
					if ( $this->db->executeQuery($sql) )
					{
						while( $ar = $this->db->getNext() )
						{
							$tmpMac = new MACAddress( $ar['hmMAC'] );
							if ( $tmpMac->isValid() )
								$host->addAdditionalMacAddress( $tmpMac );
						}
					}
				}
				
				if ( $host != null && $imageID >= 0 )
				{
					$imageMan = new ImageManager($this->db);
					$host->setImage( $imageMan->getImageById( $imageID ) );
				}
				return $host;		
			}		
		}
		return null;
	}
	
	/**
	 * Returns all hosts known on the system
	 *
	 * @return Host[] An array of host objects
	 */
	public function getAllHosts($sortingOpts=self::SORT_HOST_ASC)
	{
		if ( $this->db != null )
		{
			$sql = "SELECT
					hostID 
				FROM 
					hosts " . $this->getSortingOptions( $sortingOpts );
			
			$arIDS = array();
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{
					$arIDS[] = $ar["hostID"];
				}
			}
			
			$arHosts = array();
			if ( $arIDS != null )
			{
				for( $i = 0; $i < count( $arIDS); $i++ )
				$arHosts[] = $this->getHostById($arIDS[$i]); 
			}
			return $arHosts;
		}
		return null;
	}

	public function addMACToPendingForHost( $host, $mac )
	{
		if ( $this->db != null && $host != null && $mac != null && $mac->isValid() )
		{
			// make sure it doesn't exist in the pending table
			$macs = $this->getPendingMacAddressesForHost( $host );
			if ( $macs != null )
			{
				for( $i = 0; $i < count( $macs ); $i++ )
				{
					$cMac = $macs[$i];
					if ( $cMac != null )
					{
						if ( $cMac->getMACWithColon() == $mac->getMACWithColon() )
							return false;
					}
				}
			}
		
			$sql = "INSERT INTO
					pendingMACS (pmAddress, pmHostID)
				VALUES
					('" . $this->db->escape((String)$mac) . "', '" . $this->db->escape($host->getID()) . "')";
			return $this->db->executeUpdate( $sql ) == 1;
		}
		return false;
	}

	public function deletePendingMacAddressForHost( $host, $mac )
	{
		if ( $this->db != null && $host != null && $mac != null && $mac->isValid() )
		{
			$sql = "DELETE
				FROM 
					pendingMACS 
				WHERE
					pmHostID = '" . $this->db->escape($host->getID()) . "' AND
					pmAddress = '" . $this->db->escape($mac->getMACWithColon()) . "'";

			return $this->db->executeUpdate($sql) > 0;
		
		}
		return false;
	}

	public function getAllHostsWithPendingMacs()
	{
		if ( $this->db != null )
		{
			$sql = "SELECT 
					pmHostID
				FROM 
					pendingMACS";
					
			$arHostsIds = array();	
			
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{
					$arHostsIds[] = $ar["pmHostID"];
				}	
			}
			
			$arHosts = array();
			for( $i = 0; $i < count($arHostsIds); $i++ )
			{
				$h = $this->getHostById( $arHostsIds[$i] );
				if ( $h != null )
				$arHosts[] = $h;
			}
			return $arHosts;
								
		}
		return null;
	}

	public function getPendingMacAddressesForHost( $host )
	{
		if ( $this->db != null && $host != null  )
		{
			$sql = "SELECT
					pmAddress 
				FROM 
					pendingMACS 
				WHERE
					pmHostID = '" . $this->db->escape($host->getID()) . "'
				GROUP BY
					pmAddress";
			if ( $this->db->executeQuery($sql) )
			{
				$arMacs = array();
				while( $ar = $this->db->getNext() )
				{
					 $mac = new MACAddress($ar["pmAddress"]);
					 if ( $mac->isValid() )
						 $arMacs[] = $mac;
				}	
				return $arMacs;	
			}					
		}
		return null;
	}

	public function getHostByMacAddress( $mac, $primaryOnly=false )
	{
		if ( $this->db != null && $mac != null && $mac->isValid() )
		{
			$sql = "SELECT
					hostID 
				FROM 
					hosts 
				WHERE
					hostMAC = '" . $this->db->escape($mac->getMACWithColon()) . "'";
			
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{
					return $this->getHostById($ar["hostID"]);
				}		
			}

			if ( ! $primaryOnly )
			{
				$sql = "SELECT
						hmHostID 
					FROM 
						hostMAC 
					WHERE
						hmMAC = '" . $this->db->escape($mac->getMACWithColon()) . "'";
					
				if ( $this->db->executeQuery($sql) )
				{
					while( $ar = $this->db->getNext() )
					{
						return $this->getHostById($ar["hmHostID"]);
					}		
				}
			}					
		}
		return null;
	}
	
	public function doesHostExistWithMac( $mac, $ignoringHostId=-1 )
	{
		$host = $this->getHostByMacAddress( $mac );
		if ( $host == null )
			return false;
		else
		{	
			if ( $ignoringHostId == -1 )
				return true;
			else
				return  $host->getID() != $ignoringHostId;
		} 
	}
	
	public function getHostByMacAddresses( $arMac )
	{
		if ( $arMac != null )
		{
			if ( is_array( $arMac ) )
			{
				$hostReturn = null;
				for( $i = 0; $i < count( $arMac ); $i++ )
				{
					if ( $arMac[$i] !== null && $arMac[$i]->isValid() )
					{
						$tmpHost = $this->getHostByMacAddress( $arMac[$i] );
						if ( $tmpHost != null )
						{
							if ( $hostReturn == null )
							{
								$hostReturn = $tmpHost;
							}
							else
							{
								if ( $hostReturn->getID() != $tmpHost->getID() )
									throw new Exception( _("Error multiple hosts returned for list of mac addresses!") );
							}
						}
					}
				}
				return $hostReturn;
			}
			else
			{
				return $this->getHostByMacAddress( $arMac );
			}
		}
		return null;
	}
	
	private function getSortingOptions( $sortingOpts )
	{
		$orderby = "";
		switch( $sortingOpts )
		{
			case self::SORT_HOST_ASC:
				$orderby = "ORDER BY hostName asc";
				break;
			case self::SORT_HOST_DESC:
				$orderby = "ORDER BY hostName desc";
				break;
			case self::SORT_IP_ASC:
				$orderby = "ORDER BY hostIP asc";
				break;
			case self::SORT_IP_DESC:
				$orderby = "ORDER BY hostIP desc";
				break;
			case self::SORT_MAC_ASC:
				$orderby = "ORDER BY hostMAC asc";
				break;
			case self::SORT_MAC_DESC:
				$orderby = "ORDER BY hostMAC desc";
				break;																									
		}
		return $orderby;
	}
	
	public function search( $crit, $sortingOpts=self::SORT_HOST_ASC )
	{
		$arResults = array();
		if ( $this->db != null && $crit != null )
		{
			$orderby = "";
			
			switch( $sortingOpts )
			{
				case self::SORT_HOST_ASC:
					$orderby = "ORDER BY hostName asc";
					break;
				case self::SORT_HOST_DESC:
					$orderby = "ORDER BY hostName desc";
					break;
				case self::SORT_IP_ASC:
					$orderby = "ORDER BY hostIP asc";
					break;
				case self::SORT_IP_DESC:
					$orderby = "ORDER BY hostIP desc";
					break;
				case self::SORT_MAC_ASC:
					$orderby = "ORDER BY hostMAC asc";
					break;
				case self::SORT_MAC_DESC:
					$orderby = "ORDER BY hostMAC desc";
					break;																									
			}
		
			$sql = "SELECT 
					hostID 
				FROM
					hosts 
					left outer join 
						(SELECT * FROM hostMAC WHERE hmMAC like '%" . $this->db->escape($crit) . "%') hostMAC
							on ( hmHostID = hostID ) 
					left outer join 
						inventory
							on ( iHostId = hostID ) 
				WHERE 
					hostName like '%" . $this->db->escape($crit) . "%' or 
					hostDesc like '%" . $this->db->escape($crit) . "%' or 
					hostIP like '%" . $this->db->escape($crit) . "%' or 
					hostMAC like '%" . $this->db->escape($crit) . "%' or 
					hmMAC like '%" . $this->db->escape($crit) . "%' or
					iSysSerial like '%" . $this->db->escape($crit) . "%' or 
					iPrimaryUser like '%" . $this->db->escape($crit) . "%' or
					iOtherTag like '%" . $this->db->escape($crit) . "%' or
					iOtherTag1 like '%" . $this->db->escape($crit) . "%' or
					iSysman like '%" . $this->db->escape($crit) . "%' or
					iSysproduct like '%" . $this->db->escape($crit) . "%' 
				GROUP BY 	
					hostID " . $this->getSortingOptions( $sortingOpts );
			
			if ( $this->db->executeQuery($sql) )
			{
				$arHostIds = array();
				while( $ar = $this->db->getNext() )
				{	
					$arHostIds[] = $ar["hostID"];
				}
				
				if ( $arHostIds != null )
				{
					for( $i = 0; $i < count( $arHostIds ); $i++ )
					{
						$intHID = $arHostIds[$i];
						if ( is_numeric( $intHID ) )
						{
							
							$tmpHost = $this->getHostById( $intHID  );
							if ( $tmpHost != null )
								$arResults[] = $tmpHost;
						}
					}
				}
			}
		}
		return $arResults;
	}
	
	public function isServiceModuleEnabledForHost( $host, $modKey )
	{
		if ( $this->db != null && $host != null && $modKey != null )
		{
			$sql = "SELECT 
					msState 
				FROM
					moduleStatusByHost
				WHERE
					msHostID = '" . $this->db->escape( $host->getID() ) . "' and
					msModuleID = '" . $this->db->escape( $modKey ) . "'";
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{
					if ( $ar["msState"] == "0" ) return false;
				}
				return true;
			}					
		}
		throw new Exception( _("Error looking up service status for host!") );
	}
	
	
	public function deleteHost( $id )
	{
		if ( $this->db == null )
			throw new Exception( _("Database connection is null.") );
		
		if ( $id == null )
			throw new Exception( _("Host ID is null.") );
			
		if ( ! is_numeric( $id ) || $id < 0 )
			throw new Exception( _("Invalid Host ID.") );
			
		// clean up potential orphans
		// Clean up printers
		$this->db->executeUpdate( "DELETE FROM printerAssoc WHERE paHostID = '" . $this->db->escape($id) . "'" );		
		// clean up inventory
		$this->db->executeUpdate( "DELETE FROM inventory WHERE iHostID = '" . $this->db->escape($id) . "'" );		
		// clean up pending mac addresses
		$this->db->executeUpdate( "DELETE FROM pendingMACS WHERE pmHostID = '" . $this->db->escape($id) . "'" );		
		// clean assoc macs
		$this->db->executeUpdate( "DELETE FROM hostMAC WHERE hmHostID = '" . $this->db->escape($id) . "'" );
		// clean up associated snapins
		$this->db->executeUpdate( "DELETE FROM snapinAssoc WHERE saHostID = '" . $this->db->escape($id) . "'" );
		
		
		// finally remove the host object
		return $this->db->executeUpdate( "DELETE FROM hosts WHERE hostID = '" . $this->db->escape($id) . "'" ) == 1;
	}
	
	// Adds a new host to the database
	public function addHost( $host, $user=null )
	{
		if ( $this->db == null )
			throw new Exception( _("Database connection is null.") );
		
		if ( $host == null )
			throw new Exception( _("Host is null.") );
			
		if ( $host->getMAC() == null || ! $host->getMAC()->isValid() )
			throw new Exception( _("MAC address is invalid.") );			
			
		if ( $this->doesHostExistWithMac( $host->getMAC() ) )
			throw new Exception( _("A host with this MAC address already exists.") );			
			
		if ( $host->isHostNameSafe() )
		{
			$sql = "insert into hosts(hostMAC, hostIP, hostName, hostDesc, hostCreateDate, hostImage, hostCreateBy, hostOS, hostUseAD, hostADDomain, hostADOU, hostADUser, hostADPass, hostKernelArgs, hostKernel, hostDevice) 
					  values('" . $this->db->escape($host->getMAC()->getMACWithColon() ) . "',
					  	 '" . $this->db->escape($host->getIPAddress() ) . "', 
					  	 '" . $this->db->escape($host->getHostname() ) . "', 
					  	 '" . $this->db->escape($host->getDescription() ) . "', 
					  	 NOW(), 
					  	 '" . $this->db->escape($host->getImage() != null ? $host->getImage()->getID() : '' ) . "', 
					  	 '" . $this->db->escape($user != null ? $user->getUserName() : '' ) . "', 
					  	 '" . $this->db->escape($host->getOS()) . "', 
					  	 '" . $this->db->escape($host->usesAD() ? '1' : '0') . "', 
					  	 '" . $this->db->escape($host->getDomain()) . "', 
					  	 '" . $this->db->escape($host->getOU()) . "', 
					  	 '" . $this->db->escape($host->getUser()) . "', 
					  	 '" . $this->db->escape($host->getPassword()) . "', 
					  	 '" . $this->db->escape($host->getKernelArgs()) . "', 
					  	 '" . $this->db->escape($host->getKernel()) . "', 
					  	 '" . $this->db->escape($host->getDiskDevice()) . "' )";
			return $this->db->executeUpdate( $sql ) == 1;
		}
		else
			throw new Exception( _("Invalid hostname.") );			
		return	false;
	}	

	// function either returns true or throws an exception
	public function updateHost( $host, $flags )
	{
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
	
	private function updateGeneral( $host )
	{
		if ( $this->db != null )
		{
			if (  $host != null ) 
			{
				if ( $host->getMAC() == null )
					throw new Exception( _("MAC address object is null.") );
					
				if ( ! $host->getMAC()->isValid() )
					throw new Exception( _("MAC address is invalid.") );
					
				if ( $host->getID() < 0 )
					throw new Exception( _("Host ID is invalid.") );
					
				if ( $host->getHostname() == null || strlen($host->getHostname()) == 0 )
					throw new Exception( _("Hostname is invalid.") );
					
				if ( $host->getOS() == null || $host->getOS() == -1 )
					throw new Exception( _("Operating System ID is invalid.") );
					

					
				if ( ! $this->doesHostExistWithMac( $host->getMAC(), $host->getID() ) )
				{
					
					$imageID = -1;
					if ( $host->getImage() != null )
						$imageID = $host->getImage()->getID();
					$sql = "UPDATE 
							hosts 
						SET 
							hostKernel = '" . $this->db->escape( $host->getKernel() ) . "', 
							hostDevice = '" . $this->db->escape( $host->getDiskDevice() ) . "', 
							hostKernelArgs = '" . $this->db->escape( $host->getKernelArgs() ) . "', 
							hostMAC = '" . $this->db->escape( $host->getMAC()->getMACWithColon() ) . "', 
							hostIP = '" . $this->db->escape( $host->getIPAddress() ) . "', 
							hostOS = '" . $this->db->escape( $host->getOS() ) . "', 
							hostName = '" . $this->db->escape( $host->getHostname() ) . "', 
							hostDesc = '" . $this->db->escape( $host->getDescription() ) . "', 
							hostImage = '" . $this->db->escape( $imageID ) . "' 
						WHERE 
							hostID = '" . $this->db->escape( $host->getID() ) . "'";
					
					$this->db->executeUpdate( $sql );
					
					// update the additional mac addresses
					$sql = "DELETE FROM hostMAC where hmHostID = '" . $this->db->escape( $host->getID() ) . "'";

					$this->db->executeUpdate( $sql );
					
					$addMacs = $host->getAdditionalMacAddresses();
					if ( $addMacs != null )
					{
						$exception = null;
						for( $i = 0; $i < count( $addMacs );$i++ )
						{
							$curMac = $addMacs[$i];
							if ( $curMac != null && $curMac->isValid() )
							{
								if ( ! $this->doesHostExistWithMac( $curMac ) )
								{
									$sql = "INSERT INTO hostMAC (hmHostID, hmMAC) VALUES('" . $this->db->escape( $host->getID() ) . "','" . $this->db->escape( $curMac->getMACWithColon() ) . "')";
									if ( $this->db->executeUpdate( $sql ) != 1 )
										$exception = new Exception( "Error adding additional MAC address: " . $curMac->getMACWithColon() );
								}
								else
									$exception = new Exception( _("MAC address").": " . $curMac->getMACWithColon() . " "._("is already registered with FOG.") );
							}
						}
						
						if ( $exception != null )
							throw $exception; 
					}
					
					return true;
				}
				else
					throw new Exception( _("Another host exists with that MAC address.") );
			}
			else
				throw new Exception( _("Host object is null.") );
		}
		else
			throw new Exception( _("Database object is null.") );
		
		return false;
	}
	
}
?>
