#
#  FOG is a computer imaging solution.
#  Copyright (C) 2007  Chuck Syperski & Jian Zhang
#
#   This program is free software: you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation, either version 3 of the License, or
#    any later version.
#
#   This program is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#
#

installInitScript()
{
	echo -n "  * Installing init scripts";
	
	service ${initdMCfullname} stop >/dev/null 2>&1;
	service ${initdIRfullname} stop >/dev/null 2>&1;
	service ${initdSDfullname} stop >/dev/null 2>&1;
		
	cp -f ${initdsrc}/* ${initdpath}/
	chmod 755 ${initdpath}/${initdMCfullname}
	chkconfig ${initdMCfullname} on;
	chmod 755 ${initdpath}/${initdIRfullname}
	chkconfig ${initdIRfullname} on;	
	chmod 755 ${initdpath}/${initdSDfullname}
	chkconfig ${initdSDfullname} on;
	echo "...OK";
}

configureFOGService()
{
	echo "<?php
define( \"UPDSENDERPATH\", \"/usr/local/sbin/udp-sender\" );
define( \"MULTICASTLOGPATH\", \"/opt/fog/log/multicast.log\" );
define( \"MULTICASTDEVICEOUTPUT\", \"/dev/tty2\" );
define( \"MULTICASTSLEEPTIME\", 10 );
define( \"MULTICASTINTERFACE\", \"${interface}\" );
define( \"UDPSENDER_MAXWAIT\", null );

define( \"MYSQL_HOST\", \"${snmysqlhost}\" );
define( \"MYSQL_DATABASE\", \"fog\" );
define( \"MYSQL_USERNAME\", \"${snmysqluser}\" );
define( \"MYSQL_PASSWORD\", \"${snmysqlpass}\" );

define( \"LOGMAXSIZE\", \"1000000\" );

define( \"REPLICATORLOGPATH\", \"/opt/fog/log/fogreplicator.log\" );
define( \"REPLICATORDEVICEOUTPUT\", \"/dev/tty3\" );
define( \"REPLICATORSLEEPTIME\", 600 );
define( \"REPLICATORIFCONFIG\", \"/sbin/ifconfig\" );

define( \"SCHEDULERLOGPATH\", \"/opt/fog/log/fogscheduler.log\" );
define( \"SCHEDULERDEVICEOUTPUT\", \"/dev/tty4\" );
define( \"SCHEDULERWEBROOT\", \"${webdirdest}\" );
define( \"SCHEDULERSLEEPTIME\", 60 );
?>" > ${servicedst}/etc/config.php;

	echo -n "  * Starting FOG Multicast Management Server"; 
	service ${initdMCfullname} restart >/dev/null 2>&1;
	service ${initdMCfullname} status  >/dev/null 2>&1;
	if [ "$?" != "0" ]
	then
		echo "...Failed!";
		exit 1;	
	else
		echo "...OK";
	fi
	
	echo -n "  * Starting FOG Image Replicator Server"; 
	service ${initdIRfullname} restart >/dev/null 2>&1;
	service ${initdIRfullname} status  >/dev/null 2>&1;
	if [ "$?" != "0" ]
	then
		echo "...Failed!";
		exit 1;	
	else
		echo "...OK";
	fi	
	
	echo -n "  * Starting FOG Task Scheduler Server"; 
	${initdpath}/${initdSDfullname} stop >/dev/null 2>&1;
	${initdpath}/${initdSDfullname} start >/dev/null 2>&1;
	if [ "$?" != "0" ]
	then
		echo "...Failed!";
		exit 1;	
	else
		echo "...OK";
	fi	
}

configureNFS()
{
	echo -n "  * Setting up and starting NFS Server"; 
	
	echo "/images                        *(ro,sync,no_wdelay,insecure_locks,no_root_squash,insecure)
/images/dev                    *(rw,sync,no_wdelay,no_root_squash,insecure)" > "$nfsconfig";
	
	chkconfig nfs on;
	service nfs restart >/dev/null 2>&1;
	service nfs status  >/dev/null 2>&1;
	if [ "$?" != "0" ]
	then
		echo "...Failed!";
		exit 1;	
	else
		echo "...OK";
	fi		
}

configureSudo()
{
	echo -n "  * Setting up sudo settings";
	#ret=`cat /etc/sudoers | grep "${apacheuser} ALL=(ALL) NOPASSWD: /sbin/ether-wake"`
	#if [ "$ret" = "" ]
	#then
	#	 echo "${apacheuser} ALL=(ALL) NOPASSWD: /sbin/ether-wake" >>  "/etc/sudoers";
	#	 echo "Defaults:${apacheuser} !requiretty" >>  "/etc/sudoers";
	#fi
	echo "...OK";	
}

configureFTP()
{
	echo -n "  * Setting up and starting VSFTP Server";
	if [ -f "$ftpconfig" ]
	then
		mv "$ftpconfig" "${ftpconfig}.fogbackup";
	fi
	
	echo "anonymous_enable=NO
local_enable=YES
write_enable=YES
local_umask=022
dirmessage_enable=YES
xferlog_enable=YES
connect_from_port_20=YES
xferlog_std_format=YES
listen=YES
pam_service_name=vsftpd
userlist_enable=NO
tcp_wrappers=YES" > "$ftpconfig";

	chkconfig vsftpd on;
	service vsftpd restart >/dev/null 2>&1;
	service vsftpd status  >/dev/null 2>&1;
	if [ "$?" != "0" ] 
	then
		echo "...Failed!";
		exit 1;	
	else
		echo "...OK";
	fi	

}

configureTFTPandPXE()
{
	echo -n "  * Setting up and starting TFTP and PXE Servers";
	if [ -d "$tftpdirdst" ]
	then
		rm -rf "${tftpdirdst}.fogbackup" 2>/dev/null;
		cp -Rf "$tftpdirdst" "${tftpdirdst}.fogbackup" 2>/dev/null;
		#if [ -d "$tftpdirdst" ]
		#then
		#	echo "...Failed!";
		#	echo "  * Failed to move $tftpdirdst to ${tftpdirdst}.fogbackup";
		#	echo "  * Make sure ${tftpdirdst}.fogbackup does NOT exists.";		
		#	echo "  * If ${tftpdirdst}.fogbackup does exist delete or rename ";	
		#	echo "    it and start over and everything should work.";
		#	exit 1;
		#fi
	fi
	
	mkdir "$tftpdirdst" 2>/dev/null;
	cp -Rf $tftpdirsrc/* ${tftpdirdst}/
	
	chown -R ${username} "${tftpdirdst}";
	find "${tftpdirdst}" -type d -exec chmod 755 {} \;
	find "${tftpdirdst}" ! -type d -exec chmod 644 {} \;

	echo "DEFAULT vesamenu.c32
MENU TITLE FOG Computer Cloning Solution
MENU BACKGROUND fog/bg.png
MENU MASTER PASSWD \$1\$0123456789
\n
menu color title	1;36;44    #ffffffff #00000000 std
\n
LABEL fog.local
	localboot 0
	MENU DEFAULT
	MENU LABEL Boot from hard disk
	TEXT HELP
	Boot from the local hard drive.  
	If you are unsure, select this option.
	ENDTEXT
\n
LABEL fog.memtest
	kernel fog/memtest/memtest
	MENU LABEL Run Memtest86+
	TEXT HELP
	Run Memtest86+ on the client computer.
	ENDTEXT
\n
LABEL fog.reg
	kernel fog/kernel/bzImage
	append initrd=fog/images/init.gz root=/dev/ram0 rw ramdisk_size=127000 ip=dhcp dns=${dnsbootimage} mode=autoreg web=${ipaddress}/fog/ loglevel=4
	MENU LABEL Quick Host Registration and Inventory
	TEXT HELP
	Automatically register the client computer,
	and perform a hardware inventory.
	ENDTEXT
\n
LABEL fog.reginput
	kernel fog/kernel/bzImage
	append initrd=fog/images/init.gz root=/dev/ram0 rw ramdisk_size=127000 ip=dhcp dns=${dnsbootimage} mode=manreg web=${ipaddress}/fog/ loglevel=4
	MENU LABEL Perform Full Host Registration and Inventory
	TEXT HELP
	Perform a full host registration on the client
	computer, perform a hardware inventory, and 
	optionally image the host.
	ENDTEXT
\n
LABEL fog.quickimage
	MENU PASSWD \$1\$0123456789
	kernel fog/kernel/bzImage
	append initrd=fog/images/init.gz  root=/dev/ram0 rw ramdisk_size=127000 ip=dhcp dns=${dnsbootimage} mode=quickimage keymap= web=${ipaddress}/fog/ loglevel=4
	MENU LABEL Quick Image
	TEXT HELP
	This mode will allow you to image this host quickly with
	it's default assigned image.
	ENDTEXT	
	
LABEL fog.sysinfo
	kernel fog/kernel/bzImage
	append initrd=fog/images/init.gz  root=/dev/ram0 rw ramdisk_size=127000 ip=dhcp dns=${dnsbootimage} mode=sysinfo loglevel=4
	MENU LABEL Client System Information
	TEXT HELP
	View basic client information such as MAC address 
	and FOG compatibility.
	ENDTEXT	
\n
LABEL fog.debug
	MENU PASSWD \$1\$0123456789
	kernel fog/kernel/bzImage
	append initrd=fog/images/init.gz  root=/dev/ram0 rw ramdisk_size=127000 ip=dhcp dns=${dnsbootimage} mode=onlydebug
	MENU LABEL Debug Mode
	TEXT HELP
	Debug mode will load the boot image and load a prompt so
	you can run any commands you wish.
	ENDTEXT
\n
PROMPT 0
TIMEOUT 30\n" > "${tftpdirdst}/pxelinux.cfg/default";

	if [ -f "$tftpconfig" ]
	then
		mv "$tftpconfig" "${tftpconfig}.fogbackup";
	fi

	echo "# default: off
# description: The tftp server serves files using the trivial file transfer \
#	protocol.  The tftp protocol is often used to boot diskless \
#	workstations, download configuration files to network-aware printers, \
#	and to start the installation process for some operating systems.
service tftp
{
	socket_type		= dgram
	protocol		= udp
	wait			= yes
	user			= root
	server			= /usr/sbin/in.tftpd
	server_args		= -s /tftpboot
	disable			= no
	per_source		= 11
	cps			= 100 2
	flags			= IPv4
}" > "$tftpconfig";

	chkconfig xinetd on;
	service xinetd restart >/dev/null 2>&1;
	service xinetd status  >/dev/null 2>&1;	
	if [ "$?" != "0" ]
	then
		echo "...Failed!";
		exit 1;	
	else
		echo "...OK";	
	fi	
	
}

configureDHCP()
{
	echo -n "  * Setting up and starting DHCP Server";

	if [ -f "$dhcpconfig" ]
	then
		mv "$dhcpconfig" "${dhcpconfig}.fogbackup"
	fi
	
	networkbase=`echo "${ipaddress}" | cut -d. -f1-3`;
	network="${networkbase}.0";
	startrange="${networkbase}.10";
	endrange="${networkbase}.254";
	
	dhcptouse=$dhcpconfig;
	if [ -f "${dhcpconfigother}" ]; then
		dhcptouse=$dhcpconfigother;
	fi 
	
	echo "# DHCP Server Configuration file.
# see /usr/share/doc/dhcp*/dhcpd.conf.sample
# This file was created by FOG
use-host-decl-names on;
ddns-update-style interim;
ignore client-updates;
next-server ${ipaddress};

subnet ${network} netmask 255.255.255.0 {
        option subnet-mask              255.255.255.0;
        range dynamic-bootp ${startrange} ${endrange};
        default-lease-time 21600;
        max-lease-time 43200;
${dnsaddress}
${routeraddress} 
        filename \"pxelinux.0\";
}" > "$dhcptouse";
		
	if [ "$bldhcp" = "1" ]; then
		chkconfig dhcpd on;
		service dhcpd restart >/dev/null 2>&1
		service dhcpd status  >/dev/null 2>&1;
		if [ "$?" != "0" ]
		then
			echo "...Failed!";
			exit 1;	
		else
			echo "...OK";
		fi	
	else
		echo "...Skipped";
	fi
}

