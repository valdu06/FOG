/****************************************************
 * FOG Dashboard JS
 *	Author:		Blackout
 *	Created:	1:48 PM 23/02/2011
 *	Revision:	$Revision: 664 $
 *	Last Update:	$LastChangedDate: 2011-06-13 01:00:10 +0000 (Mon, 13 Jun 2011) $
 ***/

// TODO: Merge this with $.fn.fogAjaxSearch()

var ActiveTasksContainer;
var ActiveTasksLastCount;

// Auto loader
$(function()
{
	// Show Task Container if we have items
	ActiveTasksContainer = $('#search-content');
	if (ActiveTasksContainer.find('tbody > tr').size() > 0) ActiveTasksContainer.show();

	// Hook buttons
	ActiveTasksButtonHook();
	
	// Update on load
	//ActiveTasksUpdate();
	
	// Update timer
	ActiveTasksUpdateTimerStart();
});

function ActiveTasksUpdateTimerStart()
{
	ActiveTasksUpdateTimer = setTimeout(function()
	{
		if (!ActiveTasksRequests.length)
		{
			ActiveTasksUpdate();
		}
		else
		{
			//alert('ajax requests processing, ignoring update');
		}
	}, ActiveTasksUpdateInterval);
}

function ActiveTasksUpdate()
{
	if (ActiveTasksAJAX) return;
	
	ActiveTasksAJAX = $.ajax({
		'type':		'GET',
		'url':		'ajax/tasks.active.php',
		'cache':	false,
		'dataType':	'json',
		'beforeSend':	function()
		{
			if (ActiveTasksLastCount)
			{
				Loader.fogStatusUpdate(_L['ACTIVE_TASKS_FOUND'].replace(/%1/, ActiveTasksLastCount).replace(/%2/, (ActiveTasksLastCount == 1 ? '' : 's')), { 'Class': 'loading' });
			}
			else
			{
				Loader.fogStatusUpdate(_L['ACTIVE_TASKS_LOADING'], { 'Class': 'loading' });
			}
		},
		'success':	function(data)
		{
			// Loader
			Loader.fogStatusUpdate(_L['ACTIVE_TASKS_FOUND'].replace(/%1/, data.length).replace(/%2/, (data.length == 1 ? '' : 's')));
			
			// Variables
			ActiveTasksAJAX = null;
			var tbody = $('tbody', ActiveTasksContainer);
			ActiveTasksLastCount = data.length;
			
			// Empty search table
			tbody.empty();
			
			// Do we have search results?
			if (data.length > 0)
			{
				// It is much faster for the browser to only append once, instead of $rowcount - this is why we use 2 loops instead of 1
				// Iterate data, create variable containing all new rows
				var rows = '';
				for (i in data)
				{
					rows += '<tr id="host-' + data[i]['id'] + '" class="' + (i % 2 ? 'alt2' : 'alt1')  + (data[i]['percentText'] ? ' with-progress' : '') + '"><td>' + (data[i]['name'] ? '<div class="task-name" title="Task: ' + data[i]['name'] + '">' + data[i]['name'] + '</div>' : '') + '<p>' + data[i]['hostname'] + '</p><small>' + data[i]['mac'] + '</small></td><td align="center"><small>' + data[i]['createTime'] + '</small></td><td align="center"><span class="icon icon-' + data[i]['state'].toLowerCase().replace(/ /g, '') + '" title="' + data[i]['state'] + '"></span></td><td align="center"><span class="icon icon-' + data[i]['typeName'].toLowerCase().replace(/ /g, '') + '" title="' + data[i]['typeName'] + '"></span></td><td align="center">' + (data[i]['force'] == '1' ? '<span class="icon icon-forced" title="Task forced to start"></span>' : (data[i]['type'].toLowerCase() == 'u' || data[i]['type'].toLowerCase() == 'd' ? '<a href="?node=tasks&sub=active&forcetask=' + data[i]['id'] + '&mac=' + data[i]['mac'] + '"><span class="icon icon-force" title="Force task to start"></span></a>' : '&nbsp;')) + '</td><td align="center"><a href="?node=tasks&sub=active&rmtask=' + data[i]['id'] + '&mac=' + data[i]['mac'] + '"><span class="icon icon-kill" title="Cancel task"></span></a></td></tr>';
					if (data[i]['percentText'])
					{
						rows += '<tr id="progress-' + data[i]['id'] + '" class="' + (i % 2 ? 'alt1' : 'alt2')  + '"><td colspan="7" class="task-progress-td min"><div class="task-progress-fill min" style="width: ' + (ActiveTasksContainer.width() * (data[i]['percentText']/100)) + 'px"></div><div class="task-progress"><ul><li>' + data[i]['timeElapsed'] + ' / ' + data[i]['timeRemaining'] + '</li><li>' + data[i]['percentText'] + '%</li><li>' + data[i]['dataCopied'] + ' of ' + data[i]['dataTotal'] + ' (' + data[i]['BPM'] + ')</li></ul></div></td></tr>';
					}
				}
				
				// Append rows into tbody
				tbody.append(rows);
				
				// Add data to new elements - elements should be in tbody, so we dont have to search all DOM
				// TODO: Performance test this... i think it takes a lot of time
				var tr = $('tr', tbody);
				for (i in data) tr.eq(i).data({ 'id': data[i]['id'], 'hostname': data[i]['hostname'] });
				
				// Tooltips
				HookTooltips();
				
				// Hook buttons
				ActiveTasksButtonHook();
				
				// Show results
				ActiveTasksContainer.show();
				
				// Ping hosts
				$('.ping').fogPing().removeClass('.ping');
			}
			else
			{
				// No results - hide content boxes, show nice message
				// Adjust row colours / check for empty table
				ActiveTasksTableCheck();
			}
			
			// Schedule another update
			ActiveTasksUpdateTimerStart();
		},
		'error':	function()
		{
			// Reset
			ActiveTasksAJAX = null;
			
			// Display error in loader
			Loader.fogStatusUpdate(_L['ACTIVE_TASKS_UPDATE_FAILED'], { 'Class': 'error' });
			
			// Schedule another update
			ActiveTasksUpdateTimerStart();
		}
	});
}

