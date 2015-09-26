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

// Copyright Author Dany De Bontridder danydb@aevalys.eu

/**
 * @file
 * @brief Manage the status of the document (document_state)
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
global $cn;

if ( isset($_POST['add']))
{
	if (trim ($_POST['s_value'])!="")
	{
		if ( isset($_POST['s_state']))
		{
			$cn->exec_sql('insert into document_state(s_value,s_status) values ($1,$2)',array($_POST['s_value'],'C'));
		}
		else
		{
			$cn->exec_sql('insert into document_state(s_value) values ($1)',array($_POST['s_value']));
		}
	}
}
$a_stat=$cn->get_array("select s_value,s_status from document_state order by 1");
?>

<table>
	<?php for ($i=0;$i<count($a_stat);$i++):?>

	<tr>
		<td>
			<?php echo h($a_stat[$i]['s_value'])?>
		</td>

		<td>
			<?php if ($a_stat[$i]['s_status']=='C') { echo _("Ferme l'action"); } ?>
		</td>
	</tr>
	<?php endfor;?>
</table>
<h2>Ajout d'un état</h2>
<form method="post" id='etat_add_frm' onsubmit="return confirm_box(this,'Vous confirmez ?'); ">
	<p>
		Nom de l'état <?php $value=new IText("s_value",""); echo $value->input()?>
	</p>
	<p>
		Cochez la case si cet état ferme une action <?php $state=new ICheckBox("s_state",""); echo $state->input()?>
                <input type='hidden' name='add' value='1'>
		<?php echo HtmlInput::submit("addbt", "Ajouter")?>
	</p>
</form>