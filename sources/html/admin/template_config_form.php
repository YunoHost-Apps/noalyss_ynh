<?php

/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/* $Revision$ */

// Copyright Author Dany De Bontridder danydb@aevalys.eu

/**
 * @file
 * @brief
 *
 */
?>
<div style="margin-left: 30">
<table>
	<tr>
		<TD> Répertoire temporaire </TD>
		<TD> <?php echo $ictmp->input(); echo HtmlInput::infobulle(200);?></TD>

	</tr>
	<TR>

		<TD>Changement de langue</TD>
		<TD> <?php echo $iclocale->input();echo HtmlInput::infobulle(201)?></TD>
	</TR>
	<TR>
		<TD>Chemin complet vers les executable de Postgresql </TD>
		<TD><?php echo $icpath->input();echo HtmlInput::infobulle(202)?></TD>
	</TR>
	<TR>
		<TD>Utilisateur de la base de donnée </TD>
		<TD><?php echo $icuser->input();echo HtmlInput::infobulle(203)?></TD>
	</TR>
	<TR>
		<TD>Mot de passe de l'utilisateur </TD>
		<TD><?php echo $icpasswd->input();echo HtmlInput::infobulle(204)?></TD>
</TR>
<TR>
	<TD>Port de postgresql </TD>
	<TD><?php echo $icport->input();echo HtmlInput::infobulle(205)?></TD>
</TR>
<tr>
	<td>Mode Serveur mutualisé <?php echo HtmlInput::infobulle(207) ?></td>
	<td><?php echo $smulti->input() ?></td>
</tr>
<tr id="div_db" style="visibility:hidden">
	<td>
		Nom base de donnée
	</td>
	<td>
		<?php echo $icdbname->input();echo HtmlInput::infobulle(206) ?>
	</td>
</tr>
</table>
</div>
<div class="notice">
	<?php
	if ( $os == 1 )
	{
		?>
	Attention : si vous installez sous windows n'utilisez pas le \ mais plutôt le / dans les nom de répertoire (càd les chemins ou path)
	<?php
	}
	?>
</div>

