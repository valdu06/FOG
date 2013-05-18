<?php
/*
 *  FOG - Free, Open-Source Ghost is a computer imaging solution.
 *  Copyright (C) 2007  Chuck Syperski & Jian Zhang
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
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

// Disable caching
@header("Cache-Control: no-cache");

// Require FOG Base
require_once((defined('BASEPATH') ? BASEPATH . '/commons/config.php' : '../../commons/config.php'));
require_once(BASEPATH . '/commons/init.php');

// Allow AJAX check
if (!$_SESSION['AllowAJAXTasks']) die('FOG Session Invalid');

// Variables
$data = array();

$allTasks = $core->getTaskManager()->getAllActiveTasks();
foreach( $allTasks as $task)
{

	// Reset
	$taskData = array();
	
	// Determine state
	$state = ($task->getState() == Task::STATE_QUEUED && $core->getTaskManager()->hasActiveTaskCheckedIn($task->getId()) ? 'In Line' : $task->getStateText());
	
	// Push static variables into local array
	$taskData = array('id' => $task->getId(), 'state' => $state, 'hostname' => $task->getHost()->getHostname(), 'force' => $task->isForced() ? '1' : '0', 'type' => $task->getTaskType(), 'typeName' => $task->getTaskTypeString(), 'mac' => $task->getHost()->getMAC()->getMACWithColon(), 'createTime' => $task->getCreateTime()->getLong());

	if ( $task->getName() != null )
		$taskData['name'] = trim($task->getName());
	
	if ( $task->hasTransferData() )
	{
		$taskData['percentText'] = trim($task->getTaskPercentText());
		$taskData['BPM'] = trim($task->getTransferRate());
		$taskData['timeElapsed'] = trim($task->getTimeElapsed());
		$taskData['timeRemaining'] = trim($task->getTimeRemaining());
		$taskData['dataCopied'] = trim($task->getDataCopied());
		$taskData['dataTotal'] = trim($task->getTaskDataTotal());
	}


	
	// Format variables
	$time = $taskData['createTime'];
	if ($time)
	{
		// Today
		if (date('d-m-Y', $time) == date('d-m-Y'))
		{
			//$taskData['createTime'] = 'Today, ' . date('g:i a', $time);
			$taskData['createTime'] = date('g:ia', $time);
		}
		// Yesterday
		elseif (date('d-m-Y', $time) == date("d-m-Y", strtotime("-1 day")))
		{
			$taskData['createTime'] = 'Yesterday, ' . date('g:i a', $time);
		}
		// Short date
		elseif (date('m-Y', $time) == date('m-Y'))
		{
			$taskData['createTime'] = date('dS, g:ia', $time);
		}
		// Long date
		else
		{
			$taskData['createTime'] = date('m-d-Y g:ia', $time);
		}
	}
	
	if ($taskData['BPM'])
	{
		// Convert from speed unit/min -> speed MiB/sec
		$taskData['BPM'] = (preg_match('#/min#', $taskData['BPM']) ? $taskData['BPM'] : $taskData['BPM'] . '/min');
		
		// Partimage outputs from src/shared/common.cpp: bytes, KiB, MiB, GiB, TiB
		foreach (array('MiB' => 0, 'GiB' => 1024, 'TiB' => 1024*1024) AS $unit => $multiplier)
		{
			if (preg_match('#' . $unit . '#', $taskData['BPM']))
			{
				// Remove unit/min -> recalculate to MiB/sec
				if ($taskData['BPM'] = preg_replace('#(.*) ' . $unit . '/min#U', '\\1', $taskData['BPM']))
				{
					if ($multiplier) $taskData['BPM'] = $taskData['BPM'] * $multiplier;
					
					$taskData['BPM'] = number_format($taskData['BPM'] / 60, 2) . ' MiB/sec';
				}
			}
		}
	}
	
	// Remove colons in time
	if ($taskData['timeElapsed']) $taskData['timeElapsed'] = str_replace(':', ' ', $taskData['timeElapsed']);
	if ($taskData['timeRemaining']) $taskData['timeRemaining'] = str_replace(':', ' ', $taskData['timeRemaining']);
	
	// Push into our final data array
	$data[] = $taskData;
}

// Output
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
	// AJAX request - JSON output
	print json_encode($data);
}
else
{
	// Regular request / include - HTML output
	if (count($data))
	{
		foreach ($data AS $item)
		{
			print '<tr id="host-' . $item['id'] . '" class="' . (++$i % 2 ? 'alt1' : 'alt2')  . ' ' . ($item['percentText'] ? ' with-progress' : '') . '">
			<td>' . ($item['name'] ? '<div class="task-name" title="Task: ' . $item['name'] . '">' . $item['name'] . '</div>' : '') . '<p>' . $item['hostname'] . '</p><small>' . $item['mac'] . '</small></td>
			<td align="center"><small>' . $item['createTime'] . '</small></td>
			<td align="center"><span class="icon icon-' . strtolower(str_replace(' ', '', $item['state'])) . '" title="' . $item['state'] . '"></span></td>
			<td align="center"><span class="icon icon-' . strtolower(str_replace(' ', '', $item['typeName'])) . '" title="' . $item['typeName'] . '"></span></td>
			<td align="center">' . ($item['force'] == '1' ? '<span class="icon icon-forced" title="' . _('Task forced to start') . '"></span>' : (strtolower($item['type']) == 'u' || strtolower($item['type']) == 'd' ? '<a href="?node=tasks&sub=active&forcetask=' . $item['id'] . '&mac=' . $item['mac'] . '"><span class="icon icon-force" title="' . _('Force task to start') . '"></span></a>' : '&nbsp;')) . '</td>
			<td align="center"><a href="?node=tasks&sub=active&rmtask=' . $item['id'] . '&mac=' . $item['mac'] . '"><span class="icon icon-kill" title="' . _('Cancel Task') . '"></span></a></td>
			</tr>';
			
			if ($item['percentText'])
			{
				print '<tr id="progress-' . $item['id'] . '" class="' . ($i % 2 ? 'alt1' : 'alt2')  . '"><td colspan="7" class="task-progress-td min"><div class="task-progress-fill min" style="width: ' . (600 * ($item['percentText']/100)) . 'px"></div><div class="task-progress min"><ul><li>' . $item['timeElapsed'] . ' / ' . $item['timeRemaining'] . '</li><li>' . $item['percentText'] . '%</li><li>' . $item['dataCopied'] . ' of ' . $item['dataTotal'] . ' (' . $item['BPM'] . ')</li></ul></div></td></tr>';
			}
		}
		
		?>
		<div class="fog-message-box"><?php printf('%s active task%s found', count($data), (count($data) == 1 ? '' : 's')); ?></div>
		<div class="fog-variable" id="ActiveTasksLastCount"><?php print count($data); ?></div>
		<?php
		
	}
	else
	{
		// TODO: Move to language variables
		print '<tr><td colspan="7" class="no-active-tasks">' . ($data['error'] ? (is_array($data['error']) ? '<p>' . implode('</p><p>', $data['error']) . '</p>' : $data['error']) : _('No active tasks found')) . '</td></tr>';
	}
}
