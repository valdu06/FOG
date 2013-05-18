/****************************************************
 * FOG Login JS
 *	Author:		Blackout
 *	Created:	2:58 PM 20/04/2011
 *	Revision:	$Revision: 689 $
 *	Last Update:	$LastChangedDate: 2011-06-16 17:18:02 +0000 (Thu, 16 Jun 2011) $
 ***/

$(function()
{
	var ReturnIndexes = new Array('sites', 'version');
	var ResultContainers = $('#login-form-info b');
	
	$.ajax({
		'url':		'ajax/login.info.php',
		'cache':	false,
		'dataType':	'json',
		'success':	function (data)
		{
			for (i in ReturnIndexes)
			{
				var Container = ResultContainers.eq(i);
				
				if (data['error-' + ReturnIndexes[i]])
				{
					Container.html(data['error-' + ReturnIndexes[i]]);
				}
				else
				{
					Container.html(data[ReturnIndexes[i]]);
				}
			}
		},
		'error':	function()
		{
			ResultContainers.find('span').removeClass('icon-loading-grey').addClass('icon-kill').attr('title', 'Failed to connect!');
		}
	});
	
	$('#username').select().focus();
});
