/****************************************************
 * * FOG Host Management - Edit - JavaScript
 *	Author:		Blackout
 *	Created:	9:34 AM 1/01/2012
 *	Revision:	$Revision$
 *	Last Update:	$LastChangedDate$
 ***/

$(function()
{
	// Bind to AD Settings checkbox
	$('#adEnabled').change(function() {
		
		if ( $(this).attr('checked') )
		{	
			if ( $('#adDomain').val() == '' && $('#adOU').val() == '' && $('#adUsername').val() == '' &&  $('#adPassword').val() == '' )
			{
				$.ajax({
					'type':		'GET',
					'url':		'ajax/host.adsettings.php',
					'cache':	false,
					'dataType':	'json',
					'success':	function(data)
					{	
						$('#adDomain').val(data['domain']);
						$('#adOU').val(data['ou']);
						$('#adUsername').val(data['user']);
						$('#adPassword').val(data['pass']);
					}
				});
			}

		}
	});
	
	// Fetch MAC Manufactors
	$('.mac-manufactor').each(function()
	{
		var $this = $(this);
		var input = $this.parent().find('input');
		var mac = (input.size() ? input.val() : $this.parent().find('.mac').html());
		$this.load('./ajax/mac-getman.php?prefix=' + mac);
	});
	
	// Remove MAC Buttons
	$('.remove-mac').unbind().live('click', function()
	{
		$(this).parent().remove();
		$('.tipsy').remove();
		
		if ($('#additionalMACsCell').find('.additionalMAC').size() == 0)
		{
			$('#additionalMACsRow').hide();
		}
		
		return false;
	});
	
	// Add MAC Buttons - TODO: Rewrite OLD CODE
	$('.add-mac').click(function()
	{
		$('#additionalMACsRow').show();
		$('#additionalMACsCell').append('<div><input class="addMac" type="text" name="additionalMACs[]" /> <span class="icon icon-remove remove-mac hand" title="Remove MAC"></span> <span class="mac-manufactor"></span></div>');
		
		HookTooltips();
		
		return false;
	});
	
	if ($('.additionalMAC').size())
	{
		$('#additionalMACsRow').show();
	}
	
	// Host Tasks - show advanced tasks on click
	$('.advanced-tasks-link').click(function(event)
	{
		$(this).parents('tr').fadeOut('fast', function()
		{
			$('#advanced-tasks').slideDown('slow');
		});
		
		event.preventDefault();
	});
});