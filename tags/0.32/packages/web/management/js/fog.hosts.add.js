/****************************************************
 * FOG Dashboard JS
 *	Author:		Blackout
 *	Created:	12:22 PM 9/05/2011
 *	Revision:	$Revision: 642 $
 *	Last Update:	$LastChangedDate: 2011-06-02 21:41:37 +0000 (Thu, 02 Jun 2011) $
 ***/

var MACLookupTimer;
var MACLookupTimeout = 1000;

$(function()
{
	MACUpdate = function()
	{
		var $this = $(this);
		
		$this.val($this.val().replace(/-/g, ':').toUpperCase());
		
		if (MACLookupTimer) clearTimeout(MACLookupTimer);
		MACLookupTimer = setTimeout(function()
		{
			$('#priMaker').load('./ajax/mac-getman.php?prefix=' + $this.val());
		}, MACLookupTimeout);
	};
	
	$('#mac').keyup(MACUpdate).blur(MACUpdate);
});