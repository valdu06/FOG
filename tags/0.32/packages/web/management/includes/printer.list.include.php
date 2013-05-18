<?php

if ( IS_INCLUDED !== true ) die( _("Unable to load system configuration information.") );

if ( $currentUser != null && $currentUser->isLoggedIn() )
{
	?>
	<h2><?php echo(_('All Current Search')); ?></h2>
	
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
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
			<?php
			$crit = '%';
			require('ajax/printer.search.php');
			?>
		</tbody>
	</table>
	<?php
}