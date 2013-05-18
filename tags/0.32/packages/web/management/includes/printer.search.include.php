<?php

if ( IS_INCLUDED !== true ) die( _("Unable to load system configuration information.") );

if ( $currentUser != null && $currentUser->isLoggedIn() )
{
	$_SESSION["allow_ajax_host"] = true;
	?>
	<h2><?php echo(_('Printer Search')); ?></h2>
	
	<input id="printer-search" type="text" value="<?php echo(_('Search')); ?>" class="search-input" />
	
	<table width="100%" cellpadding="0" cellspacing="0" id="search-content" border="0">
		<thead>
			<tr class="header">
				<td><?php print _('Model'); ?></td>
				<td><?php print _('Alias'); ?></td>
				<td><?php print _('Port'); ?></td>
				<td><?php print _('INF'); ?></td>
				<td><?php print _('IP'); ?></td>
				<td class="c" width="40"><?php print _('Edit'); ?></td>
			</tr>
		</thead>
		<tbody>
		
		</tbody>
	</table>
	<?php
}