configureMinHttpd()
{
	configureHttpd;
	echo "<php die( \"This is a storage node, please do not access the web ui here!\" ); ?>" > "$webdirdest/management/index.php";
}

configureHttpd()
{
	echo -n "  * Setting up and starting Apache Web Server";
	chkconfig httpd on;
	service httpd restart >/dev/null 2>&1
	sleep 2;
	service httpd status >/dev/null 2>&1;
	ret=$?;
	if [ "$ret" != "0" ]
	then
		echo "...Failed! ($ret)";
		exit 1;	
	else
		if [ ! -d "$webdirdest" ]
		then
			mkdir "$webdirdest";
		else
			rm -Rf "$webdirdest";
			mkdir "$webdirdest";
		fi		
		
		cp -Rf $webdirsrc/* $webdirdest/
		
		echo "<?php
/*
 *  FOG  is a computer imaging solution.
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

define( \"IS_INCLUDED\", true );
define( \"TFTP_HOST\", \"${ipaddress}\" );
define( \"TFTP_FTP_USERNAME\", \"${username}\" );
define( \"TFTP_FTP_PASSWORD\", \"${password}\" );
define( \"TFTP_PXE_CONFIG_DIR\", \"/tftpboot/pxelinux.cfg/\" );
define( \"TFTP_PXE_KERNEL_DIR\", \"/tftpboot/fog/kernel/\" );
define( \"PXE_KERNEL\", \"fog/kernel/bzImage\" );
define( \"PXE_KERNEL_RAMDISK\", 127000 ); 
define( \"USE_SLOPPY_NAME_LOOKUPS\", \"1\");
define( \"MEMTEST_KERNEL\", \"fog/memtest/memtest\" );
define( \"PXE_IMAGE\",  \"fog/images/init.gz\" );
define( \"PXE_IMAGE_DNSADDRESS\",  \"${dnsbootimage}\" );
define( \"STORAGE_HOST\", \"${ipaddress}\" );
define( \"STORAGE_FTP_USERNAME\", \"${username}\" );
define( \"STORAGE_FTP_PASSWORD\", \"${password}\" );
define( \"STORAGE_DATADIR\", \"/images/\" );
define( \"STORAGE_DATADIR_UPLOAD\", \"/images/dev/\" );
define( \"STORAGE_BANDWIDTHPATH\", \"/fog/status/bandwidth.php\" );
define( \"CLONEMETHOD\", \"ntfsclone\" );  // valid values partimage, ntfsclone
define( \"UPLOADRESIZEPCT\", 5 ); 
define( \"WEB_HOST\", \"${ipaddress}\" );
define( \"WEB_ROOT\", \"/fog/\" );
define( \"WOL_HOST\", \"${ipaddress}\" ); 	
define( \"WOL_PATH\", \"/fog/wol/wol.php\" );   
define( \"WOL_INTERFACE\", \"${interface}\" );
define( \"SNAPINDIR\", \"${snapindir}/\" );
define( \"QUEUESIZE\", \"10\" );
define( \"CHECKIN_TIMEOUT\", 600 );
define( \"MYSQL_HOST\", \"localhost\" );
define( \"MYSQL_DATABASE\", \"fog\" );
define( \"MYSQL_USERNAME\", \"root\" );
define( \"MYSQL_PASSWORD\", \"\" );
define( \"DB_TYPE\", \"mysql\" );
define( \"DB_HOST\", MYSQL_HOST );
define( \"DB_NAME\", MYSQL_DATABASE );
define( \"DB_USERNAME\", MYSQL_USERNAME );
define( \"DB_PASSWORD\", MYSQL_PASSWORD );
define( \"DB_PORT\", null );
define( \"USER_MINPASSLENGTH\", 4 );
define( \"USER_VALIDPASSCHARS\", \"1234567890ABCDEFGHIJKLMNOPQRSTUVWZXYabcdefghijklmnopqrstuvwxyz_$-()^!\" );
define( \"NFS_ETH_MONITOR\", \"${interface}\" );
define(\"UDPCAST_INTERFACE\",\"${interface}\");
define(\"UDPCAST_STARTINGPORT\", 63100 ); 					// Must be an even number! recommended between 49152 to 65535
define(\"FOG_MULTICAST_MAX_SESSIONS\", 64 );	
define( \"FOG_JPGRAPH_VERSION\", \"2.3\" );
define( \"FOG_REPORT_DIR\", \"./reports/\" );
define( \"FOG_THEME\", \"blackeye/blackeye.css\" );
define( \"FOG_UPLOADIGNOREPAGEHIBER\", \"1\" );
define( \"FOG_VERSION\", \"${version}\" );
define( \"FOG_SCHEMA\", ${schemaversion} );
DEFINE('BASEPATH', rtrim(\$_SERVER['DOCUMENT_ROOT'], '/') . rtrim(WEB_ROOT, '/'));
?>" > "${webdirdest}/commons/config.php";
		
		
		chown -R ${apacheuser}:${apacheuser} "$webdirdest"
		
		if [ ! -f "$webredirect" ]
		then
			echo "<?php header('Location: ./fog/index.php');?>" > $webredirect;
		fi
		
		echo "...OK";
	fi
}

configureMySql()
{
	echo -n "  * Setting up and starting MySql";
	chkconfig mysqld on;
	service mysqld restart >/dev/null 2>&1;
	service mysqld status >/dev/null 2>&1;
	if [ "$?" != "0" ]
	then
		echo "...Failed!";
		exit 1;	
	else
		echo "...OK";
	fi	
}

installPackages()
{
	if [ "$installlang" = "1" ]
	then
		packages="$packages $langPackages"
	fi

	for x in $packages
	do
		rpm -q $x >/dev/null 2>&1
		if [ "$?" != "0" ]
		then
			echo  "  * Installing package: $x";
			yum -y install $x >/dev/null 2>&1;
		else
			echo  "  * Skipping package: $x (Already Installed)";
		fi
	done

}

confirmPackageInstallation()
{
	for x in $packages
	do
		echo -n "  * Checking package: $x";
		rpm -q $x >/dev/null 2>&1;
		if [ "$?" != "0" ]
		then
			echo "...Failed!"
			exit 1;		

		else
			echo "...OK";
		fi
	done;
}

setupFreshClam()
{
	echo  -n "  * Configuring Fresh Clam";

	if [ ! -d "${freshwebroot}" ]
	then
		mkdir "${freshwebroot}"
		ln -s "${freshdb}" "${freshwebroot}"
		chown -R ${apacheuser} "${freshwebroot}"
	fi

	dte=`date +%m-%d-%y`;
	
	cp -f "${freshconf}" "${freshconf}.backup.${dte}";
	cp -f "${freshcron}" "${freshcron}.backup.${dte}";

	if [ -f "${freshconf}.backup.${dte}" ] && [ -f "${freshcron}.backup.${dte}" ]; then
		cat "${freshconf}.backup.${dte}" | sed '/Example/d' > ${freshconf};
		cat "${freshcron}.backup.${dte}" | sed '/^FRESHCLAM_DELAY=.*$/d' > ${freshcron};

		echo "...OK";
	else
		echo "...Failed!"
	fi
}
