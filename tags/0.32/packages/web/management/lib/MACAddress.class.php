<?php

class MACAddress
{
	private $strMAC;

	function __construct( $strAdd )
	{
		$this->strMAC = $strAdd;
	}
	
	function getMACWithColon() 
	{ 
		return str_replace ( "-", ":", strtolower( $this->strMAC ) );
	}
	
	function getMACWithDash() 
	{ 
		return str_replace ( ":", "-", strtolower( $this->strMAC ) );
	}
	
	function getMACImageReady()
	{
		return "01-" . $this->getMACWithDash();
	}
	
	function isValid( )
	{
		return ereg( "^([0-9a-fA-F][0-9a-fA-F]:){5}([0-9a-fA-F][0-9a-fA-F])$", $this->strMAC );
	}

}

?>
