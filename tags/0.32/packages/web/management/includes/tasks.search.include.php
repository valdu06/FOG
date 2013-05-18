<?php
	
if ( IS_INCLUDED !== true ) die( _("Unable to load system configuration information.") );

if ( $currentUser != null && $currentUser->isLoggedIn() )
{
	$_SESSION["allow_ajax_task"] = true;
	?>
	<h2><?php echo(_('Task Search')); ?></h2>
	
	<input id="task-search" type="text" value="<?php echo(_('Search')); ?>" class="search-input" />
		
	<form method="POST" action="<?php print $_SERVER['PHP_SELF'] . "?node=$node"; ?>">	
		<table width="100%" cellpadding="0" cellspacing="0" id="search-content" border="0">
			<thead>
				<tr class="header">
					<td><?php print _('Group Name'); ?></td>
					<td width="115">&nbsp;</td>
					<td width="55" class="c"><?php print _('Members'); ?></td>
					<td width="55" class="c"><?php print _('Deploy'); ?></td>
					<td width="55" class="c"><?php print _('Multicast'); ?></td>
					<td width="55" class="c"><?php print _('Advanced'); ?></td>
				</tr>
			</thead>
			<tbody>
			
			</tbody>
		</table>
	</form>
	<?php
}