<?php
/*
 *  FOG is a computer imaging solution.
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
 
class Printer
{
	private $id;
	private $port;
	private $infFile;
	private $model, $alias, $ip, $config;
	private $blDefault;

	function __construct( $id, $alias, $model, $port, $file, $ip, $config )
	{
		$this->id = $id;
		$this->alias = $alias;
		$this->model = $model;
		$this->port = $port;
		$this->infFile = $file;
		$this->ip = $ip;
		$this->config = $config;	
		$this->blDefault = false;
	}

	public function setDefault( $def )
	{
		$this->blDefault = $def;
	}

	public function isDefault()
	{
		return $this->blDefault;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getPort()
	{
		return $this->port;
	}

	public function getInfFile()
	{
		return $this->infFile;
	}

	public function getModel()
	{
		return $this->model;
	}

	public function getAlias()
	{
		return $this->alias;
	}

	public function getIp()
	{
		return $this->ip;
	}

	public function getConfig()
	{
		return $this->config;
	}					
}
?>
