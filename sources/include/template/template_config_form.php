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
		<TD> <?php echo _('Répertoire temporaire');?> </TD>
		<TD> <?php echo $ictmp->input(); echo Icon_Action::infobulle(200);?></TD>

	</tr>
	<TR>

		<TD><?php echo _('Changement de langue');?></TD>
		<TD> <?php echo $iclocale->input();echo Icon_Action::infobulle(201)?></TD>
	</TR>
	<TR>
		<TD><?php echo _('Chemin complet vers les executable de Postgresql');?> </TD>
		<TD><?php echo $icpath->input();echo Icon_Action::infobulle(202)?></TD>
	</TR>
	<TR>
		<TD><?php echo _('Utilisateur Postgresql');?> </TD>
		<TD><?php echo $icuser->input();echo Icon_Action::infobulle(203)?></TD>
	</TR>
	<TR>
		<TD><?php echo _('Mot de passe de l\'utilisateur Postgresql');?> </TD>
		<TD><?php echo $icpasswd->input();echo Icon_Action::infobulle(204)?></TD>
</TR>
<tr>
    <td>
        <?php echo _('Administrateur de noalyss')?>
    </td>
    <td>
        <?php echo $icadmin->input();?>
    </td>
</tr>
<TR>
	<TD><?php echo _('Adresse Serveur Postgresql');?> </TD>
	<TD><?php echo $ichost->input();echo Icon_Action::infobulle(208)?></TD>
</TR>
<TR>
	<TD><?php echo _('Port de Postgresql');?> </TD>
	<TD><?php echo $icport->input();echo Icon_Action::infobulle(205)?></TD>
</TR>
<tr>
	<td><?php echo _('Mode Serveur mutualisé'). Icon_Action::infobulle(207) ?></td>
	<td><?php echo $smulti->input() ?></td>
</tr>
<tr id="div_db" style="visibility:hidden">
	<td>
		<?php echo _('Nom base de donnée');?>
	</td>
	<td>
		<?php echo $icdbname->input();echo Icon_Action::infobulle(206) ?>
	</td>
</tr>
</table>
</div>
<div class="notice">
	<?php
	if ( $os == 1 )
	{
            echo _('Attention : si vous installez sous windows n\'utilisez pas le \ mais plutôt le / dans les nom de répertoire (càd les chemins ou path)');
	}
	?>
</div>
<script>
function show_dbname(obj) {
	try {
		if (obj.checked === true)
		{
			this.document.getElementById('div_db').style.visibility= 'visible';
		}
		else {
                        this.document.getElementById('div_db').style.visibility= 'hidden';
		}
	} catch (e) {
		alert_box(e.getMessage);
	}
}
<?php 
// Show the div is MONO
if ( $smulti->selected == true) :
?>
    show_dbname($('multi'));
<?php
endif;
?>
</script>