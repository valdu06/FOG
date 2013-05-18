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
 
class ClientServiceManager
{
	private $db;
	
	function __construct( $db )
	{
		$this->db = $db;
	}

	public function getGreenFOGActions()
	{
		if ( $this->db != null )
		{
			$sql = "SELECT 
					gfHour, 
					gfMin, 
					gfAction 
				FROM 
					greenFog";


			$arEntries = array();
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{
					$arEntries[] = new GreenFOG($ar["gfHour"], $ar["gfMin"], $ar["gfAction"]);
				}
			}	
			return $arEntries;				
		}
		return null;
	}

	public function completeSnapinTask( $snapintask, $exitCode, $exitDesc )
	{
		if ( $this->db != null && $snapintask != null && $snapintask->getId() >= 0 )
		{
			$exitcode = $this->db->escape( $exitCode );
			$exitdesc = base64_decode($this->db->escape( $exitDesc ) );
			$sql = "UPDATE 
					snapinTasks 
				SET 
					stState = '2', 
					stReturnCode = '" . $exitcode . "', 
					stReturnDetails = '" . $exitdesc . "' 
				WHERE 
					stID = '" . $snapintask->getId() . "'";

			return $this->db->executeUpdate($sql) == 1;
		}
		return false;
	}

	public function checkInSnapinTask( $snapin )
	{
		if ( $this->db != null && $snapin != null && $snapin->getId() >= 0 )
		{
			$sql = "UPDATE 
					snapinTasks 
				SET 
					stState = '1', 
					stCheckinDate = NOW() 
				WHERE 
					stID = '" . $snapin->getId() . "'";
			return $this->db->executeUpdate($sql) == 1;
		}
		return false;
	}

	public function getSnapinTaskById( $id )
	{
		if ( $this->db != null && $id >= 0 )
		{
			$sql = "SELECT 
			 		snapins.*,
			 		snapinJobs.*,
			 		snapinTasks.*,
			 		UNIX_TIMESTAMP(snapins.sCreateDate) as snapincreatedate,
			 		UNIX_TIMESTAMP(snapinTasks.stCheckinDate) as cin,
			 		UNIX_TIMESTAMP(snapinTasks.stCompleteDate) as comp,
			 		UNIX_TIMESTAMP(snapinJobs.sjCreateTime) as sj
			 	FROM 
					snapinTasks
					inner join snapinJobs on ( snapinTasks.stJobID = snapinJobs.sjID )
					inner join snapins on ( snapins.sID = snapinTasks.stSnapinID )
				WHERE
					stID = '" . $this->db->escape($id) . "'";
			$entries = null;
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{	
					$snapin = new Snapin($ar["sID"], $ar["sName"], $ar["sDesc"], $ar["sFilePath"], $ar["sArgs"], new Date($ar["snapincreatedate"]), $ar["sCreator"], $ar["sReboot"]=="1", $ar["sRunWith"], $ar["sRunWithArgs"]);				
					$entries = new SnapinTask( $ar["stID"], $ar["stJobID"], $ar["sjHostID"], $ar["stState"], new Date($ar["cin"]), new Date($ar["comp"]), new Date($ar["sj"]), $snapin, $ar["stReturnCode"], $ar["stReturnDetails"]  );
				}
			}
			return $entries;		
		}
		return null;
	}

	public function addLoginEntry( $loginEntry )
	{
		if ( $this->db != null && $loginEntry != null && $loginEntry->isComplete() )
		{
			$sql = "INSERT INTO 
					userTracking(utHostID, utUserName, utAction, utDateTime, utDesc, utDate)
					values( '" . $this->db->escape($loginEntry->getHostId()) . "', '" . $this->db->escape($loginEntry->getUsername()) . "', '" . $this->db->escape( $loginEntry->getAction() )  . "', FROM_UNIXTIME(" . $loginEntry->getDate()->getLong() . "),  '" . $loginEntry->getDescription() . "', DATE('" . $loginEntry->getDate()->toString( "Y-m-d" ) . "') )";	

			return $this->db->executeUpdate( $sql ) == 1;	
		}
		return false;
	}

	public function getAllPrintersForHost( $host )
	{
		if ( $this->db != null && $host != null && $host->getID() >= 0 )
		{
			$sql = "SELECT 
					* 
			 	FROM 
					printerAssoc
					inner join printers on ( printerAssoc.paPrinterID = printers.pID )
				WHERE
					paHostID = '" . $this->db->escape( $host->getID() ) . "'";

			$arPrinters = array();
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{
					$printer = new Printer( $ar["pID"], $ar["pAlias"], $ar["pModel"], $ar["pPort"], $ar["pDefFile"], $ar["pIP"], $ar["pConfig"] );
					$printer->setDefault( $ar["paIsDefault"] == "1" );
					$arPrinters[] = $printer;
				}
			}
			return $arPrinters;
		}
		return null;
	}

	public function getAutoLogOutTimeForHost( $host )
	{
		if ( $this->db != null && $host != null && $host->getID() >= 0 )
		{
			$sql = "SELECT
					haloTime 
				FROM
					hostAutoLogOut
				WHERE
					haloHostID = '" . $this->db->escape( $host->getID() ) . "'";
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{					
					return $ar["haloTime"];
				}
			}
		}
		return -1;
	}

	public function getAllActiveSnapinsForHost( $host )
	{
		if ( $this->db != null && $host != null && $host->getID() >= 0 )
		{
			$sql = "SELECT 
			 		stID
			 	FROM 
					snapinTasks
					inner join snapinJobs on ( snapinTasks.stJobID = snapinJobs.sjID )
					inner join hosts on ( snapinJobs.sjHostID = hosts.hostID )
					inner join snapins on ( snapins.sID = snapinTasks.stSnapinID )
				WHERE
					stState in ( '0', '1' ) and
					sjHostID = '" . $host->getID() . "'";
			$arEntries = array();
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{	
					$arEntries[] = $ar["stID"];
				}
			}

			$arRet = array();
			if ( $arEntries != null )
			{
				for( $i = 0; $i < count( $arEntries ); $i++ )
				{
					if ( $arEntries[$i] != null )
					{
						$tmp = $this->getSnapinTaskById( $arEntries[$i] );					
						if ( $tmp != null )
							$arRet[] = $tmp;
					}
				}
			}
			
			return $arRet;
			
		}
		return null;
	}

	public function getScreenResolutionSettingsForHost( $host )
	{
		if ( $this->db != null && $host != null && $host->getID() >= 0 )
		{
			$sql = "SELECT
					* 
				FROM
					hostScreenSettings
				WHERE
					hssHostID = '" . $host->getID() . "'";
			if ( $this->db->executeQuery($sql) )
			{
				while( $ar = $this->db->getNext() )
				{
					return new ScreenResolution($ar["hssWidth"], $ar["hssHeight"], $ar["hssRefresh"]);
				}
			}
		}
		return null;
	}

}
?>
