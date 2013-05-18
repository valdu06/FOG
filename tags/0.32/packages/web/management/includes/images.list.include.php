<?php
		
if ( IS_INCLUDED !== true ) die( _("Unable to load system configuration information.") );

if ( $currentUser != null && $currentUser->isLoggedIn() )
{
	$_SESSION["allow_ajax_host"] = true;
	?>
	<h2><?php print _('List All Images'); ?></h2>
	
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<thead>
			<tr class="header">
				<td><?php print _('Image Name'); ?></td>
				<td width="230"><?php print _('Description'); ?></td>
				<td width="120" align="center"><?php print _('Storage Group'); ?></td>
				<td width="40" align="center"><?php print _('Edit'); ?></td>
			</tr>
		</thead>
		<tbody>
			<?php
			$crit = '%';
			require('ajax/image.search.php');
			?>
		</tbody>
	</table>
	<?php
}