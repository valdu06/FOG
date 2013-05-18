<?php
/*
 *  FOG is a computer imaging solution.
 *  Copyright (C) 2011  Chuck Syperski & Jian Zhang
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
 
class Task
{
        const UPLOAD = 'u';
        const DOWNLOAD = 'd';
        const WIPE = 'w';
        const DEBUG = 'x';
        const MEMTEST = 'm';
        const TESTDISK = 't';
        const PHOTOREC = 'r';
        const MULTICAST = 'c';
        const VIRUS_SCAN = 'v';
        const INVENTORY = 'i';
        const PASSWORD_RESET = 'j';
        const ALL_SNAPINS = 's';
        const SINGLE_SNAPIN = 'l';
        const WAKEUP = 'o';

        const STATE_QUEUED = 0;
        const STATE_INPROGRESS = 1;
        const STATE_COMPLETE = 2;

	protected $id, $hostId, $state, $nfsGroupId, $nfsMemberId, $nfsFailures, $nfsLastMemberId;
	protected $name, $taskType;
	protected $createTime, $checkinTime, $scheduledStartTime;
	protected $creator; 
	protected $forced;
	protected $percent, $transferRate, $timeElapsed, $timeRemaining, $dataCopied, $taskPercentText, $taskDataTotal;
        protected $host;

        public function __construct()
        {
            
        }

        public function getHost()
        {
            return $this->host;
        }

        public function setHost($host)
        {
            $this->host = $host;
        }

        
        public function getTaskTypeString()
        {
            switch( $this->getTaskType() )
            {
                case Task::UPLOAD;
                    return "Upload";
                case Task::DOWNLOAD;
                    return "Download";
                case Task::WIPE;
                    return "Wipe";
                case Task::DEBUG;
                    return "Debug";
                case Task::MEMTEST;
                    return "Memtest";
                case Task::TESTDISK;
                    return "Testdisk";
                case Task::PHOTOREC;
                    return "PhotoRec";
                case Task::MULTICAST;
                    return "Multicast";
                case Task::VIRUS_SCAN;
                    return "Virus Scan";
                case Task::INVENTORY;
                    return "Inventory";
                case Task::PASSWORD_RESET;
                    return "Pass Reset";
                case Task::ALL_SNAPINS;
                    return "All Snapins";
                case Task::WAKEUP;
                    return "Wake up";
                case Task::SINGLE_SNAPIN;
                    return "Single Snapin";
                default:
                    return "n/a";
            }
        }

        public function getId()
        {
            return $this->id;
        }

        public function setId($id)
        {
            $this->id = $id;
        }

        public function getHostId()
        {
            return $this->hostId;
        }

        public function setHostId($hostId)
        {
            $this->hostId = $hostId;
        }

        public function getState()
        {
            return $this->state;
        }

        public function setState($state)
        {
            $this->state = $state;
        }

        public function getStateText()
        {
            switch( $this->getState() )
            {
                case Task::STATE_QUEUED:
                    return "Queued";
                case Task::STATE_INPROGRESS:
                    return "In progress";
                case Task::STATE_COMPLETE:
                    return "Complete";
                default:
                    return "unknown";
            }
        }

        public function getNfsGroupId()
        {
            return $this->nfsGroupId;
        }

        public function setNfsGroupId($nfsGroupId)
        {
            $this->nfsGroupId = $nfsGroupId;
        }

        public function getNfsMemberId()
        {
            return $this->nfsMemberId;
        }

        public function setNfsMemberId($nfsMemberId)
        {
            $this->nfsMemberId = $nfsMemberId;
        }

        public function getNfsFailures()
        {
            return $this->nfsFailures;
        }

        public function setNfsFailures($nfsFailures)
        {
            $this->nfsFailures = $nfsFailures;
        }

        public function getNfsLastMemberId()
        {
            return $this->nfsLastMemberId;
        }

        public function setNfsLastMemberId($nfsLastMemberId)
        {
            $this->nfsLastMemberId = $nfsLastMemberId;
        }

        public function getName()
        {
            return $this->name;
        }

        public function setName($name)
        {
            $this->name = $name;
        }

        public function getTaskType()
        {
            if ( $this->taskType != null )
                return strtolower( $this->taskType );
            return $this->taskType;
        }

        public function setTaskType($taskType)
        {
            $this->taskType = $taskType;
        }

        public function getCreateTime()
        {
            return $this->createTime;
        }

        public function setCreateTime($createTime)
        {
            $this->createTime = $createTime;
        }

        public function getCheckinTime()
        {
            return $this->checkinTime;
        }

        public function setCheckinTime($checkinTime)
        {
            $this->checkinTime = $checkinTime;
        }

        public function getScheduledStartTime()
        {
            return $this->scheduledStartTime;
        }

        public function setScheduledStartTime($scheduledStartTime)
        {
            $this->scheduledStartTime = $scheduledStartTime;
        }

        public function getCreator()
        {
            return $this->creator;
        }

        public function setCreator($creator)
        {
            $this->creator = $creator;
        }

        public function isForced()
        {
            return $this->forced;
        }

        public function setForced($forced)
        {
            $this->forced = $forced;
        }

	public function hasTransferData()
	{
		return $this->getPercent() != null && strlen( trim($this->getPercent() ) ) > 0 &&
		       $this->getTransferRate() != null && strlen( trim($this->getTransferRate() ) ) > 0 &&
       		       $this->getTimeElapsed() != null && strlen( trim($this->getTimeElapsed() ) ) > 0 &&
       		       $this->getTimeRemaining() != null && strlen( trim($this->getTimeRemaining() ) ) > 0 &&
       		       $this->getDataCopied() != null && strlen( trim($this->getDataCopied() ) ) > 0 &&
       		       $this->getTaskPercentText() != null && strlen( trim($this->getTaskPercentText() ) ) > 0 &&
       		       $this->getTaskDataTotal() != null && strlen( trim($this->getTaskDataTotal() ) ) > 0;
	}

        public function getPercent()
        {
            return $this->percent;
        }

        public function setPercent($percent)
        {
            $this->percent = $percent;
        }

        public function getTransferRate()
        {
            return $this->transferRate;
        }

        public function setTransferRate($transferRate)
        {
            $this->transferRate = $transferRate;
        }

        public function getTimeElapsed()
        {
            return $this->timeElapsed;
        }

        public function setTimeElapsed($timeElapsed)
        {
            $this->timeElapsed = $timeElapsed;
        }

        public function getTimeRemaining()
        {
            return $this->timeRemaining;
        }

        public function setTimeRemaining($timeRemaining)
        {
            $this->timeRemaining = $timeRemaining;
        }

        public function getDataCopied()
        {
            return $this->dataCopied;
        }

        public function setDataCopied($dataCopied)
        {
            $this->dataCopied = $dataCopied;
        }

        public function getTaskPercentText()
        {
            return $this->taskPercentText;
        }

        public function setTaskPercentText($taskPercentText)
        {
            $this->taskPercentText = $taskPercentText;
        }

        public function getTaskDataTotal()
        {
            return $this->taskDataTotal;
        }

        public function setTaskDataTotal($taskDataTotal)
        {
            $this->taskDataTotal = $taskDataTotal;
        }
}
?>
