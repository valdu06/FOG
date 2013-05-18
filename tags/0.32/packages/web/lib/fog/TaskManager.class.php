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
 
class TaskManager
{
	private $db;
	private $core;

	function __construct( $core, $db )
	{
		$this->db 	= $db;
		$this->core 	= $core;
	}

        function hasActiveTaskCheckedIn( $taskid )
        {
            if ( $taskid !== null && is_numeric( $taskid ) )
            {
                    if ( $this->db->executeQuery("select (UNIX_TIMESTAMP(taskCheckIn) - UNIX_TIMESTAMP(taskCreateTime) ) as diff from tasks where taskID = '" . $this->db->escape( $taskid ) . "'") )
                    {
                        while( $ar = $this->db->getNext() )
                        {
                                if ( $ar["diff"] > 2 )
                                    return true;
                        }
                    }
            }
            return false;
        }

        public function getAllActiveTasks()
        {
            if ( $this->core != null && $this->db != null )
            {
                $sql = "SELECT
                                taskID
                        FROM
                                tasks
                                INNER JOIN hosts ON (taskHostID = hostID)
                                LEFT OUTER JOIN images ON (hostImage = imageID )
                        WHERE
                                taskState in (0,1)
                        ORDER BY
                                taskState DESC, taskCreateTime, taskName";
                if ( $this->db->executeQuery($sql) )
                {
                    $taskIds = array();
                    while( $ar = $this->db->getNext() )
                    {
                        $taskIds[] = $ar['taskID'];
                    }

                    $tasks = array();
                    foreach( $taskIds as $taskId)
                    {
                        $tasks[] = $this->getActiveTaskById($taskId);
                    }
                    return $tasks;
                }
            }
            return null;
        }

	public function getActiveTaskById( $id )
	{
		if ( $this->core != null && $id !== null && is_numeric( $id ) && $id >= 0 && $this->db != null)
		{

                        $sql = "SELECT
                                        *,
                                        UNIX_TIMESTAMP(taskCreateTime) as unxcreate,
                                        UNIX_TIMESTAMP(taskCheckIn) as unxchkin
                                FROM
                                        tasks
                                WHERE
                                        taskID = '" . $this->db->escape( $id ) . "'";
                        if ( $this->db->executeQuery($sql) )
                        {
                                $task = null;
                                while( $ar = $this->db->getNext() )
                                {
                                    // it is probably always OK to load an
                                    // active task as a generic Task class
                                    $task = new Task();
                                    $task->setId($ar["taskID"]);
                                    $task->setHostId($ar["taskHostID"]);
                                    $task->setName($ar["taskName"]);
                                    $task->setState($ar["taskState"]);
                                    $task->setNfsGroupId($ar["taskNFSGroupID"]);
                                    $task->setNfsMemberId($ar["taskNFSMemberID"]);
                                    $task->setNfsFailures($ar["taskNFSFailures"]);
                                    $task->setNfsLastMemberId($ar["taskLastMemberID"]);
                                    $task->setTaskType($ar["taskType"]);
                                    $task->setCreateTime( new Date($ar["unxcreate"]) );
                                    $task->setCheckinTime(new Date($ar["unxchkin"]) );
                                    $task->setScheduledStartTime(new Date($ar['taskScheduledStartTime']) );
                                    $task->setCreator($ar["taskCreateBy"]);
                                    $task->setForced($ar["taskForce"] == "1");
                                    $task->setPercent($ar['taskPCT']);
                                    $task->setTransferRate($ar['taskBPM']);
                                    $task->setTimeElapsed($ar['taskTimeElapsed']);
                                    $task->setTimeRemaining($ar['taskTimeRemaining']);
                                    $task->setDataCopied($ar['taskDataCopied']);
                                    $task->setTaskPercentText($ar['taskPercentText']);
                                    $task->setTaskDataTotal($ar['taskDataTotal']);
                                }

                                if ( $task != null && is_numeric($task->getHostId()))
                                {
                                        $task->setHost( $this->core->getHostManager()->getHostById( $task->getHostId() ) );
                                        return $task;
                                }
                        }
		}
		return null;
	}
	
	public function getCountOfActiveTasksForHost( $host )
	{
		if ( $this->db != null && $host != null )
		{
			$sql = "SELECT 
					count(*) as cnt 
				FROM 
					tasks 
				WHERE 
					taskHostID = '" . $this->db->escape( $host->getID() ) . "' and 
					tasks.taskState in (0,1)";	
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
