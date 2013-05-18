<?php
/*
 *  FOG is a computer imaging solution.
 *  Copyright (C) 2007  Chuck Syperski & Jian Zhang
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 */

// Config check
if ( IS_INCLUDED !== true ) die( _("Unable to load system configuration information.") );

// Main
if ( $currentUser != null && $currentUser->isLoggedIn() )
{
	?>
	<h2><?php print _("All Current Groups"); ?></h2>
	
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<thead>
			<tr class="header">
				<td><?php print _('Name'); ?></td>
				<td width="230"><?php print _('Description'); ?></td>
				<td width="40" align="center"><?php print _('Members'); ?></td>
				<td width="40" align="center"><?php print _('Edit'); ?></td>
			</tr>
		</thead>
		<tbody>
			<?php
			$crit = '%';
			require('./ajax/group.search.php');
			?>
		</tbody>
	</table>
	<?php
}