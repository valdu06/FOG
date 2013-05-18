<?php
	
if ( IS_INCLUDED !== true ) die( _("Unable to load system configuration information.") );

if ( $currentUser != null && $currentUser->isLoggedIn() )
{
	$_SESSION["allow_ajax_snapin"] = true;
	?>
	<h2><?php echo(_('Snapin Search')); ?></h2>
	
	<input id="snapin-search" type="text" value="<?php echo(_('Search')); ?>" class="search-input" />
	
	<table width="100%" cellpadding="0" cellspacing="0" id="search-content" border="0">
		<thead>
			<tr class="header">
				<td><?php print _('Snapin Name'); ?></td>
				<td width="280"><?php print _('Description'); ?></td>
				<td width="40" align="center"><?php print _('Edit'); ?></td>
			</tr>
		</thead>
		<tbody>
		
		</tbody>
	</table>
	<?php
}