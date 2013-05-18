<?php
/*
 *  FOG  is a computer imaging solution.
 *  Copyright (C) 2010  Chuck Syperski & Jian Zhang
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


/**
 *  This is the poor man's ping class.  Because we run in Linux we can use
 *  TCP ports below 1024, so we did a little UDP trick to check is a host is 
 *  alive.  From our tests it seems pretty stable.  We didn't want to have to 
 *  use the system ping command because the overhead of execute().
 */

class Ping
{
	private $host;
	private $port = '445';	// Microsoft netbios port
	private $timeout;

	public function __construct( $host, $timeout=2, $port='445' )
	{
		$this->host = $host;
		$this->timeout = $timeout;
		$this->port = $port;
	}
	
	public function execute()
	{
		if ( $this->timeout > 0 && $this->host != null )
		{
			return $this->fsockopenPing();
		}
	}

	// Blackout - 7:41 AM 6/12/2011
	function fsockopenPing()
	{
		$socket = @fsockopen($this->host, $this->port, $errorCode, $errorMessage, $this->timeout);
		if ($socket)
		{
			fclose($socket);
		}
		
		//
		// Blackout - 7:41 AM 6/12/2011
		// 110 = ETIMEDOUT = Connection timed out
		// 111 = ECONNREFUSED = Connection refused
		// 112 = EHOSTDOWN = Host is down
		//
		// All error codes for all O/S's are located here: http://www.ioplex.com/~miallen/errcmp.html - also in 'man connect'
		//
		return ($errorCode === 0 || !in_array($errorCode, array(110, 111, 112)) ? true : $errorMessage);
	}
}
