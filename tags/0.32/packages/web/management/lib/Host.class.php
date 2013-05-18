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
class Host implements Imageable
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

	private $image;
	private $intID;
	private $strHostName;
	private $strHostDesc;
	private $strHostIP;
	private $strCreateDate;
	private $arMAC;
	private $intOSID;
	private $blUseAD;
	private $strADDomain, $strADOU, $strADUser, $strADPass;
	private $intPrinterLevel;
	private $strKernelArgs;
	private $strKernel;
	private $strHDDevice;
	
	function __construct( $id=null, $hostname=null, $hostdesc=null, $hostip=null, $createdate=null, $mac=null, $os=null )
	{
		$this->setImage( null );
		$this->intID = $id;
		$this->strHostName = $hostname;
		$this->strHostDesc = $hostdesc;
		$this->strHostIP = $hostip;
		$this->strCreateDate = $createdate;
		$this->arMAC = array();
		$this->addMACAddress( $mac );
		$this->intOSID = $os;
		$this->blUseAD = false;
		$this->strADDomain = null;
		$this->strADOU = null;
		$this->strADUser = null;
		$this->strADPass = null;
		$this->intPrinterLevel = self::PRINTER_MANAGEMENT_UNKNOWN;
		$this->strKernelArgs = null;
		$this->strKernel = null;
		$this->strHDDevice = null;
	}
	
	function setPrinterManagementLevel( $level ) { $this->intPrinterLevel = $level; }
	
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
	
	function useAD() { return $this->blUseAD; }
	function getADDomain() { return $this->strADDomain; }
	function getADOU() { return $this->strADOU; }
	function getADUser() { return $this->strADUser; }	
	function getADPass() { return $this->strADPass; }	
	
	function setKernel( $kernel ) { $this->strKernel = $kernel; }	
	function getKernel() { return $this->strKernel; }
	
	function setKernelArgs( $args ) { $this->strKernelArgs = $args; }
	function getKernelArgs() { return $this->strKernelArgs; }
	
	function setImage( $objimg ) { $this->image = $objimg;}
	function getImage() { return $this->image; }
	
	function getHostID() { return $this->intID; }
	
	function getHostName() { return $this->strHostName; }
	
	function getOSID() { return $this->intOSID; }
	
	function addMACAddress($mac) 
	{ 
		if ( $mac != null && ! $this->doesMACAddressExist( $mac ) )
		{
			$this->arMAC[] = $mac;
		}
	}
	
	function getAllMACAddresses()
	{
		return $this->arMAC;
	}
	
	function getPrimaryMACAddress()
	{
		$ar = $this->getAllMACAddresses();
		if ( $ar != null )
		{
			if ( count( $ar ) > 0 )
				return $ar[0];
		}
		return null;
	}
	
	private function doesMACAddressExist( $mac )
	{
		if ( $mac != null )
		{
			if ( $this->arMAC != null )
			{
				for( $i = 0; $i < count( $this->arMAC ); $i++ )
				{
					$m = $this->arMAC[$i];
					if ( $m != null )
					{
						if ( $m->getMACWithColon() == $mac->getMACWithColon() )
							return true;
					}
				}
			}
		}
		return false;
	}
	
	public function isReadyToImage()
	{
		if ( $this->getImage() != null && $this->getImage()->getStorageGroup() != null )
		{
			return true;
		}
		return false;
	}
	
	//private function createImagePackageMulticast($conn, $port, &$reason, $deploySnapins=true, $shutdown="" )
	//{
	
	//}
	
	public function startTask($conn, $tasktype, $blShutdown, $args1=null, $args2=null, $args3=null, $args4=null, $args5=null, &$reason)
	{
		$reason = "";
		if ( $conn != null  )
		{
			switch( strtoupper($tasktype) )
			{
				/*
				 *    Unicast Send
				 */
				
				
				case strtoupper(FOGCore::TASK_UNICAST_SEND):
					if ( $this->getImage() != null  )
					{
						if ( $this->getImage()->getPath() != null && strlen(trim($this->getImage()->getPath())) > 0 )
						{
							$storageGroup = $this->getImage()->getStorageGroup();
							if ( $storageGroup != null )
							{
								if ( $storageGroup->getMembers() != null && count( $storageGroup->getMembers() ) > 0  )
								{
									$masterNode = $storageGroup->getMasterNode();
									if ( $masterNode != null )
									{
										$mac = $this->getPrimaryMACAddress();							
										if ( $mac != null )
										{
											if ( $mac->isValid( ) )
											{
												$imgType = "imgType=n";
												if ( $this->getImage()->getImageType() == Image::IMAGE_TYPE_DD )
													$imgType = "imgType=dd";
												else if ( $this->getImage()->getImageType() == Image::IMAGE_TYPE_MULTIPARTITION_SINGLE_DISK )
													$imgType = "imgType=mps";
												else if ( $this->getImage()->getImageType() == Image::IMAGE_TYPE_MULTIPARTITION_MULTIDISK )
													$imgType = "imgType=mpa";												
												else
												{
													if ( $this->getOSID() == Host::OS_OTHER )
													{
														$reason = "Invalid OS type, unable to determine MBR.";
														return false;
													}

													if ( strlen( trim($this->getOSID()) ) == 0 || $this->getOSID() == Host::OS_UNKNOWN )
													{
														$reason = "Invalid OS type, you must specify an OS Type to image.";
														return false;
													}

													if ( trim($this->getOSID()) != Host::OS_WIN2000XP && trim($this->getOSID()) != Host::OS_WINVISTA && trim($this->getOSID()) != Host::OS_WIN7 )
													{
														$reason = "Unsupported OS detected in host!";
														return false;
													}				
												}
											
												$keymapapp = "";
												$keymap = getSetting( $conn, "FOG_KEYMAP" );
												if ( $keymap != null && $keymap != "" )
													$keymapapp = "keymap=$keymap";																							

												$strKern = getSetting($conn, "FOG_TFTP_PXE_KERNEL" );
												if ( $this->getKernel() != "" && $this->getKernel() != null )
													$strKern = $this->getKernel();		
													
												$output = "# Created by FOG Imaging System\n\n
															  DEFAULT fog\n
															  LABEL fog\n
															  kernel " . $strKern . "\n
															  append initrd=" . getSetting( $conn, "FOG_PXE_BOOT_IMAGE" ) . "  root=/dev/ram0 rw ramdisk_size=" . getSetting( $conn, "FOG_KERNEL_RAMDISK_SIZE" ) . " ip=dhcp dns=" . getSetting($conn, "FOG_PXE_IMAGE_DNSADDRESS" ) . " type=down img=" . $this->getImage()->getPath() . " mac=" . $mac->getMACWithColon() . " ftp=" . sloppyNameLookup(getSetting( $conn, "FOG_TFTP_HOST" )) . " storage=" . $masterNode->getHostIP() . ":" . $masterNode->getRoot() . " web=" . sloppyNameLookup(getSetting($conn, "FOG_WEB_HOST")) . getSetting( $conn, "FOG_WEB_ROOT" ) . " osid=" . $this->getOSID() . " $imgType $keymapapp shutdown=" . ( $blShutdown ? "on" : " " ) . " loglevel=4 "  . getSetting( $conn, "FOG_KERNEL_ARGS" ) . " " . $this->getKernelArgs();

												$tmp = createPXEFile( $output );
												if( $tmp !== null )
												{
													// make sure there is no active task for this mac address
													$num = getCountOfActiveTasksWithMAC( $conn, $mac->getMACWithColon());
				
													if ( $num == 0 )
													{
														// attempt to ftp file
										
														$ftp = ftp_connect(getSetting( $conn, "FOG_TFTP_HOST" )); 
														$ftp_loginres = ftp_login($ftp, getSetting( $conn, "FOG_TFTP_FTP_USERNAME" ), getSetting( $conn, "FOG_TFTP_FTP_PASSWORD" )); 			
														if ($ftp && $ftp_loginres ) 
														{
															if ( ftp_put( $ftp, getSetting( $conn, "FOG_TFTP_PXE_CONFIG_DIR" ) . $mac->getMACImageReady(), $tmp, FTP_ASCII ) )
															{			
																$uname = "FOGScheduler";
											
																$sql = "insert into 
																		tasks(taskName, taskCreateTime, taskCheckIn, taskHostID, taskState, taskCreateBy, taskForce, taskType, taskNFSGroupID, taskNFSMemberID ) 
																		values('" . mysql_real_escape_string("Sched: " . $this->getHostName()) . "', NOW(), NOW(), '" . $this->getHostID() . "', '0', '" . $uname . "', '0', 'D', '" . $storageGroup->getID() . "', '" . $masterNode->getID() . "' )";
								
																if ( mysql_query( $sql, $conn ) )
																{
																
																	if ( trim($args1) == "1" )
																	{
																		// Remove any exists snapin tasks
																		cancelSnapinsForHost( $conn, $this->getHostID() );
									
																		// now do a clean snapin deploy
																		deploySnapinsForHost( $conn, $this->getHostID() );
																	}
								
																	// lets try to wake the computer up!
																	wakeUp( $mac->getMACWithColon() );																			
																	@ftp_close($ftp); 					
																	@unlink( $tmp );								
																	return true;
																}
																else
																{
																	ftp_delete( $ftp, getSetting( $conn, "FOG_TFTP_PXE_CONFIG_DIR" ) . $mac->getMACImageReady() ); 									
																	$reason = mysql_error($conn);
																}
															}  
															else
																$reason = "Unable to upload file."; 											
														}	
														else
															$reason = "Unable to connect to tftp server."; 	
						
														@ftp_close($ftp); 					
														@unlink( $tmp ); 							
													}
													else
														$reason = "Host is already a member of a active task.";
												}
												else
													$reason = "Failed to open tmp file.";	
											}
											else
												$reason = "Primary MAC is invalid: " . $mac->getMACWithColon();
										}
										else
											$reason = "No primary MAC address found.";
									}
									else
										$reason = "Unable to locate master node in storage group.";
								}
								else
									$reason = "Storage group has no members.";
							}
							else 
								$reason = "Storage Group is null.";
						}
						else
							$reason = "Image path is null.";
					}
					else
						$reason = "Image is null.";				
					break;
				case strtoupper(FOGCore::TASK_UNICAST_UPLOAD):
					/*
				 	*    Unicast Upload
				 	*/
				 	if ( $this->getImage() != null  )
					{
						if ( $this->getImage()->getPath() != null && strlen(trim($this->getImage()->getPath())) > 0 )
						{
							$storageGroup = $this->getImage()->getStorageGroup();
							if ( $storageGroup != null )
							{
								if ( $storageGroup->getMembers() != null && count( $storageGroup->getMembers() ) > 0  )
								{
									$masterNode = $storageGroup->getMasterNode();
									if ( $masterNode != null )
									{
										$mac = $this->getPrimaryMACAddress();							
										if ( $mac != null )
										{
											if ( $mac->isValid( ) )
											{
												$imgType = "imgType=n";
												if ( $this->getImage()->getImageType() == Image::IMAGE_TYPE_DD )
													$imgType = "imgType=dd";
												else if ( $this->getImage()->getImageType() == Image::IMAGE_TYPE_MULTIPARTITION_SINGLE_DISK )
													$imgType = "imgType=mps";
												else if ( $this->getImage()->getImageType() == Image::IMAGE_TYPE_MULTIPARTITION_MULTIDISK )
													$imgType = "imgType=mpa";												
												else
												{
													if ( $this->getOSID() == Host::OS_OTHER )
													{
														$reason = "Invalid OS type, unable to determine MBR.";
														return false;
													}

													if ( strlen( trim($this->getOSID()) ) == 0 || $this->getOSID() == Host::OS_UNKNOWN )
													{
														$reason = "Invalid OS type, you must specify an OS Type to image.";
														return false;
													}

													if ( trim($this->getOSID()) != Host::OS_WIN2000XP && trim($this->getOSID()) != Host::OS_WINVISTA && trim($this->getOSID()) != Host::OS_WIN7 )
													{
														$reason = "Unsupported OS detected in host!";
														return false;
													}				
												}
												
												$nfsroot = $masterNode->getRoot();
												if ( $nfsroot != null )
												{
													if ( endsWith( $nfsroot, "/" )  )
														$nfsroot .= "dev/";
													else 
														$nfsroot .= "/dev/";
														
													$pct = "pct=5";
													if ( is_numeric(getSetting($conn, "FOG_UPLOADRESIZEPCT") ) && getSetting($conn, "FOG_UPLOADRESIZEPCT") >= 5 && getSetting($conn, "FOG_UPLOADRESIZEPCT") < 100 )
														$pct = "pct=" . getSetting($conn, "FOG_UPLOADRESIZEPCT");
														
													$ignorepg = "0";
			
													if ( getSetting( $conn, "FOG_UPLOADIGNOREPAGEHIBER" ) )
														$ignorepg = "1";		
														
													$keymapapp = "";
													$keymap = getSetting( $conn, "FOG_KEYMAP" );
													if ( $keymap != null && $keymap != "" )
														$keymapapp = "keymap=$keymap";	
														
													$strKern = getSetting($conn, "FOG_TFTP_PXE_KERNEL" );
													if ( $this->getKernel() != "" && $this->getKernel() != null )
														$strKern = $this->getKernel();	
														
													$output = "# Created by FOG Imaging System\n\n
																  DEFAULT send\n
																  LABEL send\n
																  kernel " . $strKern . "\n
																  append initrd=" . getSetting( $conn, "FOG_PXE_BOOT_IMAGE" ) . "  root=/dev/ram0 rw ramdisk_size=" . getSetting( $conn, "FOG_KERNEL_RAMDISK_SIZE" ) . " ip=dhcp dns=" . getSetting($conn, "FOG_PXE_IMAGE_DNSADDRESS" ) . " type=up img=" .  $this->getImage()->getPath()  . " imgid=" . $this->getImage()->getID() . " mac=" . $mac->getMACWithColon() . " storage=" . $masterNode->getHostIP() . ":" . $nfsroot . " web=" . sloppyNameLookup(getSetting($conn, "FOG_WEB_HOST")) . getSetting( $conn, "FOG_WEB_ROOT" ) . " ignorepg=$ignorepg osid=" . $this->getOSID() . " $pct $imgType $keymapapp shutdown=" . ( $blShutdown ? "on" : " " ) . " loglevel=4 "  . getSetting( $conn, "FOG_KERNEL_ARGS" ) . " " . $this->getKernelArgs();																									
			 										$tmp = createPXEFile( $output );
													if( $tmp !== null )
													{ 
														$num = getCountOfActiveTasksWithMAC( $conn, $mac->getMACWithColon());
														if ( $num == 0 )
														{
															$ftp = ftp_connect(getSetting( $conn, "FOG_TFTP_HOST" )); 
															$ftp_loginres = ftp_login($ftp, getSetting( $conn, "FOG_TFTP_FTP_USERNAME" ), getSetting( $conn, "FOG_TFTP_FTP_PASSWORD" )); 			
															if ($ftp && $ftp_loginres ) 
															{
																if ( ftp_put( $ftp, getSetting( $conn, "FOG_TFTP_PXE_CONFIG_DIR" ) . $mac->getMACImageReady(), $tmp, FTP_ASCII ) )
																{			
																	$uname = "FOGScheduler";
																	$sql = "INSERT INTO 
																			tasks(taskName, taskCreateTime, taskCheckIn, taskHostID, taskState, taskCreateBy, taskForce, taskType, taskNFSGroupID, taskNFSMemberID ) 
																			VALUES('" . mysql_real_escape_string("Sched: " . $this->getHostName()) . "', NOW(), NOW(), '" . $this->getHostID() . "', '0', '" . mysql_real_escape_string( $uname ) . "', '0', 'U', '" . $storageGroup->getID() . "', '" . $masterNode->getID() . "' )";																																
						
																	if ( mysql_query( $sql, $conn ) )
																	{																
																		// lets try to wake the computer up!
																		wakeUp( $mac->getMACWithColon() );																			
																		@ftp_close($ftp); 					
																		@unlink( $tmp );								
																		return true;
																	}
																	else
																	{
																		ftp_delete( $ftp, getSetting( $conn, "FOG_TFTP_PXE_CONFIG_DIR" ) . $mac->getMACImageReady() ); 									
																		$reason = mysql_error($conn);
																																			
																	}
																}
																else
																	$reason = "Unable to upload file.";
															}
															else
																$reason = "Unable to connect to tftp server."; 	
						
															@ftp_close($ftp); 					
															@unlink( $tmp );															
														}
														else
															$reason = "Host is already a member of a active task.";
													}	
													else
														$reason = "Failed to open tmp file.";																  
												}
												else
													$reason = "Invalid NFS Root: " . $nfsroot;
											}
											else
												$reason = "Primary MAC is invalid: " . $mac->getMACWithColon();
										}
										else
											$reason = "No primary MAC address found.";											
									}
									else
										$reason = "Unable to locate master node in storage group.";									
								}
								else
									$reason = "Storage group has no members.";
							}
							else 
								$reason = "Storage Group is null.";
						}
						else
							$reason = "Image path is null.";
					}
					else
						$reason = "Image is null.";
					break;
				case strtoupper(FOGCore::TASK_WIPE):
					if ( $this->getImage() != null  )
					{
						if ( $this->getImage()->getPath() != null && strlen(trim($this->getImage()->getPath())) > 0 )
						{
							$storageGroup = $this->getImage()->getStorageGroup();
							if ( $storageGroup != null )
							{
								if ( $storageGroup->getMembers() != null && count( $storageGroup->getMembers() ) > 0  )
								{
									$masterNode = $storageGroup->getMasterNode();
									if ( $masterNode != null )
									{
										$mac = $this->getPrimaryMACAddress();							
										if ( $mac != null )
										{
											if ( $mac->isValid( ) )
											{
												if ( is_numeric($args2) )
												{
													$wipemode="wipemode=full";
													if ( trim($args2) ==  WIPE_FAST )
														$wipemode="wipemode=fast";
													else if ( trim($args2) ==  WIPE_NORMAL )
														$wipemode="wipemode=normal";
													else if ( trim($args2) ==  WIPE_FULL )	
														$wipemode="wipemode=full";
												
													$keymapapp = "";
													$keymap = getSetting( $conn, "FOG_KEYMAP" );
													if ( $keymap != null && $keymap != "" )
														$keymapapp = "keymap=$keymap";	
														
													$strKern = getSetting($conn, "FOG_TFTP_PXE_KERNEL" );
													if ( $this->getKernel() != "" && $this->getKernel() != null )
														$strKern = $this->getKernel();													
													
													$output = "# Created by FOG Imaging System\n\n
															  DEFAULT send\n
															  LABEL send\n
															  kernel " . $strKern . "\n
															  append initrd=" . getSetting( $conn, "FOG_PXE_BOOT_IMAGE" ) . "  root=/dev/ram0 rw ramdisk_size=" . getSetting( $conn, "FOG_KERNEL_RAMDISK_SIZE" ) . " ip=dhcp dns=" . getSetting($conn, "FOG_PXE_IMAGE_DNSADDRESS" ) . " mac=" . $mac->getMACWithColon() . " web=" . sloppyNameLookup( getSetting($conn, "FOG_WEB_HOST") ) . getSetting( $conn, "FOG_WEB_ROOT" ) . " osid=" . $this->getOSID() . " $wipemode mode=wipe $keymapapp shutdown=" . ( $blShutdown ? "on" : " " ) . " loglevel=4 " . getSetting( $conn, "FOG_KERNEL_ARGS" ) . " " . $this->getKernelArgs() ;												
												
													//cancelSnapinsForHost( $conn, $this->getHostID() );
													//deploySnapinsForHost( $conn, $this->getHostID(), trim($args2) );
												
													$tmp = createPXEFile( $output );
													if( $tmp !== null )
													{ 
														$num = getCountOfActiveTasksWithMAC( $conn, $mac->getMACWithColon());
														if ( $num == 0 )
														{
															$ftp = ftp_connect(getSetting( $conn, "FOG_TFTP_HOST" )); 
															$ftp_loginres = ftp_login($ftp, getSetting( $conn, "FOG_TFTP_FTP_USERNAME" ), getSetting( $conn, "FOG_TFTP_FTP_PASSWORD" )); 			
															if ($ftp && $ftp_loginres ) 
															{
																if ( ftp_put( $ftp, getSetting( $conn, "FOG_TFTP_PXE_CONFIG_DIR" ) . $mac->getMACImageReady(), $tmp, FTP_ASCII ) )
																{			
																	$uname = "FOGScheduler";
																	$sql = "INSERT INTO 
																			tasks(taskName, taskCreateTime, taskCheckIn, taskHostID, taskState, taskCreateBy, taskForce, taskType ) 
																			VALUES('" . mysql_real_escape_string("Sched: " . $this->getHostName()) . "', NOW(), NOW(), '" . $this->getHostID() . "', '0', '" . mysql_real_escape_string( $uname ) . "', '0', 'W')";																																
						
																	if ( mysql_query( $sql, $conn ) )
																	{																
																		// lets try to wake the computer up!
																		wakeUp( $mac->getMACWithColon() );																			
																		@ftp_close($ftp); 					
																		@unlink( $tmp );								
																		return true;
																	}
																	else
																	{
																		ftp_delete( $ftp, getSetting( $conn, "FOG_TFTP_PXE_CONFIG_DIR" ) . $mac->getMACImageReady() ); 									
																		$reason = mysql_error($conn);
																																			
																	}
																}
																else
																	$reason = "Unable to upload file.";
															}
															else
																$reason = "Unable to connect to tftp server."; 	
						
															@ftp_close($ftp); 					
															@unlink( $tmp );															
														}
														else
															$reason = "Host is already a member of a active task.";
													}	
													else
														$reason = "Failed to open tmp file.";												
												
													wakeUp( $mac->getMACWithColon() );
													return true;
												}
												else
													$reason = "Invalid snapin ID";
											}		
											else
												$reason = "Primary MAC is invalid: " . $mac->getMACWithColon();
										}
										else
											$reason = "No primary MAC address found.";											
									}
									else
										$reason = "Unable to locate master node in storage group.";									
								}
								else
									$reason = "Storage group has no members.";
							}
							else 
								$reason = "Storage Group is null.";
						}
						else
							$reason = "Image path is null.";
					}
					else
						$reason = "Image is null.";							
					break;
				case strtoupper(FOGCore::TASK_DEBUG):
					break;	
				case strtoupper(FOGCore::TASK_MEMTEST):
					break;	
				case strtoupper(FOGCore::TASK_TESTDISK):
					if ( $this->getImage() != null  )
					{
						if ( $this->getImage()->getPath() != null && strlen(trim($this->getImage()->getPath())) > 0 )
						{
							$storageGroup = $this->getImage()->getStorageGroup();
							if ( $storageGroup != null )
							{
								if ( $storageGroup->getMembers() != null && count( $storageGroup->getMembers() ) > 0  )
								{
									$masterNode = $storageGroup->getMasterNode();
									if ( $masterNode != null )
									{
										$mac = $this->getPrimaryMACAddress();							
										if ( $mac != null )
										{
											if ( $mac->isValid( ) )
											{
												$keymapapp = "";
												$keymap = getSetting( $conn, "FOG_KEYMAP" );
												if ( $keymap != null && $keymap != "" )
													$keymapapp = "keymap=$keymap";	
													
												$strKern = getSetting($conn, "FOG_TFTP_PXE_KERNEL" );
												if ( $this->getKernel() != "" && $this->getKernel() != null )
													$strKern = $this->getKernel();													
												
												$output = "# Created by FOG Imaging System\n\n
														  DEFAULT send\n
														  LABEL send\n
														  kernel " . $strKern . "\n
														  append initrd=" . getSetting( $conn, "FOG_PXE_BOOT_IMAGE" ) . "  root=/dev/ram0 rw ramdisk_size=" . getSetting( $conn, "FOG_KERNEL_RAMDISK_SIZE" ) . " ip=dhcp dns=" . getSetting($conn, "FOG_PXE_IMAGE_DNSADDRESS" ) . " mac=" . $mac->getMACWithColon() . " web=" . sloppyNameLookup(getSetting($conn, "FOG_WEB_HOST")) . getSetting( $conn, "FOG_WEB_ROOT" ) . " mode=badblocks $keymapapp shutdown=" . ( $blShutdown ? "on" : " " ) . " loglevel=4 " . getSetting( $conn, "FOG_KERNEL_ARGS" ) . " " . $this->getKernelArgs();

												$tmp = createPXEFile( $output );
												if( $tmp !== null )
												{ 
													$num = getCountOfActiveTasksWithMAC( $conn, $mac->getMACWithColon());
													if ( $num == 0 )
													{
														$ftp = ftp_connect(getSetting( $conn, "FOG_TFTP_HOST" )); 
														$ftp_loginres = ftp_login($ftp, getSetting( $conn, "FOG_TFTP_FTP_USERNAME" ), getSetting( $conn, "FOG_TFTP_FTP_PASSWORD" )); 			
														if ($ftp && $ftp_loginres ) 
														{
															if ( ftp_put( $ftp, getSetting( $conn, "FOG_TFTP_PXE_CONFIG_DIR" ) . $mac->getMACImageReady(), $tmp, FTP_ASCII ) )
															{			
																$uname = "FOGScheduler";
																$sql = "INSERT INTO 
																		tasks(taskName, taskCreateTime, taskCheckIn, taskHostID, taskState, taskCreateBy, taskForce, taskType ) 
																		VALUES('" . mysql_real_escape_string("Sched: " . $this->getHostName()) . "', NOW(), NOW(), '" . $this->getHostID() . "', '0', '" . mysql_real_escape_string( $uname ) . "', '0', 'T')";																																
					
																if ( mysql_query( $sql, $conn ) )
																{																
																	// lets try to wake the computer up!
																	wakeUp( $mac->getMACWithColon() );																			
																	@ftp_close($ftp); 					
																	@unlink( $tmp );								
																	return true;
																}
																else
																{
																	ftp_delete( $ftp, getSetting( $conn, "FOG_TFTP_PXE_CONFIG_DIR" ) . $mac->getMACImageReady() ); 									
																	$reason = mysql_error($conn);
																																		
																}
															}
															else
																$reason = "Unable to upload file.";
														}
														else
															$reason = "Unable to connect to tftp server."; 	
					
														@ftp_close($ftp); 					
														@unlink( $tmp );															
													}
													else
														$reason = "Host is already a member of a active task.";
												}	
												else
													$reason = "Failed to open tmp file.";												
											
												wakeUp( $mac->getMACWithColon() );
												return true;
												
											}		
											else
												$reason = "Primary MAC is invalid: " . $mac->getMACWithColon();
										}
										else
											$reason = "No primary MAC address found.";											
									}
									else
										$reason = "Unable to locate master node in storage group.";									
								}
								else
									$reason = "Storage group has no members.";
							}
							else 
								$reason = "Storage Group is null.";
						}
						else
							$reason = "Image path is null.";
					}
					else
						$reason = "Image is null.";				
					break;
				case strtoupper(FOGCore::TASK_PHOTOREC):
					break;
				case strtoupper(FOGCore::TASK_MULTICAST):
					if ( $this->getImage() != null  )
					{
						if ( $this->getImage()->getPath() != null && strlen(trim($this->getImage()->getPath())) > 0 )
						{
							$storageGroup = $this->getImage()->getStorageGroup();
							if ( $storageGroup != null )
							{
								if ( $storageGroup->getMembers() != null && count( $storageGroup->getMembers() ) > 0  )
								{
									$masterNode = $storageGroup->getMasterNode();
									if ( $masterNode != null )
									{
										$mac = $this->getPrimaryMACAddress();							
										if ( $mac != null )
										{
											if ( $mac->isValid( ) )
											{
												$port = trim($args1);
												$mcID = trim($args2);
												if ( is_numeric( $port ) && is_numeric( $mcID ) && $port > 0 && $mcID >= 0 )
												{
													$nfsroot = $masterNode->getRoot();
													if ( $nfsroot != null )
													{
														if ( ! endsWith( $nfsroot, "/" )  )
															$nfsroot .= "/";
															
														$imgType = "imgType=n";
														if ( $this->getImage()->getImageType() == Image::IMAGE_TYPE_DD )
															$imgType = "imgType=dd";
														else if ( $this->getImage()->getImageType() == Image::IMAGE_TYPE_MULTIPARTITION_SINGLE_DISK )
															$imgType = "imgType=mps";
														else if ( $this->getImage()->getImageType() == Image::IMAGE_TYPE_MULTIPARTITION_MULTIDISK )
															$imgType = "imgType=mpa";												
														else
														{
															if ( $this->getOSID() == Host::OS_OTHER )
															{
																$reason = "Invalid OS type, unable to determine MBR.";
																return false;
															}

															if ( strlen( trim($this->getOSID()) ) == 0 || $this->getOSID() == Host::OS_UNKNOWN )
															{
																$reason = "Invalid OS type, you must specify an OS Type to image.";
																return false;
															}

															if ( trim($this->getOSID()) != Host::OS_WIN2000XP && trim($this->getOSID()) != Host::OS_WINVISTA && trim($this->getOSID()) != Host::OS_WIN7 )
															{
																$reason = "Unsupported OS detected in host!";
																return false;
															}				
														}
													
														$keymapapp = "";
														$keymap = getSetting( $conn, "FOG_KEYMAP" );
														if ( $keymap != null && $keymap != "" )
															$keymapapp = "keymap=$keymap";	
														
														$strKern = getSetting($conn, "FOG_TFTP_PXE_KERNEL" );
														if ( $this->getKernel() != "" && $this->getKernel() != null )
															$strKern = $this->getKernel();	
														
													
														
														$output = "# Created by FOG Imaging System\n\n
																	  DEFAULT send\n
																	  LABEL send\n
																	  kernel " . $strKern . "\n
																	  append initrd=" . getSetting( $conn, "FOG_PXE_BOOT_IMAGE" ) . " root=/dev/ram0 rw ramdisk_size=" . getSetting( $conn, "FOG_KERNEL_RAMDISK_SIZE" ) . " ip=dhcp dns=" . getSetting($conn, "FOG_PXE_IMAGE_DNSADDRESS" ) . " type=down img=" .  $this->getImage()->getPath()  . " mc=yes port=" . $port . " storageip=" . $masterNode->getHostIP() . " storage=" . $masterNode->getHostIP() . ":" . $nfsroot . " mac=" . $mac->getMACWithColon() . " ftp=" . sloppyNameLookup(getSetting( $conn, "FOG_TFTP_HOST" )) . " web=" . sloppyNameLookup(getSetting($conn, "FOG_WEB_HOST")) . getSetting( $conn, "FOG_WEB_ROOT" ) . " osid=" . $this->getOSID() . " $mode $imgType $keymapapp shutdown=" . ( $blShutdown ? "on" : " " ) . " loglevel=4 " . getSetting( $conn, "FOG_KERNEL_ARGS" ) . " " . $this->getKernelArgs();
																						
				 										$tmp = createPXEFile( $output );
														if( $tmp !== null )
														{ 
															// make sure there is no active task for this mac address
															$num = getCountOfActiveTasksWithMAC( $conn, $mac->getMACWithColon());
				
															if ( $num == 0 )
															{
																// attempt to ftp file
										
																$ftp = ftp_connect(getSetting( $conn, "FOG_TFTP_HOST" )); 
																$ftp_loginres = ftp_login($ftp, getSetting( $conn, "FOG_TFTP_FTP_USERNAME" ), getSetting( $conn, "FOG_TFTP_FTP_PASSWORD" )); 			
																if ($ftp && $ftp_loginres ) 
																{
																	if ( ftp_put( $ftp, getSetting( $conn, "FOG_TFTP_PXE_CONFIG_DIR" ) . $mac->getMACImageReady(), $tmp, FTP_ASCII ) )
																	{			
																		$uname = "FOGScheduler";
											
																		$sql = "insert into 
																				tasks(taskName, taskCreateTime, taskCheckIn, taskHostID, taskState, taskCreateBy, taskForce, taskType, taskNFSGroupID, taskNFSMemberID ) 
																				values('" . mysql_real_escape_string("Sched: " . $this->getHostName()) . "', NOW(), NOW(), '" . $this->getHostID() . "', '0', '" . $uname . "', '0', 'D', '" . $storageGroup->getID() . "', '" . $masterNode->getID() . "' )";
								
																		if ( mysql_query( $sql, $conn ) )
																		{
																			$insertId = mysql_insert_id( $conn );
																			if ( $insertId !== null && $insertId >= 0 )
																			{
																				if ( linkTaskToMultitaskJob( $conn, $insertId, $mcID ) )
																				{

																					// Remove any exists snapin tasks
																					cancelSnapinsForHost( $conn, $this->getHostID() );
								
																					// now do a clean snapin deploy
																					deploySnapinsForHost( $conn, $this->getHostID() );

																					// lets try to wake the computer up!
																					wakeUp( $mac->getMACWithColon() );																			
																					@ftp_close($ftp); 					
																					@unlink( $tmp );
																					$reason = "OK";								
																					return true;
																				}
																				else
																					$reason = "Unable to link host task to multicast job!";
																			}
																			else
																				$reason = "Unable to obtain auto ID";
																		}
																		else
																		{
																			ftp_delete( $ftp, getSetting( $conn, "FOG_TFTP_PXE_CONFIG_DIR" ) . $mac->getMACImageReady() ); 									
																			$reason = mysql_error($conn);
																		}
																	}  
																	else
																		$reason = "Unable to upload file."; 											
																}	
																else
																	$reason = "Unable to connect to tftp server."; 	
						
																@ftp_close($ftp); 					
																@unlink( $tmp ); 							
															}
															else
																$reason = "Host is already a member of a active task.";
														}
														else
															$reason = "Failed to open tmp file.";														
													}
													else
														$reason = "Unable to determine NFS root";
												}
												else
													$reason = "Invalid port or multicast ID number";
											}		
											else
												$reason = "Primary MAC is invalid: " . $mac->getMACWithColon();																	
										}
										else
											$reason = "No primary MAC address found.";
									}
									else
										$reason = "Unable to locate master node in storage group.";
								}
								else
									$reason = "Storage group has no members.";
							}
							else 
								$reason = "Storage Group is null.";
						}
						else
							$reason = "Image path is null.";						
					}
					else
						$reason = "Image is null.";					
					break;
				case strtoupper(FOGCore::TASK_VIRUSSCAN):
					if ( $this->getImage() != null  )
					{
						if ( $this->getImage()->getPath() != null && strlen(trim($this->getImage()->getPath())) > 0 )
						{
							$storageGroup = $this->getImage()->getStorageGroup();
							if ( $storageGroup != null )
							{
								if ( $storageGroup->getMembers() != null && count( $storageGroup->getMembers() ) > 0  )
								{
									$masterNode = $storageGroup->getMasterNode();
									if ( $masterNode != null )
									{
										$mac = $this->getPrimaryMACAddress();							
										if ( $mac != null )
										{
											if ( $mac->isValid( ) )
											{
												$scanmode="avmode=s";
												if ( trim($args2) == FOG_AV_SCANQUARANTINE )
													$scanmode="avmode=q";
											
												$keymapapp = "";
												$keymap = getSetting( $conn, "FOG_KEYMAP" );
												if ( $keymap != null && $keymap != "" )
													$keymapapp = "keymap=$keymap";	
													
												$strKern = getSetting($conn, "FOG_TFTP_PXE_KERNEL" );
												if ( $this->getKernel() != "" && $this->getKernel() != null )
													$strKern = $this->getKernel();													
												
												$output = "# Created by FOG Imaging System\n\n
														  DEFAULT send\n
														  LABEL send\n
														  kernel " . $strKern . "\n
														  append initrd=" . getSetting( $conn, "FOG_PXE_BOOT_IMAGE" ) . "  root=/dev/ram0 rw ramdisk_size=" . getSetting( $conn, "FOG_KERNEL_RAMDISK_SIZE" ) . " ip=dhcp dns=" . getSetting($conn, "FOG_PXE_IMAGE_DNSADDRESS" ) . " mac=" . $mac->getMACWithColon() . " web=" . sloppyNameLookup(getSetting($conn, "FOG_WEB_HOST")) . getSetting( $conn, "FOG_WEB_ROOT" ) . " osid=" . $this->getOSID() . " $scanmode mode=clamav $keymapapp shutdown=" . ( $blShutdown ? "on" : " " ) . " loglevel=4 " . getSetting( $conn, "FOG_KERNEL_ARGS" ) . " " . $this->getKernelArgs();												
											
												$tmp = createPXEFile( $output );
												if( $tmp !== null )
												{ 
													$num = getCountOfActiveTasksWithMAC( $conn, $mac->getMACWithColon());
													if ( $num == 0 )
													{
														$ftp = ftp_connect(getSetting( $conn, "FOG_TFTP_HOST" )); 
														$ftp_loginres = ftp_login($ftp, getSetting( $conn, "FOG_TFTP_FTP_USERNAME" ), getSetting( $conn, "FOG_TFTP_FTP_PASSWORD" )); 			
														if ($ftp && $ftp_loginres ) 
														{
															if ( ftp_put( $ftp, getSetting( $conn, "FOG_TFTP_PXE_CONFIG_DIR" ) . $mac->getMACImageReady(), $tmp, FTP_ASCII ) )
															{			
																$uname = "FOGScheduler";
																$sql = "INSERT INTO 
																		tasks(taskName, taskCreateTime, taskCheckIn, taskHostID, taskState, taskCreateBy, taskForce, taskType ) 
																		VALUES('" . mysql_real_escape_string("Sched: " . $this->getHostName()) . "', NOW(), NOW(), '" . $this->getHostID() . "', '0', '" . mysql_real_escape_string( $uname ) . "', '0', 'V')";																																
					
																if ( mysql_query( $sql, $conn ) )
																{																
																	// lets try to wake the computer up!
																	wakeUp( $mac->getMACWithColon() );																			
																	@ftp_close($ftp); 					
																	@unlink( $tmp );								
																	return true;
																}
																else
																{
																	ftp_delete( $ftp, getSetting( $conn, "FOG_TFTP_PXE_CONFIG_DIR" ) . $mac->getMACImageReady() ); 									
																	$reason = mysql_error($conn);
																																		
																}
															}
															else
																$reason = "Unable to upload file.";
														}
														else
															$reason = "Unable to connect to tftp server."; 	
					
														@ftp_close($ftp); 					
														@unlink( $tmp );															
													}
													else
														$reason = "Host is already a member of a active task.";
												}	
												else
													$reason = "Failed to open tmp file.";												
											
												wakeUp( $mac->getMACWithColon() );
												return true;
												
											}		
											else
												$reason = "Primary MAC is invalid: " . $mac->getMACWithColon();
										}
										else
											$reason = "No primary MAC address found.";											
									}
									else
										$reason = "Unable to locate master node in storage group.";									
								}
								else
									$reason = "Storage group has no members.";
							}
							else 
								$reason = "Storage Group is null.";
						}
						else
							$reason = "Image path is null.";
					}
					else
						$reason = "Image is null.";				
					break;	
				case strtoupper(FOGCore::TASK_INVENTORY):
					if ( $this->getImage() != null  )
					{
						if ( $this->getImage()->getPath() != null && strlen(trim($this->getImage()->getPath())) > 0 )
						{
							$storageGroup = $this->getImage()->getStorageGroup();
							if ( $storageGroup != null )
							{
								if ( $storageGroup->getMembers() != null && count( $storageGroup->getMembers() ) > 0  )
								{
									$masterNode = $storageGroup->getMasterNode();
									if ( $masterNode != null )
									{
										$mac = $this->getPrimaryMACAddress();							
										if ( $mac != null )
										{
											if ( $mac->isValid( ) )
											{
												$keymapapp = "";
												$keymap = getSetting( $conn, "FOG_KEYMAP" );
												if ( $keymap != null && $keymap != "" )
													$keymapapp = "keymap=$keymap";	
													
												$strKern = getSetting($conn, "FOG_TFTP_PXE_KERNEL" );
												if ( $this->getKernel() != "" && $this->getKernel() != null )
													$strKern = $this->getKernel();													
												
												$output = "# Created by FOG Imaging System\n\n
														  DEFAULT send\n
														  LABEL send\n
														  kernel " . $strKern . "\n
														  append initrd=" . getSetting( $conn, "FOG_PXE_BOOT_IMAGE" ) . "  root=/dev/ram0 rw ramdisk_size=" . getSetting( $conn, "FOG_KERNEL_RAMDISK_SIZE" ) . " ip=dhcp dns=" . getSetting($conn, "FOG_PXE_IMAGE_DNSADDRESS" ) . " mac_deployed=" . $mac->getMACWithColon() . " web=" . sloppyNameLookup(getSetting($conn, "FOG_WEB_HOST")) . getSetting( $conn, "FOG_WEB_ROOT" ) . " mode=autoreg deployed=1 $keymapapp shutdown=" . ( $blShutdown ? "on" : " " ) . " loglevel=4 " . getSetting( $conn, "FOG_KERNEL_ARGS" ) . " " . $this->getKernelArgs();											
											
												$tmp = createPXEFile( $output );
												if( $tmp !== null )
												{ 
													$num = getCountOfActiveTasksWithMAC( $conn, $mac->getMACWithColon());
													if ( $num == 0 )
													{
														$ftp = ftp_connect(getSetting( $conn, "FOG_TFTP_HOST" )); 
														$ftp_loginres = ftp_login($ftp, getSetting( $conn, "FOG_TFTP_FTP_USERNAME" ), getSetting( $conn, "FOG_TFTP_FTP_PASSWORD" )); 			
														if ($ftp && $ftp_loginres ) 
														{
															if ( ftp_put( $ftp, getSetting( $conn, "FOG_TFTP_PXE_CONFIG_DIR" ) . $mac->getMACImageReady(), $tmp, FTP_ASCII ) )
															{			
																$uname = "FOGScheduler";
																$sql = "INSERT INTO 
																		tasks(taskName, taskCreateTime, taskCheckIn, taskHostID, taskState, taskCreateBy, taskForce, taskType ) 
																		VALUES('" . mysql_real_escape_string("Sched: " . $this->getHostName()) . "', NOW(), NOW(), '" . $this->getHostID() . "', '0', '" . mysql_real_escape_string( $uname ) . "', '0', 'I')";																																
					
																if ( mysql_query( $sql, $conn ) )
																{																
																	// lets try to wake the computer up!
																	wakeUp( $mac->getMACWithColon() );																			
																	@ftp_close($ftp); 					
																	@unlink( $tmp );								
																	return true;
																}
																else
																{
																	ftp_delete( $ftp, getSetting( $conn, "FOG_TFTP_PXE_CONFIG_DIR" ) . $mac->getMACImageReady() ); 									
																	$reason = mysql_error($conn);
																																		
																}
															}
															else
																$reason = "Unable to upload file.";
														}
														else
															$reason = "Unable to connect to tftp server."; 	
					
														@ftp_close($ftp); 					
														@unlink( $tmp );															
													}
													else
														$reason = "Host is already a member of a active task.";
												}	
												else
													$reason = "Failed to open tmp file.";												
											
												wakeUp( $mac->getMACWithColon() );
												return true;
												
											}		
											else
												$reason = "Primary MAC is invalid: " . $mac->getMACWithColon();
										}
										else
											$reason = "No primary MAC address found.";											
									}
									else
										$reason = "Unable to locate master node in storage group.";									
								}
								else
									$reason = "Storage group has no members.";
							}
							else 
								$reason = "Storage Group is null.";
						}
						else
							$reason = "Image path is null.";
					}
					else
						$reason = "Image is null.";					
					break;
				case strtoupper(FOGCore::TASK_PASSWORD_RESET):
					break;
				case strtoupper(FOGCore::TASK_ALL_SNAPINS):
					if ( $this->getImage() != null  )
					{
						if ( $this->getImage()->getPath() != null && strlen(trim($this->getImage()->getPath())) > 0 )
						{
							$storageGroup = $this->getImage()->getStorageGroup();
							if ( $storageGroup != null )
							{
								if ( $storageGroup->getMembers() != null && count( $storageGroup->getMembers() ) > 0  )
								{
									$masterNode = $storageGroup->getMasterNode();
									if ( $masterNode != null )
									{
										$mac = $this->getPrimaryMACAddress();							
										if ( $mac != null )
										{
											if ( $mac->isValid( ) )
											{
												cancelSnapinsForHost( $conn, $this->getHostID() );
												deploySnapinsForHost( $conn, $this->getHostID() );
												
												wakeUp( $mac->getMACWithColon() );
												return true;
											}		
											else
												$reason = "Primary MAC is invalid: " . $mac->getMACWithColon();
										}
										else
											$reason = "No primary MAC address found.";											
									}
									else
										$reason = "Unable to locate master node in storage group.";									
								}
								else
									$reason = "Storage group has no members.";
							}
							else 
								$reason = "Storage Group is null.";
						}
						else
							$reason = "Image path is null.";
					}
					else
						$reason = "Image is null.";											
					break;
				case strtoupper(FOGCore::TASK_SINGLE_SNAPIN):
					if ( $this->getImage() != null  )
					{
						if ( $this->getImage()->getPath() != null && strlen(trim($this->getImage()->getPath())) > 0 )
						{
							$storageGroup = $this->getImage()->getStorageGroup();
							if ( $storageGroup != null )
							{
								if ( $storageGroup->getMembers() != null && count( $storageGroup->getMembers() ) > 0  )
								{
									$masterNode = $storageGroup->getMasterNode();
									if ( $masterNode != null )
									{
										$mac = $this->getPrimaryMACAddress();							
										if ( $mac != null )
										{
											if ( $mac->isValid( ) )
											{
												if ( is_numeric($args2) )
												{
													//cancelSnapinsForHost( $conn, $this->getHostID() );
													deploySnapinsForHost( $conn, $this->getHostID(), trim($args2) );
												
													wakeUp( $mac->getMACWithColon() );
													return true;
												}
												else
													$reason = "Invalid snapin ID";
											}		
											else
												$reason = "Primary MAC is invalid: " . $mac->getMACWithColon();
										}
										else
											$reason = "No primary MAC address found.";											
									}
									else
										$reason = "Unable to locate master node in storage group.";									
								}
								else
									$reason = "Storage group has no members.";
							}
							else 
								$reason = "Storage Group is null.";
						}
						else
							$reason = "Image path is null.";
					}
					else
						$reason = "Image is null.";				
				
				
					break;
				case strtoupper(FOGCore::TASK_WAKE_ON_LAN):
					$mac = $this->getPrimaryMACAddress();							
					if ( $mac != null )
					{
						if ( $mac->isValid( ) )
						{
							wakeUp( $mac->getMACWithColon() );
							return true;
						}
						else
							$reason = "MAC Address is not valid.";
					}
					else
						$reason = "MAC address is null";
					break;				
				default:
					$reason = "Unknown task type: " . $tasktype;
			}
		}
		else
			$reason = "Database connection was null.";
		return false;
	}
}

?>