function ActiveTasksButtonHook()
{
	// Hook: Click: Kill Button - Legacy GET call still works if AJAX fails
	$('.icon-kill').parent().unbind('click').click(function()
	{
		var $this = $(this);
		var ID = $this.parents('tr').attr('id').replace(/^host-/, '');
		var ProgressBar = $('#progress-' + ID, ActiveTasksContainer);
		
		ActiveTasksRequests[ActiveTasksRequests.length] = $.ajax({
			'type':		'GET',
			'url':		$this.attr('href'),
			//'url':		'ajax/sleep.php',
			'beforeSend':	function()
			{
				// Loader
				$this.find('span').removeClass().addClass('loading');
				
				// Unhook this button - multiple clicks now do nothing
				$this.unbind('click').click(function() { return false; });
			},
			'success':	function(data)
			{
				// Fade row out
				$this.parents('tr').fadeOut('fast', function()
				{
					// Remove tr element
					$(this).remove();
					
					// Remove progress bar
					ProgressBar.remove();
					
					// Adjust row colours / check for empty table
					ActiveTasksTableCheck();
				});
				
				// Remove this request from our AJAX request tracking
				ActiveTasksRequests.splice(0, 1);
			},
			'error':	function()
			{
				// Re-hook buttons
				ActiveTasksButtonHook();
				
				// Remove this request from our AJAX request tracking
				ActiveTasksRequests.splice(0, 1);
			}
		});
		
		// Stop default event
		return false;
	});
	
	// Hook: Click: Force Button - Legacy GET call still works if AJAX fails
	$('.icon-force').parent().unbind('click').click(function()
	{
		var $this = $(this);
		
		ActiveTasksRequests[ActiveTasksRequests.length] = $.ajax({
			'type':		'GET',
			'url':		$this.attr('href'),
			//'url':		'ajax/sleep.php',
			'beforeSend':	function()
			{
				// Loader
				$this.find('span').removeClass().addClass('loading');
				
				// Unhook this button - multiple clicks now do nothing
				$this.unbind('click').click(function() { return false; });
			},
			'success':	function(data)
			{
				// Indicate job has been forced
				$this.parents('td').html('<span class="icon icon-forced"></span>');
				
				// Remove this request from our AJAX request tracking
				ActiveTasksRequests.splice(0, 1);
			},
			'error':	function()
			{
				// Remove this request from our AJAX request tracking
				ActiveTasksRequests.splice(0, 1);
			}
		});
		
		// Stop default event
		return false;
	});
	
	// Hook: Hover: Show Progress Bar on Active Task
	$('.with-progress').hover(function()
	{
		var id = $(this).attr('id').replace(/^host-/, '');
		var progress = $('#progress-' + id);
		
		progress.find('.min').addClass('no-min').removeClass('min').end().find('ul').show();
	}, function()
	{
		var id = $(this).attr('id').replace(/^host-/, '');
		var progress = $('#progress-' + id);
		
		progress.find('.no-min').addClass('min').removeClass('no-min').end().find('ul').hide();
	});
	
	// Hook: Hover: Show Progress Bar on Progress Bar
	$('tr[id^="progress-"]').hover(function()
	{
		$(this).find('.min').addClass('no-min').removeClass('min').end().find('ul').show();
	}, function()
	{
		$(this).find('.no-min').addClass('min').removeClass('no-min').end().find('ul').hide();
	});
}

function ActiveTasksTableCheck()
{
	// Variables
	var tbody = $('tbody', ActiveTasksContainer);
	var tbodyRows = tbody.find('tr');
	
	// If we have rows in the table
	if (tbodyRows.size() > 0)
	{
		// Adjust alt colours
		var i = 0;
		tbodyRows.each(function()
		{
			$(this).removeClass().addClass('alt' + (i++ % 2 ? '2' : '1'));
		});
	}
	// No rows in the table
	else
	{
		// Insert pretty message
		tbody.html('<tr><td colspan="7" class="no-active-tasks">' + _L['NO_ACTIVE_TASKS'] + '</td></tr>');
	}
}
