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
 
class Host
{
	const PRINTER_MANAGEMENT_UNKNOWN = -1;
	const PRINTER_MANAGEMENT_NO_MANAGEMENT = 0;
	const PRINTER_MANAGEMENT_ADD = 1;	
	const PRINTER_MANAGEMENT_ADDREMOVE = 2;	

	const OS_UNKNOWN = -1;
	const OS_WIN2000XP = 1;
	const OS_WINVISTA = 2;
	const OS_WIN98 = 3;
	const OS_WIN7 = 5;
	const OS_WINOTHER = 4;
	const OS_LINUX = 50;
	const OS_OTHER = 99;
	
	private $intID, $intOS, $intPrinterLevel;
	private $strHostname, $strHostDescription, $strIP, $strADDomain, $strADOU, $strADUser, $strADPass, $strKernel, $strKernelArgs, $strDevice; 
	private $image;
	private $date;							// Date Object
	private $mac;	
	private $addMacs;							
	private $blUseAD;
	
	function __construct( $id=null, $hostname=null, $hostdesc=null, $hostip=null, $createdate=null, $mac=null, $os=null )
	{
		$this->intID 					= $id;
		$this->intOS 					= $os;
		$this->intPrinterLevel 				= self::PRINTER_MANAGEMENT_UNKNOWN;
		$this->strHostname 				= $hostname;
		$this->strHostDescription 			= $hostdesc;
		$this->strIP					= $hostip;
		$this->strADDomain				= null;
		$this->strADOU					= null;
		$this->strADUser				= null;
		$this->strADPass				= null;
		$this->strKernel				= null;
		$this->strKernelArgs				= null;
		$this->strDevice 				= null;
		$this->image 					= null;
		$this->date 					= $createdate;
		$this->mac					= $mac;								
		$this->blUseAD 					= false;
		$this->addMacs 					= array();
	}

	public function clearAdditionalMacAddresses()
	{
		$this->addMacs = array();
	}

	public function addAdditionalMacAddress( $mac )			
	{ 
		if ( $mac != null && $mac->isValid() )
		{
			if ( ! $this->hasMac( $mac ) )
			{
				$this->addMacs[] = $mac; 
				return true;
			}
		}
		return false;
	}
	
	public function getAdditionalMacAddresses()
	{
		return $this->addMacs;
	}
	
	public function getAdditionalMacAddressCount()
	{
		if ( $this->getAdditionalMacAddresses() != null )
			return count( $this->getAdditionalMacAddresses() );
		
		return 0;
	}
	
	public function hasMac( $mac )
	{
		if ( $mac != null && $mac->isValid() && $this->mac != null && $this->mac->isValid() )
		{
			if ( $this->mac->getMACWithColon() == $mac->getMACWithColon() )
			{
				return true;
			}
			
			if ( $this->addMacs != null )
			{
				for( $i = 0; $i < count( $this->addMacs ); $i++ )
				{
					$curMac = $this->addMacs[$i];
					if ( $curMac != null && $curMac->isValid() )
					{
						if ( $curMac->getMACWithColon() == $mac->getMACWithColon() )
							return true;
					}
				}
			}
		}
		return false;
	}
	

	public function setID( $id )				{ $this->intID = $id; }
	public function getID()					{ return $this->intID; 	}
	
	public function setHostname( $hn )			{ $this->strHostname = $hn; }
	public function getHostname( )				{ return $this->strHostname; }
	
	public function isHostNameSafe()			
	{ 
		return ( ereg( "^[0-9a-zA-Z_\-]*$", $this->strHostname ) && strlen($this->strHostname ) > 0 && strlen( $this->strHostname ) <= 15  ); 
	}
	
	public function setDescription( $desc )			{ $this->strHostDescription = $desc; }
	public function getDescription( )			{ return $this->strHostDescription; }
	
	public function setMAC( $m )				{ $this->mac = $m; }
	public function getMAC( )				{ return $this->mac; }
	
	public function setOS( $os )				{ $this->intOS = $os; }
	public function getOS( )				{ return $this->intOS; }

	public function getOSName()
	{
		switch( $this->getOS() )
		{
			case self::OS_UNKNOWN:
				return _("Unknown");
			case self::OS_WIN2000XP;
				return _("Windows 2000/XP");
			case self::OS_WINVISTA:
				return _("Windows Vista");
			case self::OS_WIN98:
				return _("Windows 98");
			case self::OS_WIN7:
				return _("Windows 7");
			case self::OS_WINOTHER:
				return _("Windows (other)");
			case self::OS_LINUX:
				return _("Linux");
			case self::OS_OTHER:
				return _("Other");
			default:
				return _("Unknown");
				
		}
	}
	
	public function setPrinterManagementLevel( $level ) 	{ $this->intPrinterLevel = $level; }
	public function getPrinterManagementLevel(  ) 		{ return $this->intPrinterLevel; }
	
	public function setIPAddress( $ip )			{ $this->strIP = $ip; }
	public function getIPAddress( )				{ return $this->strIP; }
	
	public function setADInformation( $useAD, $domain=null, $ou=null, $user=null, $pass=null )
	{
		$this->blUseAD					= $useAD;
		$this->strADDomain				= $domain;
		$this->strADOU					= $ou;
		$this->strADUser				= $user;
		$this->strADPass				= $pass;
	}
	
	function setADUsage( $bl )
	{
		$this->blUseAD = $bl;	
	}
	
	function setupAD( $domain, $ou, $user, $pass )
	{

		$this->strADDomain = $domain;
		$this->strADOU = $ou;
		$this->strADUser = $user;
		$this->strADPass = $pass;			
	}
	
	public function isReadyToImage()
	{
		if ( $this->getImage() != null && $this->getImage()->getStorageGroup() != null )
		{
			return true;
		}
		return false;
	}
	
	public function usesAD()				{ return $this->blUseAD; }
	public function getDomain()				{ return $this->strADDomain; }
	public function getOU()					{ return $this->strADOU; }
	public function getUser()				{ return $this->strADUser; }
	public function getPassword()				{ return $this->strADPass; }
	
	public function setKernel( $kernel )			{ $this->strKernel = $kernel; }
	public function getKernel( )				{ return $this->strKernel; }
	
	public function setKernelArgs( $args )			{ $this->strKernelArgs = $args; }
	public function getKernelArgs(  )			{ return $this->strKernelArgs; }
	
	public function setDiskDevice( $hd )			{ $this->strDevice = $hd; }
	public function getDiskDevice(  )			{ return $this->strDevice; }
	
	public function setImage ( $img )			{ $this->image = $img; }
	public function getImage()				{ return $this->image; }
}
?>
