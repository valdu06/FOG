/****************************************************
 * FOG Host Management JS
 *	Author:		Blackout
 *	Created:	2:36 PM 8/05/2011
 *	Revision:	$Revision: 658 $
 *	Last Update:	$LastChangedDate: 2011-06-11 07:17:02 +0000 (Sat, 11 Jun 2011) $
 ***/

// TODO: Remove when old code has been rewritten
var addMacId = 0;

$(function()
{
	// Host ping
	$('.host-ping').fogPing({ 'Delay': 0, 'UpdateStatus': 0 }).removeClass('host-ping');	
	
	// TODO: Remove when old code has been rewritten
	addMacId = ($('.addMac').size() ? $('.addMac').size() : 0);
	
	// Fetch MAC Manufactors
	$('.mac-manufactor').each(function()
	{
		var $this = $(this);
		var input = $this.parent().find('input');
		var mac = (input.size() ? input.val() : $this.parent().find('.mac').html());
		$this.load('./ajax/mac-getman.php?prefix=' + mac);
	});
	
	// Remove MAC Buttons
	BindRemoveMAC();
	
	// Add MAC Buttons - TODO: Rewrite OLD CODE
	$('.add-mac').click(function()
	{
		$('#addMacsRow').show();
		
		var curId = addMacId++;
		if ( curId < 25 )
		{
			$('#cellAddMacs').append('<div><input id="addMac' + curId + '" class="addMac" type="text" name="addMac' + curId + '" /> <span class="icon icon-remove remove-mac hand" title="Remove MAC"></span> <span class="mac-manufactor"></span></div>');
		
			BindRemoveMAC();
		}
		
		$('#addMacsRow').parents('tr').show();
		
		HookTooltips();
	});
});

function BindRemoveMAC()
{
	$('.remove-mac').unbind().click(function()
	{
		$(this).parent().remove();
		
		if ($('#cellAddMacs').html().length == 0) $('#addMacsRow').hide();
		
		return false;
	});
}