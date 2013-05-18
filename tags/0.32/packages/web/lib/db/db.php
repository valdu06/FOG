<?php

$homedir	=	dirname(__FILE__) . "/";

require_once( $homedir . "/DBManager.class.php" );
require_once( $homedir . "/iDatabase.php" );

switch( DB_TYPE )
{
	case "mysql":
		define( "DB_ID", DBManager::DBTYPE_MYSQL );
		break; 
	case "mssql":
		define( "DB_ID", DBManager::DBTYPE_MSSQL );
		break; 		
	case "oracle":
		define( "DB_ID", DBManager::DBTYPE_ORACLE );
		break; 	
	default:
		define( "DB_ID", DBManager::DBTYPE_UNKNOWN );			
}

$dir = dir($homedir );
while (false !== ($filename = $dir->read())) 
{ 	
	if (substr($filename, -13) == '.db.class.php') 
	{
		require_once( $homedir . $filename ); 
	} 
} 
$dir->close(); 



?>
