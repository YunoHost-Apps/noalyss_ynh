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
 * @brief add, modify or delete plugin
 *
 */
if ( ! defined ('ALLOWED') ) die('Appel direct ne sont pas permis');
$msg=($new==1)?_("Nouvelle extension"):_("Modification"). " ".$me_menu->value;
echo HtmlInput::title_box($msg, $ctl);
?>
<form method="POST" id="plugin_detail_frm" onsubmit="return confirm_box('plugin_detail_frm','<?php echo _("Vous confirmez");?>')">
<table>
	<tr>
		<TD><?php echo _("Label");?></td>
		<td><?php echo $me_menu->input();?></td>
	</tr>
	<tr>
		<TD><?php echo _("Code");?></td>
		<td><?php echo $me_code->input();?></td>
	</tr>
	<tr>
		<TD><?php echo _("Description");?></td>
		<td><?php echo $me_description->input();?></td>
	</tr>
	<tr>
		<TD><?php echo _("Fichier");?></td>
		<td><?php echo $me_file->input();?></td>
	</tr>
</table>
	<?php 
	if ($new ==1 )
	{
            echo HtmlInput::hidden('save_plugin',1);
		echo HtmlInput::submit("save_plugin_sbt",_("Ajouter ce plugin"));
	} else {
		$delete=new ICheckBox('delete_pl');
		echo "<p>"._("Voulez-vous effacer ce plugin ?")." ".$delete->input()."</p>";
                echo HtmlInput::hidden('mod_plugin',1);
		echo HtmlInput::submit("mod_plugin_sbt",_("Modifier ce plugin"));

	}
	?>
</form>