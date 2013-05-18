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
class Group implements Imageable
{
	private $intID;
	private $strName, $strDesc;
	private $arMembers;

	function __construct( $id, $groupname, $groupdesc=null )
	{
		$this->intID = $id;
		$this->strName = $groupname;
		$this->strDesc = $groupdesc;
		$this->arMembers = array();
	}
	
	function getID() { return $this->intID; }
	function getName() { return $this->strName; }
	function getDescription() { return $this->strDesc; }
	
	function addMember( $member )
	{
		$this->arMembers[] = $member;
	}
	
	function removeMember( $member )
	{
		$tmp = array();
		for( $i = 0; $i < count( $this->arMembers ); $i++ )
		{
			if ( $this->arMembers[$i] !== $member )
				$tmp[] = $this->arMembers[$i];		
		}
		$this->arMembers = $tmp;
	}
	
	function doMembersHaveUniformImages()
	{
		$members = $this->getMembers();	
		if ( $members != null && count( $members ) > 0 )
		{
			$imgid = -99999999;
			for ( $i = 0; $i < count( $members ); $i++ )
			{
				$member = $members[$i];
				if ( $member != null )
				{
					$image = $member->getImage();
					if ( $image != null )
					{
						if ( $i == 0 )
						{
							$imgid = $image->getID();
							if ( $imgid < 0 ) return false;
							if ( ! is_numeric($imgid ) ) return false;
						}
						else 
						{
							if ( $imgid != $image->getID() )
								return false;
						}
					}
					else
						return false;
				}
			}
			return true;
		}
		return false;
	}
	
	function doMembersHaveUniformOS()
	{
		$members = $this->getMembers();
		if ( $members != null && count( $members ) > 0 )
		{
			$osid = -99999999;
			for ( $i = 0; $i < count( $members ); $i++ )
			{
				$member = $members[$i];
				if ( $member != null )
				{
					if ( $i == 0 )
						$osid = $member->getOSID();
					else 
					{
						if ( $osid != $member->getOSID() )
							return false;
					}
				}
			}
			return true;
		}
		return false;
	}
	
	function getMembers() { return $this->arMembers; }
	
	function getCount() 
	{
		if ( $this->getMembers() != null )
		{
			return count( $this->getMembers() );
		}
		return 0;
	}
	
	public function startTask($conn, $tasktype, $blShutdown, $args1=null, $args2=null, $args3=null, $args4=null, $args5=null, &$reason)
	{
		$reason = "";
		if ( $conn != null  )
		{	
			switch( strtoupper($tasktype) )
			{
				case strtoupper(FOGCore::TASK_MULTICAST):
					if ( $this->getCount() > 0 )
					{
						if ( $this->doMembersHaveUniformOS() )
						{
							if ( $this->doMembersHaveUniformImages() )
							{
								$t = $this->getMembers();
								$templateHost = $t[0];
								if ( $templateHost != null )
								{
									$templateImage = $templateHost->getImage();
									if ( $templateImage != null )
									{
										$templateSG = $templateImage->getStorageGroup();
										if ( $templateSG != null )
										{
											// get port base
											$port = getMulticastPort( $conn );
											if ( $port !== -1 )
											{
												$mcId = createMulticastJob( $conn, "Scheduled Task: " . $this->getName(), $port, $templateImage->getPath(), null, $templateImage->getImageType(), $templateSG->getID() );
												if ( is_numeric( $mcId ) && $mcId >=0 )
												{
													$hosts = $this->getMembers();
													$suc = 0;
													$fail = 0;
													$hostoutput = "";
													for( $i = 0; $i < count( $hosts ); $i++ )
													{
														$host = $hosts[$i];
														if ( $host != null )
														{
															// arg1 = port
															// arg2 = job id
															$ireason;
															if ( $host->startTask($conn, $tasktype, $blShutdown, $port, $mcId, null, null, null, &$ireason) )
															{
																$suc++;
																$hostoutput .= $host->getHostName() . ": " . $ireason . "\n";
															}
															else
															{
																$fail++;
																$hostoutput .= $host->getHostName() . ": " . $ireason . "\n";
															}
														}
													}
													
													if ( $suc > 0 )
													{
														if ( activateMulticastJob( $conn, $mcId ) )
														{
															$reason = $hostoutput . "\n" . "=============================================" . "\nResult: " . $suc . " of " . ($suc + $fail) . " clients were added to the task.";
															return true;
														}
														else
															$reason = "Failed to active task!";
													}
													else
													{
														$reason = $hostoutput . "\nNo hosts were added to multicast task!";
													}
												}
												else
													$reason = "Unable to create a multicast job entry";
											}
											else
												$reason = "Unable to determine port base for multicast.";
										}
										else
											$reason = "Template storage group is null.";
									}
									else
										$reason = "Template image is null.";
								}
								else
									$reason = "Template host is null.";
							}
							else
								$reason = "Not all hosts have the same Image.";
						}
						else
							$reason = "Not all hosts have the same operating system association.";
					}
					else
						$reason = "No hosts present in group.";
					break; 	
				default:
					$reason = "Unsupported task at group level";
					break;	
			}
		}
		else
			$reason = "Database connection was null.";
		return false;		
	}
}
?>
