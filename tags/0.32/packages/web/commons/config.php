<?php
/*
 *  FOG is a computer imaging solution.
 *  Copyright (C) 2007  Chuck Syperski & Jian Zhang
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

define( "IS_INCLUDED", true );

define( "MYSQL_HOST", "localhost" );
define( "MYSQL_DATABASE", "fog" );
define( "MYSQL_USERNAME", "root" );
define( "MYSQL_PASSWORD", "password" );

define( "FOG_VERSION", "0.32" );
define( "FOG_SCHEMA", 23);


define( "DB_TYPE", "mysql" );
define( "DB_HOST", MYSQL_HOST );
define( "DB_NAME", MYSQL_DATABASE );
define( "DB_USERNAME", MYSQL_USERNAME );
define( "DB_PASSWORD", MYSQL_PASSWORD );
define( "DB_PORT", null );

/*
 *  IMPORTANT NOTICE!
 *  -----------------
 *  In order to make updating from version to version of fog easier, we have moved
 *  most off these settings into the fog database.  The only settings which are 
 *  active are the settings above.  All settings below this message are transfered 
 *  to the fog database during schema update/installation.  To modify these 
 *  settings please use the fog management portal.
 *
 */

define( "TFTP_HOST", "10.0.0.10" );  
define( "TFTP_FTP_USERNAME", "fog" ); 
define( "TFTP_FTP_PASSWORD", "b699baa83bf42bde9824c6f51fdbe5cb" ); 
define( "TFTP_PXE_CONFIG_DIR", "/tftpboot/pxelinux.cfg/" ); 
define( "TFTP_PXE_KERNEL_DIR", "/tftpboot/fog/kernel/" );
define( "PXE_KERNEL", "fog/kernel/bzImage" ); 
define( "PXE_KERNEL_RAMDISK", 127000 ); 
define( "USE_SLOPPY_NAME_LOOKUPS", true); 
define( "MEMTEST_KERNEL", "fog/memtest/memtest" ); 
define( "PXE_IMAGE",  "fog/images/init.gz" ); 
define( "PXE_IMAGE_DNSADDRESS",  "10.0.0.10" ); 
define( "STORAGE_HOST", "10.0.0.10" ); 
define( "STORAGE_FTP_USERNAME", "fog" ); 
define( "STORAGE_FTP_PASSWORD", "b699baa83bf42bde9824c6f51fdbe5cb" ); 
define( "STORAGE_DATADIR", "/images/" ); 
define( "STORAGE_DATADIR_UPLOAD", "/images/dev/" ); 
define( "STORAGE_BANDWIDTHPATH", "/fog/status/bandwidth.php" );
define( "UPLOADRESIZEPCT", 5 ); 
define( "WEB_HOST", "10.0.0.10" ); 
define( "WEB_ROOT", "/fog/" ); 
define( "WOL_HOST", "10.0.0.10" ); 
define( "WOL_PATH", "/fog/wol/wol.php" ); 
define( "WOL_INTERFACE", "eth0" );					
define( "SNAPINDIR", "/opt/fog/snapins/" ); 
define( "QUEUESIZE", "10" ); 
define( "CHECKIN_TIMEOUT", 600 ); 
define( "USER_MINPASSLENGTH", 4 ); 
define( "USER_VALIDPASSCHARS", "1234567890ABCDEFGHIJKLMNOPQRSTUVWZXYabcdefghijklmnopqrstuvwxyz_()^!#" ); 
define( "NFS_ETH_MONITOR", "eth0" ); 
define( "UDPCAST_INTERFACE","eth0"); 
define( "UDPCAST_STARTINGPORT", 63100 ); 					// Must be an even number! recommended between 49152 to 65535
define( "FOG_MULTICAST_MAX_SESSIONS", 64 ); 
define( "FOG_JPGRAPH_VERSION", "2.3" );
define( "FOG_REPORT_DIR", "./reports/" );
define( "FOG_THEME", "blackeye/blackeye.css" );
define( "FOG_UPLOADIGNOREPAGEHIBER", true );
// Determine BASEPATH
define('BASEPATH', DetermineBasePath());





function DetermineBasePath()
{
	// Find the name of the first directory in the files path
	$FirstDirectory = rtrim(next(explode('/', dirname($_SERVER['PHP_SELF']))));
	
	if (preg_match('#fog#i', $FirstDirectory))
	{
		// If the first directory contains the word 'fog', we assume the fog installation is under a sub directory (default installation)
		return rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . $FirstDirectory;
	}
	else
	{
		// else we assume fog is not under a sub directory (virtual host)
		return rtrim($_SERVER['DOCUMENT_ROOT'], '/');
	}
}
