<?php
/*
 *  FOG a computer imaging solution.
 *
 * Updated: Blackout - 5:56 PM 30/04/2011
 *
 */

if ( IS_INCLUDED !== true ) die( _("Unable to load system configuration information.") );

?>
<h2><?php print _('All Current Hosts'); ?></h2>

<form method="POST" name="hosts" action="?node=host">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<thead>
			<tr class="header">
				<td width="25"><input type="checkbox" name="no" checked="checked" /></td>
				<td width="20"></td>
				<td><?php print _('Host Name'); ?></td>
				<td width="120"><?php print _('MAC'); ?></td>
				<td width="120"><?php print _('IP Address'); ?></td>
				<td class="c" width="40"><?php print _('Edit'); ?></td>
			</tr>
		</thead>
		<tbody>
		<?php
		$crit = '%';
		require('ajax/host.search.php');
		?>
		</tbody>
	</table>

	<div id="action-box" style="display: block">
		<input type="hidden" name="frmSub" value="1" />
		<p>
		<label for="newgroup"><?php print _('Create new group'); ?></label>
		<input type="text" name="newgroup" id="newgroup" autocomplete="off" />
		</p>
		<?php
		// Group lookup
		try
		{
			?>				
			<p class="c">OR</p>
			<label for="grp"><?php print _('Add to group'); ?></label>
			<select name="grp" id="grp"><option value="">- <?php print _('Select a group'); ?> -</option>
			<?php
			$groupMan = $core->getGroupManager( $hostMan );
			$arGroups = $groupMan->getAllGroups();
			for ($i = 0; $i < count($arGroups); $i++)
			{
				$g = $arGroups[$i];
				if ($g != null) printf('<option value="%s">%s</option>', $g->getName(), $g->getName());
			}
			?>
			</select>
			<?php
		}
		catch( Exception $e )
		{
			criticalError( $e->getMessage(), _("FOG :: Group Lookup Error!") );
		}
		?>
		<p class="c"><input type="submit" value="<?php print _("Process Group Changes"); ?>" /></p>
	</div>
</form>